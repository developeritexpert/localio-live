@php
    $shareUrl = request()->url();
    $shareTitle = isset($page) ? $page->title : config('app.name');
    $shareDescription = isset($page) ? $page->meta_description : '';
    $componentId = 'share-' . uniqid();
@endphp

<div class="inside_sec_text inside_sec_text_2" id="{{ $componentId }}">
    <div class="sharing_icons">
        <div class="sharing_ul">
            <a aria-label="Facebook" class="fb_icon share-btn" data-platform="facebook" title="Facebook">
                <span class="svg">
                    <svg style="display:block;border-radius:999px;" focusable="false" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="15px" height="15px">
                        <path fill="currentColor"
                            d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z" />
                    </svg>
                </span>
            </a>
        </div>
        <div class="sharing_ul">
            <a aria-label="Pinterest" class="pin_icon share-btn" data-platform="pinterest" title="Pinterest">
                <span class="svg">
                    <svg style="display:block;border-radius:999px;" focusable="false" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-2 -2 35 35">
                        <path fill="currentColor"
                            d="M16.539 4.5c-6.277 0-9.442 4.5-9.442 8.253 0 2.272.86 4.293 2.705 5.046.303.125.574.005.662-.33.061-.231.205-.816.27-1.06.088-.331.053-.447-.191-.736-.532-.627-.873-1.439-.873-2.591 0-3.338 2.498-6.327 6.505-6.327 3.548 0 5.497 2.168 5.497 5.062 0 3.81-1.686 7.025-4.188 7.025-1.382 0-2.416-1.142-2.085-2.545.397-1.674 1.166-3.48 1.166-4.689 0-1.081-.581-1.983-1.782-1.983-1.413 0-2.548 1.462-2.548 3.419 0 1.247.421 2.091.421 2.091l-1.699 7.199c-.505 2.137-.076 4.755-.039 5.019.021.158.223.196.314.077.13-.17 1.813-2.247 2.384-4.324.162-.587.929-3.631.929-3.631.46.876 1.801 1.646 3.227 1.646 4.247 0 7.128-3.871 7.128-9.053.003-3.918-3.317-7.568-8.361-7.568z">
                        </path>
                    </svg>
                </span>
            </a>
        </div>
        <div class="sharing_ul">
            <a aria-label="X" class="twitter_icon share-btn" data-platform="twitter" title="Twitter">
                <span class="svg">
                    <svg width="100%" height="100%" style="display:block;border-radius:999px;" focusable="false"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                        <path fill="currentColor"
                            d="M21.751 7h3.067l-6.7 7.658L26 25.078h-6.172l-4.833-6.32-5.531 6.32h-3.07l7.167-8.19L6 7h6.328l4.37 5.777L21.75 7Zm-1.076 16.242h1.7L11.404 8.74H9.58l11.094 14.503Z">
                        </path>
                    </svg>
                </span>
            </a>
        </div>
        <div class="sharing_ul">
            <a aria-label="Copy Link" class="copy_link_icon share-btn" data-platform="copy" title="Copy Link">
                <span class="svg">
                    <svg style="display:block;border-radius:999px;" focusable="false" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-4 -4 40 40">
                        <path fill="currentColor"
                            d="M24.412 21.177c0-.36-.126-.665-.377-.917l-2.804-2.804a1.235 1.235 0 0 0-.913-.378c-.377 0-.7.144-.97.43.026.028.11.11.255.25.144.14.24.236.29.29s.117.14.2.256c.087.117.146.232.177.344.03.112.046.236.046.37 0 .36-.126.666-.377.918a1.25 1.25 0 0 1-.918.377 1.4 1.4 0 0 1-.373-.047 1.062 1.062 0 0 1-.345-.175 2.268 2.268 0 0 1-.256-.2 6.815 6.815 0 0 1-.29-.29c-.14-.142-.223-.23-.25-.254-.297.28-.445.607-.445.984 0 .36.126.664.377.916l2.778 2.79c.243.243.548.364.917.364.36 0 .665-.118.917-.35l1.982-1.97c.252-.25.378-.55.378-.9zm-9.477-9.504c0-.36-.126-.665-.377-.917l-2.777-2.79a1.235 1.235 0 0 0-.913-.378c-.35 0-.656.12-.917.364L7.967 9.92c-.254.252-.38.553-.38.903 0 .36.126.665.38.917l2.802 2.804c.242.243.547.364.916.364.377 0 .7-.14.97-.418-.026-.027-.11-.11-.255-.25s-.24-.235-.29-.29a2.675 2.675 0 0 1-.2-.255 1.052 1.052 0 0 1-.176-.344 1.396 1.396 0 0 1-.047-.37c0-.36.126-.662.377-.914.252-.252.557-.377.917-.377.136 0 .26.015.37.046.114.03.23.09.346.175.117.085.202.153.256.2.054.05.15.148.29.29.14.146.222.23.25.258.294-.278.442-.606.442-.983zM27 21.177c0 1.078-.382 1.99-1.146 2.736l-1.982 1.968c-.745.75-1.658 1.12-2.736 1.12-1.087 0-2.004-.38-2.75-1.143l-2.777-2.79c-.75-.747-1.12-1.66-1.12-2.737 0-1.106.392-2.046 1.183-2.818l-1.186-1.185c-.774.79-1.708 1.186-2.805 1.186-1.078 0-1.995-.376-2.75-1.13l-2.803-2.81C5.377 12.82 5 11.903 5 10.826c0-1.08.382-1.993 1.146-2.738L8.128 6.12C8.873 5.372 9.785 5 10.864 5c1.087 0 2.004.382 2.75 1.146l2.777 2.79c.75.747 1.12 1.66 1.12 2.737 0 1.105-.392 2.045-1.183 2.817l1.186 1.186c.774-.79 1.708-1.186 2.805-1.186 1.078 0 1.995.377 2.75 1.132l2.804 2.804c.754.755 1.13 1.672 1.13 2.75z">
                        </path>
                    </svg>
                </span>
            </a>
        </div>
        <div class="sharing_ul more_sharing_ul">
            <a class="a2a_dd" href="https://www.addtoany.com/share" style="cursor: pointer;">
                <span class="svg">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        viewBox="-.3 0 32 32" version="1.1" width="100%" height="100%"
                        style="display:block;border-radius:999px;" xml:space="preserve">
                        <g>
                            <path fill="currentColor" d="M18 14V8h-4v6H8v4h6v6h4v-6h6v-4h-6z" fill-rule="evenodd"></path>
                        </g>
                    </svg>
                </span>
            </a>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="sharingModal-{{ $componentId }}" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Share this page</h3>
            <button class="modal-close" type="button">&times;</button>
        </div>
        <div class="modal-body">
            <div class="sharing-grid">
                <a href="#" class="share-option" data-platform="facebook">
                    <div class="share-icon facebook">
                        <svg viewBox="0 0 320 512" width="20" height="20">
                            <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z" fill="white"/>
                        </svg>
                    </div>
                    <span>Facebook</span>
                </a>

                <a href="#" class="share-option" data-platform="twitter">
                    <div class="share-icon twitter">
                        <svg viewBox="0 0 32 32" width="20" height="20">
                            <path d="M21.751 7h3.067l-6.7 7.658L26 25.078h-6.172l-4.833-6.32-5.531 6.32h-3.07l7.167-8.19L6 7h6.328l4.37 5.777L21.75 7Zm-1.076 16.242h1.7L11.404 8.74H9.58l11.094 14.503Z" fill="white"/>
                        </svg>
                    </div>
                    <span>X (Twitter)</span>
                </a>

                <a href="#" class="share-option" data-platform="pinterest">
                    <div class="share-icon pinterest">
                        <svg viewBox="-2 -2 35 35" width="20" height="20">
                            <path d="M16.539 4.5c-6.277 0-9.442 4.5-9.442 8.253 0 2.272.86 4.293 2.705 5.046.303.125.574.005.662-.33.061-.231.205-.816.27-1.06.088-.331.053-.447-.191-.736-.532-.627-.873-1.439-.873-2.591 0-3.338 2.498-6.327 6.505-6.327 3.548 0 5.497 2.168 5.497 5.062 0 3.81-1.686 7.025-4.188 7.025-1.382 0-2.416-1.142-2.085-2.545.397-1.674 1.166-3.48 1.166-4.689 0-1.081-.581-1.983-1.782-1.983-1.413 0-2.548 1.462-2.548 3.419 0 1.247.421 2.091.421 2.091l-1.699 7.199c-.505 2.137-.076 4.755-.039 5.019.021.158.223.196.314.077.13-.17 1.813-2.247 2.384-4.324.162-.587.929-3.631.929-3.631.46.876 1.801 1.646 3.227 1.646 4.247 0 7.128-3.871 7.128-9.053.003-3.918-3.317-7.568-8.361-7.568z" fill="white"/>
                        </svg>
                    </div>
                    <span>Pinterest</span>
                </a>

                <a href="#" class="share-option" data-platform="whatsapp">
                    <div class="share-icon whatsapp">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.488" fill="white"/>
                        </svg>
                    </div>
                    <span>WhatsApp</span>
                </a>

                <a href="#" class="share-option" data-platform="linkedin">
                    <div class="share-icon linkedin">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="white"/>
                        </svg>
                    </div>
                    <span>LinkedIn</span>
                </a>

                <a href="#" class="share-option" data-platform="telegram">
                    <div class="share-icon telegram">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" fill="white"/>
                        </svg>
                    </div>
                    <span>Telegram</span>
                </a>

                <a href="#" class="share-option" data-platform="email">
                    <div class="share-icon email">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-.904.732-1.636 1.636-1.636h.832L12 10.37l9.532-6.546h.832c.904 0 1.636.732 1.636 1.636z" fill="white"/>
                        </svg>
                    </div>
                    <span>Email</span>
                </a>

                <a href="#" class="share-option" data-platform="copy">
                    <div class="share-icon copy">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z" fill="white"/>
                        </svg>
                    </div>
                    <span>Copy Link</span>
                </a>
            </div>
        </div>
    </div>
