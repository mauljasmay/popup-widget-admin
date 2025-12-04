/**
 * Popup Widget Display Script
 * This script displays popup widgets on websites
 */

(function() {
    'use strict';
    
    // Configuration
    const config = {
        apiEndpoint: '/admin/api/widgets_public.php',
        analyticsEndpoint: '/admin/api/analytics.php',
        sessionKey: 'popup_widget_session',
        shownWidgetsKey: 'popup_widget_shown',
        trackingEnabled: true,
        retryAttempts: 3,
        retryDelay: 1000
    };
    
    // State management
    let widgets = [];
    let activePopups = [];
    let sessionData = {};
    let shownWidgets = {};
    
    // Initialize
    function init() {
        try {
            // Load session data
            sessionData = JSON.parse(localStorage.getItem(config.sessionKey) || '{}');
            shownWidgets = JSON.parse(localStorage.getItem(config.shownWidgetsKey) || '{}');
            
            // Generate new session ID if needed
            if (!sessionData.sessionId) {
                sessionData.sessionId = generateSessionId();
                sessionData.startTime = Date.now();
                saveSessionData();
            }
            
            // Load widgets
            loadWidgets();
            
            // Setup event listeners
            setupEventListeners();
            
        } catch (error) {
            console.error('Popup Widget initialization error:', error);
        }
    }
    
    // Generate unique session ID
    function generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Save session data
    function saveSessionData() {
        try {
            localStorage.setItem(config.sessionKey, JSON.stringify(sessionData));
            localStorage.setItem(config.shownWidgetsKey, JSON.stringify(shownWidgets));
        } catch (error) {
            console.error('Error saving session data:', error);
        }
    }
    
    // Load widgets from API
    function loadWidgets() {
        const fetchWithRetry = async (url, attempts = 0) => {
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    widgets = data.widgets;
                    processWidgets();
                } else {
                    console.error('Error loading widgets:', data.error);
                }
            } catch (error) {
                console.error('Error fetching widgets:', error);
                if (attempts < config.retryAttempts) {
                    console.log(`Retrying... Attempt ${attempts + 1} of ${config.retryAttempts}`);
                    setTimeout(() => fetchWithRetry(url, attempts + 1), config.retryDelay);
                } else {
                    console.error('Failed to load widgets after multiple attempts');
                }
            }
        };
        
        fetchWithRetry(config.apiEndpoint + '?action=active');
    }
    
    // Process and display widgets
    function processWidgets() {
        widgets.forEach(widget => {
            if (shouldShowWidget(widget)) {
                scheduleWidget(widget);
            }
        });
    }
    
    // Check if widget should be shown
    function shouldShowWidget(widget) {
        // Check if widget is active
        if (!widget.is_active) return false;
        
        // Check date range
        if (widget.start_date && new Date(widget.start_date) > new Date()) return false;
        if (widget.end_date && new Date(widget.end_date) < new Date()) return false;
        
        // Check page targeting
        if (widget.target_pages) {
            const targetPages = JSON.parse(widget.target_pages);
            const currentPage = window.location.pathname;
            if (!targetPages.some(page => currentPage.includes(page))) {
                return false;
            }
        }
        
        // Check page exclusions
        if (widget.exclude_pages) {
            const excludePages = JSON.parse(widget.exclude_pages);
            const currentPage = window.location.pathname;
            if (excludePages.some(page => currentPage.includes(page))) {
                return false;
            }
        }
        
        // Check max shows per user
        if (widget.max_shows_per_user > 0) {
            const widgetShows = shownWidgets[widget.id] || 0;
            if (widgetShows >= widget.max_shows_per_user) {
                return false;
            }
        }
        
        return true;
    }
    
    // Schedule widget display
    function scheduleWidget(widget) {
        switch (widget.type) {
            case 'modal':
            case 'notification':
                if (widget.show_after > 0) {
                    setTimeout(() => showWidget(widget), widget.show_after * 1000);
                } else {
                    showWidget(widget);
                }
                break;
                
            case 'slide_in':
                if (widget.show_after > 0) {
                    setTimeout(() => showWidget(widget), widget.show_after * 1000);
                } else {
                    showWidget(widget);
                }
                break;
                
            case 'exit_intent':
                setupExitIntent(widget);
                break;
        }
        
        // Setup scroll trigger if enabled
        if (widget.show_on_scroll) {
            setupScrollTrigger(widget);
        }
    }
    
    // Show widget
    function showWidget(widget) {
        // Check if already shown in this session
        if (activePopups.find(popup => popup.id === widget.id)) {
            return;
        }
        
        // Create popup element
        const popup = createPopupElement(widget);
        document.body.appendChild(popup);
        
        // Track active popup
        activePopups.push({
            id: widget.id,
            element: popup,
            widget: widget
        });
        
        // Track analytics
        trackAnalytics(widget.id, 'show');
        
        // Update shown widgets count
        shownWidgets[widget.id] = (shownWidgets[widget.id] || 0) + 1;
        saveSessionData();
        
        // Setup auto close
        if (widget.auto_close > 0) {
            setTimeout(() => closeWidget(widget.id), widget.auto_close * 1000);
        }
    }
    
    // Create popup element
    function createPopupElement(widget) {
        const popup = document.createElement('div');
        popup.className = `popup-widget popup-${widget.type}`;
        popup.id = `popup-${widget.id}`;
        
        // Apply styles
        popup.style.cssText = `
            position: fixed;
            z-index: 9999;
            background-color: ${widget.background_color};
            color: ${widget.text_color};
            width: ${widget.width}px;
            max-width: 90vw;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            ${getPositionStyles(widget)}
            ${getAnimationStyles(widget)}
        `;
        
        // Create content
        let content = `
            <div class="popup-content" style="padding: 20px;">
                ${widget.show_close_button ? `
                    <button class="popup-close" onclick="closeWidget(${widget.id})" style="
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        background: none;
                        border: none;
                        font-size: 20px;
                        cursor: pointer;
                        color: ${widget.text_color};
                        opacity: 0.7;
                    ">×</button>
                ` : ''}
                <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: 600;">${widget.title}</h3>
                <div style="margin-bottom: 20px; line-height: 1.5;">${widget.content}</div>
                <button class="popup-button" onclick="clickWidget(${widget.id})" style="
                    background-color: ${widget.button_color};
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-weight: 600;
                ">${widget.button_text}</button>
            </div>
        `;
        
        popup.innerHTML = content;
        
        // Add to DOM and animate in
        document.body.appendChild(popup);
        
        // Trigger animation
        setTimeout(() => {
            popup.style.opacity = '1';
            popup.style.transform = 'translateY(0)';
        }, 100);
        
        return popup;
    }
    
    // Get position styles
    function getPositionStyles(widget) {
        const positions = {
            'center': 'top: 50%; left: 50%; transform: translate(-50%, -50%);',
            'top_left': 'top: 20px; left: 20px;',
            'top_right': 'top: 20px; right: 20px;',
            'bottom_left': 'bottom: 20px; left: 20px;',
            'bottom_right': 'bottom: 20px; right: 20px;'
        };
        
        return positions[widget.position] || positions['center'];
    }
    
    // Get animation styles
    function getAnimationStyles(widget) {
        const animations = {
            'modal': 'opacity: 0; transform: translate(-50%, -50%) scale(0.8); transition: all 0.3s ease;',
            'slide_in': 'opacity: 0; transform: translateX(100%); transition: all 0.3s ease;',
            'notification': 'opacity: 0; transform: translateY(-20px); transition: all 0.3s ease;',
            'exit_intent': 'opacity: 0; transform: translate(-50%, -50%) scale(0.9); transition: all 0.3s ease;'
        };
        
        return animations[widget.type] || animations['modal'];
    }
    
    // Close widget
    function closeWidget(widgetId) {
        const popupIndex = activePopups.findIndex(popup => popup.id === widgetId);
        if (popupIndex !== -1) {
            const popup = activePopups[popupIndex];
            
            // Animate out
            popup.element.style.opacity = '0';
            popup.element.style.transform = popup.element.style.transform.includes('translateY') ? 
                'translateY(20px)' : 'translate(-50%, -50%) scale(0.8)';
            
            // Remove from DOM
            setTimeout(() => {
                if (popup.element.parentNode) {
                    popup.element.parentNode.removeChild(popup.element);
                }
            }, 300);
            
            // Remove from active popups
            activePopups.splice(popupIndex, 1);
            
            // Track analytics
            trackAnalytics(widgetId, 'close');
        }
    }
    
    // Click widget
    function clickWidget(widgetId) {
        const popup = activePopups.find(popup => popup.id === widgetId);
        if (popup) {
            // Track analytics
            trackAnalytics(widgetId, 'click');
            
            // Close widget
            closeWidget(widgetId);
        }
    }
    
    // Setup exit intent
    function setupExitIntent(widget) {
        let mouseLeft = false;
        
        document.addEventListener('mouseleave', function(e) {
            if (e.clientY <= 0 && !mouseLeft) {
                mouseLeft = true;
                showWidget(widget);
            }
        });
        
        document.addEventListener('mouseenter', function() {
            mouseLeft = false;
        });
    }
    
    // Setup scroll trigger
    function setupScrollTrigger(widget) {
        const scrollPercentage = widget.scroll_percentage || 50;
        const triggerPoint = (scrollPercentage / 100) * document.documentElement.scrollHeight;
        
        let hasTriggered = false;
        
        window.addEventListener('scroll', function() {
            if (!hasTriggered && window.scrollY + window.innerHeight >= triggerPoint) {
                hasTriggered = true;
                showWidget(widget);
            }
        });
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && activePopups.length > 0) {
                closeWidget(activePopups[activePopups.length - 1].id);
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            activePopups.forEach(popup => {
                // Recalculate positions if needed
                const styles = getPositionStyles(popup.widget);
                popup.element.style.cssText += styles;
            });
        });
    }
    
    // Track analytics
    function trackAnalytics(widgetId, action) {
        if (!config.trackingEnabled) return;
        
        const data = {
            widget_id: widgetId,
            action: action,
            page_url: window.location.href,
            session_id: sessionData.sessionId,
            user_agent: navigator.userAgent
        };
        
        // Send analytics data with retry
        const sendAnalytics = async (attempts = 0) => {
            try {
                const response = await fetch(config.analyticsEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                // Just log if successful
                console.log('Analytics tracked:', action, 'for widget:', widgetId);
                
            } catch (error) {
                console.error('Analytics tracking error:', error);
                if (attempts < config.retryAttempts) {
                    console.log(`Retrying analytics... Attempt ${attempts + 1}`);
                    setTimeout(() => sendAnalytics(attempts + 1), config.retryDelay);
                } else {
                    console.error('Failed to track analytics after multiple attempts');
                }
            }
        };
        
        sendAnalytics();
    }
    
    // Public API
    window.PopupWidget = {
        show: function(widgetId) {
            const widget = widgets.find(w => w.id == widgetId);
            if (widget) {
                showWidget(widget);
            }
        },
        close: function(widgetId) {
            closeWidget(widgetId);
        },
        closeAll: function() {
            activePopups.slice().forEach(popup => {
                closeWidget(popup.id);
            });
        },
        getSessionId: function() {
            return sessionData.sessionId;
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();