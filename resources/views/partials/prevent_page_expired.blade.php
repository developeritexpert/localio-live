<script>
    (function () {
        function refreshCsrfToken() {
            var refreshUrl = '{{ url("/refresh-csrf") }}';
            return fetch(refreshUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Failed to refresh CSRF token');
            })
            .then(function(data) {
                if (data && data.token) {
                    // Update meta tag
                    var metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        metaToken.setAttribute('content', data.token);
                    }
                    // Update any form inputs
                    document.querySelectorAll('input[name="_token"]').forEach(function(input) {
                        input.value = data.token;
                    });
                    if (window.Laravel) {
                        window.Laravel.csrfToken = data.token;
                    }
                    return data.token;
                }
            })
            .catch(function(err) {
                console.warn('CSRF refresh warning:', err);
            });
        }

        // 1. Keep-alive heartbeat: refresh token every 10 minutes
        setInterval(refreshCsrfToken, 10 * 60 * 1000);

        // 2. Refresh token when user returns to active tab after sleep/idle
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                refreshCsrfToken();
            }
        });

        // 3. Prevent Livewire 3 "This page has expired" popup dialog
        function setupLivewireExpiredHandler() {
            if (window.Livewire) {
                if (typeof window.Livewire.onPageExpired === 'function') {
                    window.Livewire.onPageExpired(function (response, message) {
                        if (response && typeof response.preventDefault === 'function') {
                            response.preventDefault();
                        }
                        refreshCsrfToken().then(function (newToken) {
                            if (!newToken) {
                                window.location.reload();
                            }
                        }).catch(function () {
                            window.location.reload();
                        });
                        return false;
                    });
                }
            }
        }

        document.addEventListener('livewire:init', setupLivewireExpiredHandler);
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setupLivewireExpiredHandler();
        } else {
            document.addEventListener('DOMContentLoaded', setupLivewireExpiredHandler);
        }
    })();
</script>
