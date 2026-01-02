/**
 * GLOBAL MODAL & ALERT POSITIONING FIX - JavaScript Component
 * Elite Salon - Universal Solution for All Pages
 * 
 * PURPOSE:
 * 1. Ensure #global-alert-container exists on every page
 * 2. Move orphaned alerts into the container
 * 3. Move all modals to document body (escape parent stacking contexts)
 * 4. Handle dynamically created alerts/modals
 * 
 * USAGE: Add this script after Bootstrap JS on all pages
 */

(function() {
    'use strict';
    
    // ================================================================
    // CONFIGURATION
    // ================================================================
    const CONFIG = {
        alertContainerId: 'global-alert-container',
        navbarHeight: 70, // Adjust if navbar height changes
        alertAutoHideDuration: 5000, // 5 seconds
        debugMode: false // Set to true for console logs
    };
    
    // ================================================================
    // UTILITY FUNCTIONS
    // ================================================================
    
    /**
     * Debug logger (only logs if debugMode is enabled)
     */
    function log(...args) {
        if (CONFIG.debugMode) {
            console.log('[Modal/Alert Fix]', ...args);
        }
    }
    
    /**
     * Create global alert container if it doesn't exist
     */
    function ensureAlertContainer() {
        let container = document.getElementById(CONFIG.alertContainerId);
        
        if (!container) {
            log('Creating global alert container');
            container = document.createElement('div');
            container.id = CONFIG.alertContainerId;
            
            // Insert at beginning of body (but after navbar if exists)
            const navbar = document.querySelector('.navbar');
            if (navbar && navbar.nextSibling) {
                navbar.parentNode.insertBefore(container, navbar.nextSibling);
            } else {
                document.body.insertBefore(container, document.body.firstChild);
            }
        }
        
        return container;
    }
    
    /**
     * Move orphaned alerts into global container
     */
    function moveOrphanedAlerts() {
        const container = ensureAlertContainer();
        
        // Find all alerts NOT already in the global container
        const orphanedAlerts = document.querySelectorAll('.alert:not(#' + CONFIG.alertContainerId + ' .alert)');
        
        orphanedAlerts.forEach(alert => {
            // Skip alerts inside modals (they should stay there)
            if (alert.closest('.modal')) {
                log('Skipping alert inside modal:', alert);
                return;
            }
            
            log('Moving orphaned alert to container:', alert);
            container.appendChild(alert);
        });
    }
    
    /**
     * Move all modals to document body (escape stacking contexts)
     */
    function moveModalsToBody() {
        const modals = document.querySelectorAll('.modal');
        
        modals.forEach(modal => {
            // Only move if not already a direct child of body
            if (modal.parentElement !== document.body) {
                log('Moving modal to body:', modal.id || 'unnamed modal');
                document.body.appendChild(modal);
            }
        });
    }
    
    /**
     * Auto-hide alerts after configured duration
     */
    function setupAlertAutoHide() {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(alert => {
            // Skip if already has auto-hide timer
            if (alert.dataset.autoHideSet) return;
            
            // Mark as processed
            alert.dataset.autoHideSet = 'true';
            
            // Set timer
            setTimeout(() => {
                // Use Bootstrap's dismiss if available
                const closeBtn = alert.querySelector('[data-bs-dismiss="alert"]');
                if (closeBtn) {
                    closeBtn.click();
                } else {
                    // Fallback: fade out and remove
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentElement) {
                            alert.remove();
                        }
                    }, 500);
                }
            }, CONFIG.alertAutoHideDuration);
        });
    }
    
    /**
     * Fix z-index hierarchy
     */
    function fixZIndexHierarchy() {
        // Ensure modals are above alerts
        document.querySelectorAll('.modal').forEach(modal => {
            if (!modal.style.zIndex || parseInt(modal.style.zIndex) < 1055) {
                modal.style.zIndex = '1055';
            }
        });
        
        // Ensure backdrops are behind modals
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            if (!backdrop.style.zIndex || parseInt(backdrop.style.zIndex) >= 1055) {
                backdrop.style.zIndex = '1050';
            }
        });
    }
    
    /**
     * Initialize all fixes
     */
    function initializeFixes() {
        log('Initializing modal/alert fixes...');
        
        // Step 1: Ensure alert container exists
        ensureAlertContainer();
        
        // Step 2: Move orphaned alerts
        moveOrphanedAlerts();
        
        // Step 3: Move modals to body
        moveModalsToBody();
        
        // Step 4: Fix z-index
        fixZIndexHierarchy();
        
        // Step 5: Setup auto-hide
        setupAlertAutoHide();
        
        log('Initialization complete');
    }
    
    /**
     * Setup mutation observer for dynamically added content
     */
    function setupMutationObserver() {
        const observer = new MutationObserver(function(mutations) {
            let needsUpdate = false;
            
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        // Check if added node is or contains alert/modal
                        if (node.classList && 
                            (node.classList.contains('alert') || 
                             node.classList.contains('modal'))) {
                            needsUpdate = true;
                        } else if (node.querySelector) {
                            if (node.querySelector('.alert, .modal')) {
                                needsUpdate = true;
                            }
                        }
                    }
                });
            });
            
            if (needsUpdate) {
                log('DOM changed, re-running fixes...');
                // Debounce to avoid excessive calls
                clearTimeout(observer.debounceTimer);
                observer.debounceTimer = setTimeout(() => {
                    moveOrphanedAlerts();
                    moveModalsToBody();
                    fixZIndexHierarchy();
                    setupAlertAutoHide();
                }, 100);
            }
        });
        
        // Observe body for changes
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        log('Mutation observer setup complete');
    }
    
    /**
     * Handle Bootstrap modal events
     */
    function setupModalEventHandlers() {
        // When modal is shown, ensure proper positioning
        document.addEventListener('show.bs.modal', function(e) {
            log('Modal opening:', e.target.id || 'unnamed modal');
            
            // Ensure modal is in body
            if (e.target.parentElement !== document.body) {
                document.body.appendChild(e.target);
            }
            
            // Fix z-index
            fixZIndexHierarchy();
        });
        
        // When modal is hidden, check for leftover backdrops
        document.addEventListener('hidden.bs.modal', function(e) {
            log('Modal closed:', e.target.id || 'unnamed modal');
            
            // Clean up orphaned backdrops (Bootstrap bug workaround)
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                const openModals = document.querySelectorAll('.modal.show');
                
                if (openModals.length === 0 && backdrops.length > 0) {
                    log('Cleaning up orphaned backdrops');
                    backdrops.forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                }
            }, 300);
        });
    }
    
    /**
     * Public API for manually showing alerts
     */
    window.showGlobalAlert = function(type, message, autoDismiss = true) {
        const container = ensureAlertContainer();
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        
        // Icon based on type
        const icons = {
            success: 'bi-check-circle',
            danger: 'bi-exclamation-triangle',
            warning: 'bi-exclamation-triangle',
            info: 'bi-info-circle'
        };
        const icon = icons[type] || 'bi-info-circle';
        
        alertDiv.innerHTML = `
            <i class="bi ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        container.appendChild(alertDiv);
        
        if (autoDismiss) {
            setupAlertAutoHide();
        }
        
        log('Alert created:', type, message);
        
        return alertDiv;
    };
    
    // ================================================================
    // INITIALIZATION
    // ================================================================
    
    /**
     * Run fixes when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initializeFixes();
            setupMutationObserver();
            setupModalEventHandlers();
        });
    } else {
        // DOM already loaded
        initializeFixes();
        setupMutationObserver();
        setupModalEventHandlers();
    }
    
    log('Global modal/alert fix script loaded');
    
})();

/**
 * USAGE EXAMPLES:
 * 
 * // Show a success alert programmatically
 * showGlobalAlert('success', 'Operation completed successfully!');
 * 
 * // Show an error alert
 * showGlobalAlert('danger', 'An error occurred. Please try again.');
 * 
 * // Show a warning that doesn't auto-dismiss
 * showGlobalAlert('warning', 'Important notice!', false);
 * 
 * // All existing alerts and modals will be automatically fixed
 * // No changes needed to existing PHP/HTML code
 */
