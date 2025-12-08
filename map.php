<?php
/**
 * Map Page Template - Interactive Map
 * Bohemian Beach Vibe Design
 *
 * Template Name: Map View
 *
 * @package Capiznon_Geo
 */

get_header();
?>

<main id="main" class="flex-1 relative bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    
    <!-- Full-screen Map -->
    <div class="fixed inset-0 top-[68px] z-0">
        <div id="cg-map" class="w-full h-full"></div>
    </div>

    <!-- Floating Filter Panel - Simplified -->
    <div id="cg-filter-panel" class="fixed top-20 left-4 z-30 w-80 max-w-[calc(100vw-2rem)] cg-filter-panel transition-all duration-300">
        
        <!-- Header with Toggle -->
        <div class="cg-filter-header">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <span class="text-2xl">🌊</span>
                        <?php esc_html_e('Explore Capiz', 'capiznon-geo'); ?>
                    </h2>
                    <p class="text-sm text-white/70 mt-0.5">
                        <span id="cg-location-count" class="font-semibold text-white">0</span>
                        <?php esc_html_e('locations found', 'capiznon-geo'); ?>
                    </p>
                </div>
                <button type="button" id="cg-filter-toggle" class="cg-filter-toggle" aria-expanded="true">
                    <span class="toggle-show hidden"><?php esc_html_e('Show', 'capiznon-geo'); ?></span>
                    <span class="toggle-hide"><?php esc_html_e('Hide', 'capiznon-geo'); ?></span>
                </button>
            </div>
            
            <!-- Decorative elements -->
            <div class="absolute top-2 right-2 w-8 h-8 opacity-20">
                <svg viewBox="0 0 100 100" fill="currentColor" class="text-amber-200">
                    <path d="M50,10 Q60,30 80,30 Q60,50 50,70 Q40,50 20,30 Q40,30 50,10 Z"/>
                </svg>
            </div>
            <div class="absolute bottom-2 right-12 w-4 h-4 bg-amber-300/30 rotate-45 rounded-full"></div>
        </div>

        <!-- Collapsible Body - Simplified -->
        <div id="cg-filter-body" class="cg-filter-body">
            
            <!-- Search -->
            <div class="relative mb-4">
                <input 
                    type="search" 
                    id="cg-search" 
                    placeholder="<?php esc_attr_e('Search places...', 'capiznon-geo'); ?>"
                    class="w-full pl-11 pr-4 py-3 bg-white/90 backdrop-blur-sm border-2 border-amber-200 rounded-2xl text-sm focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-200 transition-all font-medium placeholder:text-amber-500"
                >
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-amber-600">
                    <span class="text-xl">🔍</span>
                </div>
            </div>

            <!-- Quick Type Filters -->
            <div class="flex flex-wrap gap-2 mb-3">
                <button type="button" data-type="" class="cg-type-btn active px-4 py-2 text-xs font-bold rounded-full bg-gradient-to-r from-amber-500 to-orange-500 text-white transition-all hover:scale-105">
                    ✨ <?php esc_html_e('All', 'capiznon-geo'); ?>
                </button>
                <button type="button" data-type="food-dining" class="cg-type-btn px-4 py-2 text-xs font-bold rounded-full bg-white/80 text-amber-800 hover:bg-gradient-to-r hover:from-amber-100 hover:to-orange-100 transition-all hover:scale-105 border border-amber-200">
                    🍽️ <?php esc_html_e('Food', 'capiznon-geo'); ?>
                </button>
                <button type="button" data-type="accommodation" class="cg-type-btn px-4 py-2 text-xs font-bold rounded-full bg-white/80 text-amber-800 hover:bg-gradient-to-r hover:from-amber-100 hover:to-orange-100 transition-all hover:scale-105 border border-amber-200">
                    🏨 <?php esc_html_e('Stay', 'capiznon-geo'); ?>
                </button>
                <button type="button" data-type="attractions" class="cg-type-btn px-4 py-2 text-xs font-bold rounded-full bg-white/80 text-amber-800 hover:bg-gradient-to-r hover:from-amber-100 hover:to-orange-100 transition-all hover:scale-105 border border-amber-200">
                    ⭐ <?php esc_html_e('See', 'capiznon-geo'); ?>
                </button>
                <button type="button" data-type="shopping" class="cg-type-btn px-4 py-2 text-xs font-bold rounded-full bg-white/80 text-amber-800 hover:bg-gradient-to-r hover:from-amber-100 hover:to-orange-100 transition-all hover:scale-105 border border-amber-200">
                    🛍️ <?php esc_html_e('Shop', 'capiznon-geo'); ?>
                </button>
            </div>

            <!-- Link to All Locations for advanced filters -->
            <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>" class="block text-center text-sm text-amber-600 hover:text-amber-800 font-medium py-2 border-t border-amber-200">
                <?php esc_html_e('More filters →', 'capiznon-geo'); ?>
            </a>
        </div>
    </div>

    <!-- Location List Panel (Right Side / Drawer) -->
    <div id="cg-list-panel" class="fixed top-20 right-4 bottom-4 w-80 max-w-[calc(100vw-2rem)] z-40 cg-list-panel">
        <div class="h-full bg-gradient-to-br from-white to-amber-50 backdrop-blur-lg rounded-3xl shadow-xl overflow-hidden flex flex-col border-2 border-amber-200">
            
            <!-- List Header -->
            <div class="p-4 bg-gradient-to-r from-amber-100 to-orange-100 border-b border-amber-200">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-amber-900 flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-lg">🏝️</span>
                        <?php esc_html_e('Discover Nearby', 'capiznon-geo'); ?>
                    </h3>
                    <button type="button" onclick="CapiznonGeoMap.fitToMarkers()" class="p-2 hover:bg-white/50 rounded-xl transition-colors" title="<?php esc_attr_e('Fit all', 'capiznon-geo'); ?>">
                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Location List -->
            <div id="cg-location-list" class="flex-1 overflow-y-auto p-3 space-y-3 cg-sidebar">
                <div class="cg-loading">
                    <div class="cg-spinner"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Toggle -->
    <button 
        type="button" 
        id="cg-mobile-toggle"
        class="lg:hidden fixed bottom-6 left-4 z-50 bg-gradient-to-r from-amber-500 to-orange-500 text-white px-6 py-3 rounded-full shadow-xl flex items-center gap-3 hover:shadow-2xl transition-all active:scale-95"
    >
        <span class="text-xl">🏝️</span>
        <span class="font-bold"><?php esc_html_e('Discover Capiz', 'capiznon-geo'); ?></span>
        <span id="cg-mobile-count" class="bg-white/20 px-2 py-0.5 rounded-full text-xs font-bold">0</span>
    </button>

    <!-- Desktop List Toggle -->
    <button 
        type="button" 
        id="cg-desktop-list-toggle"
        class="hidden lg:flex fixed bottom-6 left-6 z-30 bg-gradient-to-r from-amber-100 to-orange-100 text-amber-900 px-4 py-2 rounded-full shadow-xl items-center gap-2 hover:shadow-2xl transition-all active:scale-95 border border-amber-200 font-semibold"
    >
        <span class="text-lg">🏝️</span>
        <span class="text-sm font-bold"><?php esc_html_e('View Locations', 'capiznon-geo'); ?></span>
    </button>

    <!-- Add Location Buttons -->
    <div class="fixed bottom-6 right-6 z-40 flex flex-col items-end gap-3">
        <button
            type="button"
            id="cg-add-my-location"
            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-full shadow-xl flex items-center gap-2 hover:shadow-2xl transition-all active:scale-95 text-sm font-bold"
        >
            <span class="text-lg">📍</span>
            <span><?php esc_html_e('Add Location', 'capiznon-geo'); ?></span>
        </button>
        <button
            type="button"
            id="cg-add-place-here"
            class="bg-gradient-to-r from-amber-100 to-orange-100 text-amber-900 px-4 py-2 rounded-full shadow-xl flex items-center gap-2 hover:shadow-2xl transition-all active:scale-95 text-sm font-bold border border-amber-200"
        >
            <span class="text-lg">✨</span>
            <span><?php esc_html_e('Add Here', 'capiznon-geo'); ?></span>
        </button>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const filterToggle = document.getElementById('cg-filter-toggle');
    const filterBody = document.getElementById('cg-filter-body');
    const filterPanel = document.getElementById('cg-filter-panel');
    const toggleShow = filterToggle?.querySelector('.toggle-show');
    const toggleHide = filterToggle?.querySelector('.toggle-hide');
    const listPanel = document.getElementById('cg-list-panel');
    const searchInput = document.getElementById('cg-search');

    // Filter panel toggle
    filterToggle?.addEventListener('click', function() {
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        filterBody?.classList.toggle('hidden');
        toggleShow?.classList.toggle('hidden');
        toggleHide?.classList.toggle('hidden');
    });

    // Function to minimize filter panel
    function minimizeFilterPanel() {
        if (filterToggle && filterBody) {
            filterToggle.setAttribute('aria-expanded', 'false');
            filterBody.classList.add('hidden');
            toggleShow?.classList.remove('hidden');
            toggleHide?.classList.add('hidden');
        }
    }

    // Function to open locations list
    function openLocationsList() {
        if (listPanel) {
            listPanel.classList.add('cg-list-panel-open');
        }
    }

    // Search functionality - minimize filter and open list on search
    let searchTimeout;
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        searchTimeout = setTimeout(() => {
            if (query.length >= 2) {
                // Minimize filter panel
                minimizeFilterPanel();
                // Open locations list
                openLocationsList();
                // Trigger search filter
                CapiznonGeoMap.filter({ search: query });
            } else if (query.length === 0) {
                // Clear search filter
                CapiznonGeoMap.filter({ search: '' });
            }
        }, 300);
    });

    // Also handle Enter key
    searchInput?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                minimizeFilterPanel();
                openLocationsList();
                CapiznonGeoMap.filter({ search: query });
            }
        }
    });

    // Quick type filter buttons
    document.querySelectorAll('.cg-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.cg-type-btn').forEach(b => {
                b.classList.remove('active', 'bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white');
                b.classList.add('bg-white/80', 'text-amber-800', 'border', 'border-amber-200');
            });
            this.classList.add('active', 'bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white');
            this.classList.remove('bg-white/80', 'text-amber-800', 'border', 'border-amber-200');
            
            const type = this.dataset.type;
            CapiznonGeoMap.filter({ type: type });
        });
    });

    // Update mobile count
    document.addEventListener('cg:locationsLoaded', function(e) {
        const mobileCount = document.getElementById('cg-mobile-count');
        if (mobileCount) mobileCount.textContent = e.detail.total;
    });

    // Toggle list panel function
    function toggleListPanel() {
        if (!listPanel) return;
        listPanel.classList.toggle('cg-list-panel-open');
    }

    // Mobile drawer toggle
    const mobileToggle = document.getElementById('cg-mobile-toggle');
    if (mobileToggle && listPanel) {
        mobileToggle.addEventListener('click', function() {
            toggleListPanel();
        });
    }

    // Desktop show/hide toggle
    const desktopToggle = document.getElementById('cg-desktop-list-toggle');
    if (desktopToggle && listPanel) {
        desktopToggle.addEventListener('click', function() {
            toggleListPanel();
        });
    }

    // Close list panel when clicking outside it
    document.addEventListener('click', function(e) {
        if (!listPanel) return;
        if (!listPanel.classList.contains('cg-list-panel-open')) return;

        const isClickInsidePanel = listPanel.contains(e.target);
        const isClickInsideFilter = filterPanel?.contains(e.target);
        const isToggleButton = e.target.closest('#cg-mobile-toggle') || e.target.closest('#cg-desktop-list-toggle');
        if (!isClickInsidePanel && !isClickInsideFilter && !isToggleButton) {
            listPanel.classList.remove('cg-list-panel-open');
        }
    });

    // Close list panel on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;
        if (!listPanel) return;
        if (!listPanel.classList.contains('cg-list-panel-open')) return;
        listPanel.classList.remove('cg-list-panel-open');
    });
});
</script>

<?php 
// Don't show footer on map page - full screen experience
wp_footer(); 
?>
</body>
</html>
