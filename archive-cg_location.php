<?php
/**
 * Location Archive Template - Bohemian Beach Vibe
 *
 * @package Capiznon_Geo
 */

get_header();

// DEBUG: Uncomment to see what's happening with price filter
if (!empty($_GET['price']) && current_user_can('manage_options')) {
    $debug_raw = $_GET['price'];
    $debug_slug = capiznon_geo_resolve_price_slug($debug_raw);
    $debug_term = get_term_by('slug', $debug_slug, 'location_price');
    echo '<!-- DEBUG price: raw=' . esc_html($debug_raw) . ' | slug=' . esc_html($debug_slug) . ' | term=' . ($debug_term ? $debug_term->name : 'NOT FOUND') . ' | found_posts=' . $GLOBALS['wp_query']->found_posts . ' -->';
}
?>

<main id="main" class="flex-1 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    
    <!-- Art Nouveau Header -->
    <div class="relative bg-gradient-to-br from-amber-400 via-orange-400 to-rose-400 text-white py-12 md:py-16 overflow-hidden">
        <!-- Decorative overlay -->
        <div class="absolute inset-0 bg-white/20"></div>
        
        <!-- Art Nouveau wave divider -->
        <svg class="absolute bottom-0 left-0 right-0 text-amber-50" viewBox="0 0 1440 120" fill="currentColor">
            <path d="M0,64 C240,96 480,32 720,64 C960,96 1200,32 1440,64 L1440,120 L0,120 Z"/>
        </svg>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <?php
                // Check for active filters
                $active_filters = [];
                if (!empty($_GET['amenity'])) {
                    $tag_term = get_term_by('slug', sanitize_text_field($_GET['amenity']), 'location_tag');
                    if ($tag_term) $active_filters[] = $tag_term->name;
                }
                if (!empty($_GET['type'])) {
                    $type_term = get_term_by('slug', sanitize_text_field($_GET['type']), 'location_type');
                    if ($type_term) $active_filters[] = $type_term->name;
                }
                if (!empty($_GET['area'])) {
                    $area_term = get_term_by('slug', sanitize_text_field($_GET['area']), 'location_area');
                    if ($area_term) $active_filters[] = $area_term->name;
                }
                if (!empty($_GET['cuisine'])) {
                    $cuisine_term = get_term_by('slug', sanitize_text_field($_GET['cuisine']), 'location_cuisine');
                    if ($cuisine_term) $active_filters[] = $cuisine_term->name;
                }
                if (!empty($_GET['price'])) {
                    $price_slug = capiznon_geo_resolve_price_slug($_GET['price']);
                    $price_term = get_term_by('slug', $price_slug, 'location_price');
                    if ($price_term) $active_filters[] = $price_term->name;
                }
                if (!empty($_GET['search'])) {
                    $active_filters[] = '"' . esc_html(sanitize_text_field($_GET['search'])) . '"';
                }
                ?>
                
                <?php if (!empty($active_filters)) : ?>
                    <p class="text-amber-100 text-sm uppercase tracking-wider mb-2">Filtered by</p>
                    <h1 class="text-4xl md:text-5xl font-bold mb-3 drop-shadow-lg">
                        <?php echo esc_html(implode(' + ', $active_filters)); ?>
                    </h1>
                    <p class="text-amber-50 text-xl max-w-2xl mx-auto">
                        <?php 
                        printf(
                            esc_html(_n('%d location found', '%d locations found', $wp_query->found_posts, 'capiznon-geo')),
                            $wp_query->found_posts
                        );
                        ?>
                    </p>
                <?php else : ?>
                    <h1 class="text-4xl md:text-5xl font-bold mb-3 drop-shadow-lg">
                        🌴 <?php esc_html_e('Locations in Capiz', 'capiznon-geo'); ?> 🌴
                    </h1>
                    <p class="text-amber-50 text-xl max-w-2xl mx-auto">
                        <?php 
                        printf(
                            esc_html__('Explore %d places in Capiz', 'capiznon-geo'),
                            wp_count_posts('cg_location')->publish
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filter Panel with bohemian styling -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div id="cg-filter-panel" class="cg-filter-panel">
            <!-- Header with Toggle -->
            <div class="cg-filter-header">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <h2 class="text-lg font-bold flex items-center gap-2">
                            <span class="text-2xl">🌊</span>
                            <?php esc_html_e('Filter Locations', 'capiznon-geo'); ?>
                        </h2>
                        <p class="text-sm text-white/70 mt-0.5">
                            <span id="cg-location-count" class="font-semibold text-white"><?php echo $wp_query->found_posts; ?></span>
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
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <div class="absolute bottom-2 left-2 w-6 h-6 opacity-20">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </div>
            </div>

            <!-- Collapsible Body -->
            <div id="cg-filter-body" class="cg-filter-body">
                
                <!-- Search -->
                <div class="relative mb-4">
                    <input 
                        type="search" 
                        id="cg-search" 
                        placeholder="<?php esc_attr_e('Search locations...', 'capiznon-geo'); ?>"
                        class="w-full pl-11 pr-4 py-3 bg-white/90 backdrop-blur-sm border-2 border-amber-200 rounded-2xl text-sm focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-200 transition-all font-medium placeholder:text-amber-500"
                    >
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-amber-600">
                        <span class="text-xl">🔍</span>
                    </div>
                </div>

                <!-- Quick Type Filters -->
                <div class="flex flex-wrap gap-2 mb-4">
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

                <!-- Advanced Filters (Expandable) -->
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer text-sm font-semibold text-amber-800 hover:text-amber-600 transition-colors py-2 border-t-2 border-dashed border-amber-200">
                        <span class="flex items-center gap-2">
                            <span class="text-lg">🎯</span>
                            <?php esc_html_e('Discover More', 'capiznon-geo'); ?>
                        </span>
                        <svg class="w-4 h-4 transition-transform group-open:rotate-180 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    
                    <div class="pt-3 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-amber-700 mb-1.5 uppercase tracking-wide"><?php esc_html_e('Category', 'capiznon-geo'); ?></label>
                            <select id="cg-filter-type" class="cg-filter-control">
                                <option value=""><?php esc_html_e('All Types', 'capiznon-geo'); ?></option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-amber-700 mb-1.5 uppercase tracking-wide"><?php esc_html_e('Area', 'capiznon-geo'); ?></label>
                            <select id="cg-filter-area" class="cg-filter-control">
                                <option value=""><?php esc_html_e('All Areas', 'capiznon-geo'); ?></option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-amber-700 mb-1.5 uppercase tracking-wide"><?php esc_html_e('Amenities', 'capiznon-geo'); ?></label>
                                <select id="cg-filter-tag" class="cg-filter-control">
                                    <option value=""><?php esc_html_e('Any', 'capiznon-geo'); ?></option>
                                </select>
                            </div>
                            <div>
                                <label for="cg-filter-price" class="block text-xs font-semibold text-amber-700 mb-1.5 uppercase tracking-wide"><?php esc_html_e('Price', 'capiznon-geo'); ?></label>
                                <select id="cg-filter-price" class="cg-filter-control" aria-label="<?php esc_attr_e('Price', 'capiznon-geo'); ?>">
                                    <option value=""><?php esc_html_e('Any', 'capiznon-geo'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div id="cg-filter-cuisine-wrapper">
                            <label for="cg-filter-cuisine" class="block text-xs font-semibold text-amber-700 mb-1.5 uppercase tracking-wide"><?php esc_html_e('Cuisine', 'capiznon-geo'); ?></label>
                            <select id="cg-filter-cuisine" class="cg-filter-control" aria-label="<?php esc_attr_e('Cuisine', 'capiznon-geo'); ?>">
                                <option value=""><?php esc_html_e('Any', 'capiznon-geo'); ?></option>
                            </select>
                        </div>
                        
                        <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl hover:from-amber-100 hover:to-orange-100 transition-all border border-amber-200">
                            <input type="checkbox" id="cg-filter-featured" class="cg-filter-control w-5 h-5 rounded border-2 border-amber-300 text-amber-600 focus:ring-amber-200 shrink-0">
                            <label for="cg-filter-featured" class="text-sm font-medium text-amber-900 cursor-pointer flex-1">
                                <?php esc_html_e('Featured locations only', 'capiznon-geo'); ?>
                            </label>
                            <span aria-hidden="true" class="ml-auto text-lg">⭐</span>
                        </div>
                    </div>
                </details>

                <!-- Clear Filters -->
                <button type="button" id="cg-clear-filters" class="w-full mt-4 py-2.5 text-sm font-semibold text-amber-700 hover:text-rose-600 border-2 border-dashed border-amber-300 hover:border-rose-300 rounded-2xl transition-all">
                    <?php esc_html_e('Clear all filters', 'capiznon-geo'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Content with bohemian styling -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        
        <?php if (have_posts()) : ?>
        
        <!-- Bohemian Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while (have_posts()) : the_post(); ?>
            
            <article class="bg-gradient-to-br from-white to-amber-50 rounded-3xl shadow-lg border border-amber-200 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <!-- Image with beachy placeholder -->
                <a href="<?php the_permalink(); ?>" class="block aspect-video bg-gradient-to-br from-amber-100 to-orange-100 overflow-hidden">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('location-card', ['class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300']); ?>
                    <?php else : ?>
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-100 to-orange-100">
                            <span class="text-4xl">🏝️</span>
                        </div>
                    <?php endif; ?>
                </a>

                <!-- Content with bohemian styling -->
                <div class="p-6">
                    <!-- Type with beachy badge -->
                    <?php 
                    $types = get_the_terms(get_the_ID(), 'location_type');
                    if ($types && !is_wp_error($types)) : 
                    ?>
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-2 flex items-center gap-1">
                            <span>🌊</span>
                            <?php echo esc_html($types[0]->name); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Title with featured star -->
                    <h2 class="text-xl font-bold text-amber-900 mb-3 group-hover:text-orange-700 transition-colors">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <?php if (get_post_meta(get_the_ID(), '_cg_featured', true)) : ?>
                            <span class="inline-flex text-yellow-500 ml-1" title="<?php esc_attr_e('Featured Location', 'capiznon-geo'); ?>">⭐</span>
                        <?php endif; ?>
                    </h2>

                    <!-- Excerpt -->
                    <?php if (has_excerpt()) : ?>
                        <p class="text-sm text-amber-700 line-clamp-2 mb-4 leading-relaxed"><?php echo get_the_excerpt(); ?></p>
                    <?php endif; ?>

                    <!-- Meta with beachy styling -->
                    <div class="flex items-center justify-between text-sm">
                        <?php echo capiznon_geo_open_status_badge(); ?>
                        
                        <a href="<?php the_permalink(); ?>" class="text-amber-600 font-bold hover:text-orange-600 flex items-center gap-1">
                            <?php esc_html_e('View Location', 'capiznon-geo'); ?> 
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </article>

            <?php endwhile; ?>
        </div>

        <!-- Bohemian Pagination -->
        <nav class="mt-12 flex justify-center">
            <?php
            echo paginate_links([
                'prev_text' => '← ' . __('Previous', 'capiznon-geo'),
                'next_text' => __('Next', 'capiznon-geo') . ' →',
                'type'      => 'list',
                'class'     => 'flex items-center gap-2',
            ]);
            ?>
        </nav>

        <?php else : ?>
        
        <!-- Empty state with beachy theme -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center">
                <span class="text-4xl">🏝️</span>
            </div>
            <h2 class="text-2xl font-bold text-amber-900 mb-3"><?php esc_html_e('No locations found', 'capiznon-geo'); ?></h2>
            <p class="text-amber-700 text-lg"><?php esc_html_e('Check back soon for new places to discover.', 'capiznon-geo'); ?></p>
        </div>

        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter panel toggle
    const filterToggle = document.getElementById('cg-filter-toggle');
    const filterBody = document.getElementById('cg-filter-body');
    const toggleShow = filterToggle?.querySelector('.toggle-show');
    const toggleHide = filterToggle?.querySelector('.toggle-hide');
    
    filterToggle?.addEventListener('click', function() {
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        filterBody?.classList.toggle('hidden');
        toggleShow?.classList.toggle('hidden');
        toggleHide?.classList.toggle('hidden');
    });

    // Quick type filter buttons
    const cuisineWrapper = document.getElementById('cg-filter-cuisine-wrapper');
    const cuisineSelect = document.getElementById('cg-filter-cuisine');

    document.querySelectorAll('.cg-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.cg-type-btn').forEach(b => {
                b.classList.remove('active', 'bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white');
                b.classList.add('bg-white/80', 'text-amber-800', 'border', 'border-amber-200');
            });
            this.classList.add('active', 'bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white');
            this.classList.remove('bg-white/80', 'text-amber-800', 'border', 'border-amber-200');
            
            const type = this.dataset.type;
            filterArchiveLocations(type);

        });
    });

    // Search functionality
    const searchInput = document.getElementById('cg-search');
    let searchDebounceTimer;
    
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            filterArchiveLocations();
        }, 300);
    });

    // Advanced filters
    document.querySelectorAll('.cg-filter-control').forEach(control => {
        control.addEventListener('change', function() {
            filterArchiveLocations();
        });
    });

    // Clear filters
    document.getElementById('cg-clear-filters')?.addEventListener('click', function() {
        document.querySelectorAll('.cg-filter-control').forEach(el => {
            if (el.type === 'checkbox') {
                el.checked = false;
            } else {
                el.value = '';
            }
        });
        
        // Reset type buttons
        document.querySelectorAll('.cg-type-btn').forEach(b => {
            b.classList.remove('active', 'bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white');
            b.classList.add('bg-white/80', 'text-amber-800', 'border', 'border-amber-200');
        });
        document.querySelector('.cg-type-btn[data-type=""]')?.classList.add('active', 'bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white');
        document.querySelector('.cg-type-btn[data-type=""]')?.classList.remove('bg-white/80', 'text-amber-800', 'border', 'border-amber-200');
        
        filterArchiveLocations();
    });

    // Filter function
    function filterArchiveLocations(type = null) {
        const search = searchInput?.value || '';
        const typeFilter = type !== null ? type : document.getElementById('cg-filter-type')?.value || '';
        const area = document.getElementById('cg-filter-area')?.value || '';
        const tag = document.getElementById('cg-filter-tag')?.value || '';
        const price = document.getElementById('cg-filter-price')?.value || '';
        const cuisine = document.getElementById('cg-filter-cuisine')?.value || '';
        const featured = document.getElementById('cg-filter-featured')?.checked ? '1' : '';

        // Build URL with filters
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        if (typeFilter) params.set('type', typeFilter);
        if (area) params.set('area', area);
        if (tag) params.set('amenity', tag);
        if (price) params.set('price', price);
        if (cuisine) params.set('cuisine', cuisine);
        if (featured) params.set('featured', featured);

        const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
        
        // Update URL without page reload
        history.pushState({}, '', newUrl);
        
        // Here you would typically make an AJAX call to filter results
        // For now, we'll just reload the page with new parameters
        window.location.reload();
    }

    // Load filter options from API (same as front page)
    loadFilterOptions();

    // Initialize filters from URL parameters
    initFiltersFromURL();

    function initFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);
        
        // Set search input
        if (params.get('search') && searchInput) {
            searchInput.value = params.get('search');
        }
        
        // Set type button active state
        const typeParam = params.get('type');
        if (typeParam) {
            document.querySelectorAll('.cg-type-btn').forEach(b => {
                b.classList.remove('active', 'bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white');
                b.classList.add('bg-white/80', 'text-amber-800', 'border', 'border-amber-200');
            });
            const activeBtn = document.querySelector(`.cg-type-btn[data-type="${typeParam}"]`);
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white');
                activeBtn.classList.remove('bg-white/80', 'text-amber-800', 'border', 'border-amber-200');
            }
        }
        
        // Set select filters after options are loaded
        setTimeout(() => {
            if (params.get('type')) {
                const typeSelect = document.getElementById('cg-filter-type');
                if (typeSelect) typeSelect.value = params.get('type');
            }
            if (params.get('area')) {
                const areaSelect = document.getElementById('cg-filter-area');
                if (areaSelect) areaSelect.value = params.get('area');
            }
            if (params.get('amenity')) {
                const tagSelect = document.getElementById('cg-filter-tag');
                if (tagSelect) tagSelect.value = params.get('amenity');
            }
            if (params.get('price')) {
                const priceSelect = document.getElementById('cg-filter-price');
                if (priceSelect) priceSelect.value = params.get('price');
            }
            if (params.get('cuisine')) {
                const cuisineSelect = document.getElementById('cg-filter-cuisine');
                if (cuisineSelect) cuisineSelect.value = params.get('cuisine');
                // Show cuisine wrapper if cuisine is set
                if (cuisineWrapper) cuisineWrapper.classList.remove('hidden');
            }
            if (params.get('featured') === '1') {
                const featuredCheck = document.getElementById('cg-filter-featured');
                if (featuredCheck) featuredCheck.checked = true;
            }
        }, 500);
    }

    async function loadFilterOptions() {
        try {
            const response = await fetch(`${window.capiznonGeo?.restUrl || '/wp-json/capiznon-geo/v1/'}filters`);
            if (!response.ok) return;
            
            const data = await response.json();

            // Cache for reuse
            window.cgFilterOptions = data;

            populateFilterSelect('cg-filter-type', data.types, true);
            populateFilterSelect('cg-filter-area', data.areas);
            populateFilterSelect('cg-filter-tag', data.tags);
            populateFilterSelect('cg-filter-price', data.prices);
            populateFilterSelect('cg-filter-cuisine', data.cuisines);
        } catch (error) {
            console.error('Error loading filters:', error);
        }
    }

    function populateFilterSelect(elementId, items, hierarchical = false) {
        const select = document.getElementById(elementId);
        if (!select || !items || !Array.isArray(items) || items.length === 0) return;

        // Keep the first option (All)
        const firstOption = select.querySelector('option');
        select.innerHTML = '';
        if (firstOption) select.appendChild(firstOption);

        if (hierarchical) {
            const parents = items.filter(item => !item.parent);
            const children = items.filter(item => item.parent);

            parents.forEach(parent => {
                const option = document.createElement('option');
                option.value = parent.slug;
                option.textContent = parent.name;
                select.appendChild(option);

                children.filter(c => c.parent === parent.id).forEach(child => {
                    const childOption = document.createElement('option');
                    childOption.value = child.slug;
                    childOption.textContent = `  └ ${child.name}`;
                    select.appendChild(childOption);
                });
            });
        } else {
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                select.appendChild(option);
            });
        }
    }
});
</script>
