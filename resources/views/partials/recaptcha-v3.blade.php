@php
    $recaptchaSiteKey = config('services.recaptcha.site_key');
@endphp

@if(!empty($recaptchaSiteKey))
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

<script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
<script>
(function() {
    const siteKey = "{{ $recaptchaSiteKey }}";
    if (!siteKey) return;

    /**
     * Checks if an HTML element is actually visible in the DOM.
     */
    function isElementVisible(el) {
        if (!el) return false;
        if (el.classList && el.classList.contains('hidden')) return false;
        try {
            const style = window.getComputedStyle(el);
            return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';
        } catch (e) {
            return false;
        }
    }

    /**
     * Updates body class to show or hide the floating reCAPTCHA badge.
     * The badge is ONLY shown on:
     *  1. The Contact Us page
     *  2. Standalone auth pages (login, register, vendor-register)
     *  3. When the Sign In modal (#login-modal) is actively open
     *  4. When the Register modal (#register-profile-modal) is actively open
     */
    window.updateRecaptchaVisibility = function() {
        if (!document.body) return;

        const isContactPage = !!(
            document.body.classList.contains('page-route-contact') ||
            document.body.classList.contains('page-contact') ||
            window.location.pathname.match(/(^|\/)contact(\/|$)/i) ||
            document.querySelector('form.conatct_from, form.contact_form')
        );

        const isAuthPage = !!(
            document.body.classList.contains('page-route-login') ||
            document.body.classList.contains('page-route-register') ||
            document.body.classList.contains('page-route-vendor-register') ||
            window.location.pathname.match(/(^|\/)(login|register|vendor-register|sign-in)(\/|$)/i)
        );

        const loginModal = document.getElementById('login-modal');
        const isLoginModalOpen = isElementVisible(loginModal);

        const registerModal = document.getElementById('register-profile-modal');
        const isRegisterModalOpen = isElementVisible(registerModal);

        if (isContactPage || isAuthPage || isLoginModalOpen || isRegisterModalOpen) {
            document.body.classList.add('recaptcha-badge-visible');
        } else {
            document.body.classList.remove('recaptcha-badge-visible');
        }
    };

    // Run visibility check immediately and on lifecycle events
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.updateRecaptchaVisibility);
    } else {
        window.updateRecaptchaVisibility();
    }
    window.addEventListener('load', window.updateRecaptchaVisibility);

    // Watch for modal DOM and attribute changes
    if (window.MutationObserver) {
        const observer = new MutationObserver(function() {
            window.updateRecaptchaVisibility();
        });

        const setupObserver = function() {
            if (!document.body) return;
            observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });

            const loginModal = document.getElementById('login-modal');
            if (loginModal) {
                observer.observe(loginModal, { attributes: true, attributeFilter: ['style', 'class'] });
            }

            const registerModal = document.getElementById('register-profile-modal');
            if (registerModal) {
                observer.observe(registerModal, { attributes: true, attributeFilter: ['style', 'class'] });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupObserver);
        } else {
            setupObserver();
        }
    }

    // Check visibility on user interactions that may toggle modals
    document.addEventListener('click', function() {
        setTimeout(window.updateRecaptchaVisibility, 50);
        setTimeout(window.updateRecaptchaVisibility, 300);
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            setTimeout(window.updateRecaptchaVisibility, 50);
        }
    });

    // Support Livewire modal events if present
    const hookLivewire = function() {
        if (window.Livewire && typeof window.Livewire.on === 'function') {
            window.Livewire.on('show-login-modal', function() {
                setTimeout(window.updateRecaptchaVisibility, 50);
            });
            window.Livewire.on('close-login-modal', function() {
                setTimeout(window.updateRecaptchaVisibility, 50);
            });
        }
    };
    if (window.Livewire) {
        hookLivewire();
    } else {
        document.addEventListener('livewire:load', hookLivewire);
    }

    /**
     * Executes reCAPTCHA v3 with the specified action and injects the token into the form.
     */
    window.executeRecaptchaForForm = function(form, actionName, onComplete) {
        const callback = typeof onComplete === 'function' ? onComplete : function() {};

        if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function') {
            console.warn('[reCAPTCHA] grecaptcha is not available or blocked.');
            callback(null);
            return;
        }

        // Safety timeout so submission doesn't hang if Google API is unreachable
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
     * Intercept standard form submit events.
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

    /**
     * Intercept direct programmatic form.submit() calls (e.g. from jQuery validate).
     */
    const originalFormSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function() {
        const form = this;
        const actionName = getRecaptchaActionForForm(form);

        if (!actionName || form.dataset.recaptchaSubmitting === 'true') {
            return originalFormSubmit.call(form);
        }

        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
        }

        window.executeRecaptchaForForm(form, actionName, function(token) {
            form.dataset.recaptchaSubmitting = 'true';
            if (submitBtn) {
                submitBtn.disabled = false;
            }
            originalFormSubmit.call(form);
        });
    };
})();
</script>
@endif
