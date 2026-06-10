class UserTracker {
    constructor() {
        this.userId = window.userId || null;
        this.sessionId = this.getSessionId();
        this.startTime = Date.now();
        this.currentPage = {
            productId: null,
            categoryId: null,
            businessId: null
        };
        this.hasTrackedView = false;
        this.init();
    }

    getSessionId() {
        let sessionId = localStorage.getItem('session_id');
        if (!sessionId) {
            sessionId = 'sess_' + Math.random().toString(36).substr(2, 9) + Date.now();
            localStorage.setItem('session_id', sessionId);
        }
        return sessionId;
    }

    init() {
        // Set current page context
        this.currentPage = {
            productId: this.getProductId(),
            categoryId: this.getCategoryId(),
            businessId: this.getBusinessId()
        };

        // Only track if we're on a relevant page
        if (this.isRelevantPage()) {
            this.trackPageView();
            this.trackTimeSpent();
        }
        
        // Always track clicks regardless of page
        this.trackClicks();
    }

    isRelevantPage() {
        // Only track pages with specific content (product, category, or business pages)
        return this.currentPage.productId || this.currentPage.categoryId || this.currentPage.businessId;
    }

    trackPageView() {
        if (this.hasTrackedView) return; // Prevent duplicate view tracking
        
        this.sendActivity({
            type: 'view',
            product_id: this.currentPage.productId,
            category_id: this.currentPage.categoryId,
            business_id: this.currentPage.businessId,
            metadata: {
                url: window.location.href,
                referrer: document.referrer,
                page_type: this.getPageType()
            }
        });
        
        this.hasTrackedView = true;
    }

    trackClicks() {
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-track]');
            if (target) {
                const trackData = JSON.parse(target.dataset.track);
                // console.log(trackData);
                this.sendActivity({
                    type: 'click',
                    ...trackData,
                    metadata: {
                        element: target.tagName,
                        text: target.textContent?.trim().substring(0, 100),
                        page_context: this.getPageType()
                    }
                });
            }
        });
    }

    trackTimeSpent() {
        let isActive = true;
        let timeSpent = 0;
        
        // Track user activity to determine if they're actually engaging
        const resetActivity = () => { isActive = true; };
        document.addEventListener('mousemove', resetActivity);
        document.addEventListener('scroll', resetActivity);
        document.addEventListener('keypress', resetActivity);
        document.addEventListener('click', resetActivity);

        // Check activity every 5 seconds
        const interval = setInterval(() => {
            if (isActive) {
                timeSpent += 5;
                isActive = false; // Reset for next interval
            }
        }, 5000);

        // Send time spent when user leaves (only if they spent meaningful time)
        window.addEventListener('beforeunload', () => {
            clearInterval(interval);
            const totalTime = Math.floor((Date.now() - this.startTime) / 1000);
            
            // Only send if user spent more than 10 seconds on the page
            if (totalTime >= 10) {
                this.sendActivity({
                    type: 'engagement', // Changed from 'time_spent' to 'engagement'
                    product_id: this.currentPage.productId,
                    category_id: this.currentPage.categoryId,
                    business_id: this.currentPage.businessId,
                    duration: totalTime,
                    metadata: { 
                        active_time: timeSpent,
                        engagement_ratio: timeSpent / totalTime
                    }
                }, true); // true for synchronous send
            }
        });

        // Also track when user switches tabs/windows
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                isActive = false;
            }
        });
    }

    sendActivity(data, synchronous = false) {
        const payload = {
            user_id: this.userId,
            session_id: this.sessionId,
            activity: data
        };
        // console.log(payload);
        if (synchronous) {
            // For beforeunload events, use sendBeacon for reliability
            navigator.sendBeacon('/api/track-activity', 
                new Blob([JSON.stringify(payload)], {type: 'application/json'})
            );
        } else {
            fetch('/api/track-activity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            }).catch(err => console.log('Tracking error:', err));
        }
    }

    getPageType() {
        if (this.currentPage.productId) return 'product';
        if (this.currentPage.categoryId) return 'category';
        if (this.currentPage.businessId) return 'business';
        return 'general';
    }

    getProductId() {
        return document.querySelector('[data-product-id]')?.dataset.productId;
    }

    getCategoryId() {
        return document.querySelector('[data-category-id]')?.dataset.categoryId;
    }

    getBusinessId() {
        return document.querySelector('[data-business-id]')?.dataset.businessId;
    }
}

// Initialize tracker
document.addEventListener('DOMContentLoaded', () => {
    new UserTracker();
});
