@php
    $recaptchaSiteKey = config('services.recaptcha.site_key');
@endphp

@if(!empty($recaptchaSiteKey))
<script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
<script>
(function() {
    const siteKey = "{{ $recaptchaSiteKey }}";
    if (!siteKey) return;

    /**
     * Executes reCAPTCHA v3 with the specified action and injects the token into the form.
     */
    window.executeRecaptchaForForm = function(form, actionName, onComplete) {
        if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function') {
            console.warn('[reCAPTCHA] grecaptcha is not available or blocked.');
            if (typeof onComplete === 'function') onComplete(null);
            return;
        }

        grecaptcha.ready(function() {
            grecaptcha.execute(siteKey, { action: actionName || 'submit' })
                .then(function(token) {
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
                    if (typeof onComplete === 'function') {
                        onComplete(token);
                    }
                })
                .catch(function(err) {
                    console.error('[reCAPTCHA] execution error:', err);
                    if (typeof onComplete === 'function') {
                        onComplete(null);
                    }
                });
        });
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
     * Intercept form submissions for protected forms.
     */
    document.addEventListener('submit', function(e) {
        // If client-side validation already stopped submission, don't execute reCAPTCHA
        if (e.defaultPrevented) return;

        const form = e.target;
        if (!form || !(form instanceof HTMLFormElement)) return;

        const actionName = getRecaptchaActionForForm(form);
        if (!actionName) return;

        // If this form already has an active verified submission underway, let it proceed
        if (form.dataset.recaptchaSubmitting === 'true') {
            return;
        }

        // If browser HTML5 validation fails, let browser show standard validation UI
        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            return;
        }

        e.preventDefault();

        // Optional submit button visual state
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
