@php
    $recaptchaSiteKey = config('services.recaptcha.site_key');
    // Determine whether current page should load reCAPTCHA immediately (contact or auth pages)
    $isContactPage = request()->is('*contact*') || request()->routeIs('*contact*');
    $isAuthPage = request()->is('*login*') || request()->routeIs('*login*') 
               || request()->is('*register*') || request()->routeIs('*register*')
               || request()->is('*vendor-register*');
    $shouldLoadImmediately = $isContactPage || $isAuthPage;
@endphp

@if(!empty($recaptchaSiteKey))
@push('styles')
<style id="recaptcha-v3-visibility-styles">
    /* 1. By default, completely hide Google reCAPTCHA floating badge on ALL pages */
    .grecaptcha-badge {
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        transition: opacity 0.25s ease, visibility 0.25s ease !important;
    }

    /* 2. Show the badge ONLY when active: on contact page, or when signin/register modal is open */
    body.page-route-contact .grecaptcha-badge,
    body.page-contact .grecaptcha-badge,
    body.page-route-login .grecaptcha-badge,
    body.page-route-register .grecaptcha-badge,
    body.page-route-vendor-register .grecaptcha-badge,
    body.recaptcha-badge-visible .grecaptcha-badge {
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        z-index: 2147483647 !important;
    }

    @media (max-width: 576px) {
        body.recaptcha-badge-visible .grecaptcha-badge {
            bottom: 8px !important;
            right: 8px !important;
            transform: scale(0.85);
            transform-origin: bottom right;
        }
    }
</style>
@endpush

@if($shouldLoadImmediately)
<script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
@endif