</div>


<script>
    (function() {
        const componentId = '{{ $componentId }}';
        const shareUrl = '{{ $shareUrl }}';
        const shareTitle = '{{ $shareTitle }}';
        const shareDescription = '{{ $shareDescription }}';

        const component = document.getElementById(componentId);
        const modal = document.getElementById('sharingModal-' + componentId);

        if (!component || !modal) return;

        // Share functions
        const shareActions = {
            facebook: () => {
                const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`;
                window.open(url, '_blank', 'width=600,height=400');
            },

            twitter: () => {
                const url = `https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareTitle)}`;
                window.open(url, '_blank', 'width=600,height=400');
            },

            pinterest: () => {
                const url = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(shareUrl)}&description=${encodeURIComponent(shareTitle)}`;
                window.open(url, '_blank', 'width=600,height=400');
            },

            whatsapp: () => {
                const url = `https://wa.me/?text=${encodeURIComponent(shareTitle + ' ' + shareUrl)}`;
                window.open(url, '_blank');
            },

            linkedin: () => {
                const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`;
                window.open(url, '_blank', 'width=600,height=400');
            },

            telegram: () => {
                const url = `https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareTitle)}`;
                window.open(url, '_blank');
            },

            email: () => {
                const subject = encodeURIComponent(shareTitle);
                const body = encodeURIComponent(`Check out this page: ${shareUrl}`);
                window.location.href = `mailto:?subject=${subject}&body=${body}`;
            },

            copy: async () => {
                try {
                    console.log('copy');
                    await navigator.clipboard.writeText(shareUrl);
                    showNotification('Link copied to clipboard!', 'success');
                } catch (err) {
                    const textArea = document.createElement('textarea');
                    textArea.value = shareUrl;
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        showNotification('Link copied to clipboard!', 'success');
                    } catch (err) {
                        showNotification('Failed to copy link', 'error');
                    }
                    document.body.removeChild(textArea);
                }
            }
        };
        function showNotification(message, type = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type,
                    title: message,
                    toast: true,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } else {
                alert(message);
            }
        }
        function openModal() {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        component.addEventListener('click', function(e) {
            e.preventDefault();
            const shareBtn = e.target.closest('.share-btn');
            if (!shareBtn) return;

            const platform = shareBtn.dataset.platform;

            if (platform === 'more') {
                openModal();
            } else if (shareActions[platform]) {
                shareActions[platform]();
            }
        });
        modal.addEventListener('click', function(e) {
            e.preventDefault();
            if (e.target === modal) {
                closeModal();
                return;
            }
            if (e.target.classList.contains('modal-close')) {
                closeModal();
                return;
            }
            const shareOption = e.target.closest('.share-option');
            if (shareOption) {
                const platform = shareOption.dataset.platform;
                if (shareActions[platform]) {
                    shareActions[platform]();
                    closeModal();
                }
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                closeModal();
            }
        });

    })();
</script>

@push('scripts')
<script>
    // Native Web Share API fallback (for mobile devices)
    window.nativeShare = function(url, title, text) {
        if (navigator.share) {
            navigator.share({
                title: title,
                text: text,
                url: url,
            }).catch((error) => console.log('Error sharing:', error));
        }
    };
</script>
@endpush