<script>
(function() {
    const siteKey = "{{ $recaptchaSiteKey }}";
    if (!siteKey) return;

    let recaptchaScriptLoading = false;

    /**
     * Lazily load reCAPTCHA script only when needed (e.g. modal opens or submission attempted).
     */
    window.loadRecaptchaScript = function(callback) {
        if (window.grecaptcha && typeof window.grecaptcha.ready === 'function') {
            if (typeof callback === 'function') callback();
            return;
        }

        const existingScript = document.getElementById('google-recaptcha-script');
        if (existingScript) {
            if (typeof callback === 'function') {
                existingScript.addEventListener('load', () => callback(), { once: true });
            }
            return;
        }

        if (recaptchaScriptLoading) {
            if (typeof callback === 'function') {
                const checkInterval = setInterval(function() {
                    if (window.grecaptcha && typeof window.grecaptcha.ready === 'function') {
                        clearInterval(checkInterval);
                        callback();
                    }
                }, 100);
            }
            return;
        }

        recaptchaScriptLoading = true;
        const script = document.createElement('script');
        script.id = 'google-recaptcha-script';
        script.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(siteKey);
        script.async = true;
        script.onload = function() {
            recaptchaScriptLoading = false;
            if (typeof callback === 'function') callback();
        };
        script.onerror = function() {
            recaptchaScriptLoading = false;
            console.warn('[reCAPTCHA] Failed to load Google reCAPTCHA script.');
            if (typeof callback === 'function') callback();
        };
        document.head.appendChild(script);
    };

    /**
     * Updates badge visibility based on body classes or modal state.
     */
    window.updateRecaptchaVisibility = function() {
        if (!document.body) return;
        const loginModal = document.getElementById('login-modal');
        const regModal = document.getElementById('register-profile-modal');

        const isLoginVisible = loginModal && loginModal.style.display !== 'none' && !loginModal.classList.contains('hidden');
        const isRegVisible = regModal && regModal.style.display !== 'none' && !regModal.classList.contains('hidden');

        if (isLoginVisible || isRegVisible) {
            document.body.classList.add('recaptcha-badge-visible');
            window.loadRecaptchaScript();
        } else if (!document.body.classList.contains('page-route-contact') && !document.body.classList.contains('page-contact')) {
            document.body.classList.remove('recaptcha-badge-visible');
        }
    };

    // Preload reCAPTCHA when user interacts with login/register triggers
    document.addEventListener('click', function(e) {
        const target = e.target;
        if (!target) return;
        if (
            target.closest('#open-login-btn') ||
            target.closest('.open-login-modal') ||
            target.closest('[data-bs-target="#login-modal"]') ||
            target.closest('a[href*="login"]') ||
            target.closest('a[href*="register"]') ||
            target.closest('#login-modal') ||
            target.closest('#register-profile-modal')
        ) {
            window.loadRecaptchaScript();
        }
    }, { passive: true });

    // Preload if focus lands inside login/register modal
    document.addEventListener('focusin', function(e) {
        if (e.target && e.target.closest && (e.target.closest('#login-modal') || e.target.closest('#register-profile-modal'))) {
            window.loadRecaptchaScript();
        }
    }, { passive: true });

    // Livewire integration
    if (window.Livewire && typeof window.Livewire.on === 'function') {
        window.Livewire.on('show-login-modal', function() {
            document.body.classList.add('recaptcha-badge-visible');
            window.loadRecaptchaScript();
        });
        window.Livewire.on('close-login-modal', function() {
            document.body.classList.remove('recaptcha-badge-visible');
        });
    } else {
        document.addEventListener('livewire:load', function() {
            if (window.Livewire && typeof window.Livewire.on === 'function') {
                window.Livewire.on('show-login-modal', function() {
                    document.body.classList.add('recaptcha-badge-visible');
                    window.loadRecaptchaScript();
                });
                window.Livewire.on('close-login-modal', function() {
                    document.body.classList.remove('recaptcha-badge-visible');
                });
            }
        });
    }

    /**
     * Executes reCAPTCHA v3 with the specified action and injects the token into the form.
     */
    window.executeRecaptchaForForm = function(form, actionName, onComplete) {
        const callback = typeof onComplete === 'function' ? onComplete : function() {};

        const execute = function() {
            if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function') {
                console.warn('[reCAPTCHA] grecaptcha is not available or blocked.');
                callback(null);
                return;
            }

            let executed = false;
            const timeoutId = setTimeout(function() {
                if (!executed) {
                    executed = true;
                    console.warn('[reCAPTCHA] execution timed out.');
                    callback(null);
                }
            }, 3000);

            try {
                grecaptcha.ready(function() {
                    grecaptcha.execute(siteKey, { action: actionName || 'submit' })
                        .then(function(token) {
                            if (executed) return;
                            executed = true;
                            clearTimeout(timeoutId);

                            if (form) {
                                let tokenInput = form.querySelector('input[name="g-recaptcha-response"]');
                                if (!tokenInput) {
                                    tokenInput = document.createElement('input');
                                    tokenInput.type = 'hidden';
                                    tokenInput.name = 'g-recaptcha-response';
                                    form.appendChild(tokenInput);
                                }
                                tokenInput.value = token;
                            }
                            callback(token);
                        })
                        .catch(function(err) {
                            if (executed) return;
                            executed = true;
                            clearTimeout(timeoutId);
                            console.error('[reCAPTCHA] execution error:', err);
                            callback(null);
                        });
                });
            } catch (e) {
                if (!executed) {
                    executed = true;
                    clearTimeout(timeoutId);
                    console.error('[reCAPTCHA] unexpected error:', e);
                    callback(null);
                }
            }
        };

        if (!window.grecaptcha) {
            window.loadRecaptchaScript(execute);
        } else {
            execute();
        }
    };

    /**
     * Detect matching protected form action: 'login', 'register', or 'contact'.
     */
    function getRecaptchaActionForForm(form) {
        if (!form || !(form instanceof HTMLFormElement)) return null;

        const actionAttr = (form.getAttribute('action') || '').toLowerCase();
        const formId = (form.id || '').toLowerCase();
        const classList = form.className || '';

        // Login forms
        if (classList.includes('login_form') || actionAttr.includes('loginprocc') || formId.includes('login')) {
            return 'login';
        }

        // Register forms
        if (
            classList.includes('register_form') ||
            actionAttr.includes('register-process') ||
            actionAttr.includes('sign-in/details') ||
            actionAttr.includes('vendor-register') ||
            formId.includes('register')
        ) {
            return 'register';
        }

        // Contact forms
        if (
            classList.includes('conatct_from') ||
            classList.includes('contact_form') ||
            actionAttr.includes('query-form-submit') ||
            actionAttr.includes('contact-form-submit') ||
            formId.includes('contact')
        ) {
            return 'contact';
        }

        return null;
    }

    /**
     * Intercept standard form submit events cleanly.
     */
    document.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;

        const form = e.target;
        if (!form || !(form instanceof HTMLFormElement)) return;

        const actionName = getRecaptchaActionForForm(form);
        if (!actionName) return;

        if (form.dataset.recaptchaSubmitting === 'true') {
            return;
        }

        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            return;
        }

        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
        }

        window.executeRecaptchaForForm(form, actionName, function(token) {
            form.dataset.recaptchaSubmitting = 'true';
            if (submitBtn) {
                submitBtn.disabled = false;
            }
            form.submit();
        });
    }, false);
})();
</script>
@endif
