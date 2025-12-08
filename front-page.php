<?php
/**
 * Front Page Template - Discovery Home
 * Mobile-first card carousel layout
 *
 * @package Capiznon_Geo
 */

get_header();

// Get current user info
$current_user = wp_get_current_user();
$user_name = $current_user->ID ? $current_user->display_name : __('Explorer', 'capiznon-geo');
?>

<main id="main" class="flex-1 bg-white min-h-screen">
    
    <div class="max-w-7xl mx-auto w-full sm:px-2 lg:px-4">
        <!-- Header Section -->
    <div class="px-4 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-medium text-gray-900">
                    <?php printf(__('Hi, %s', 'capiznon-geo'), esc_html($user_name)); ?>
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    <?php esc_html_e('Capiz, Philippines', 'capiznon-geo'); ?>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo esc_url(home_url('/map/')); ?>" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors">
                    <span>🗺️</span>
                </a>
                <?php if (is_user_logged_in()) : ?>
                    <a href="<?php echo esc_url(get_edit_profile_url()); ?>" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                        <?php echo get_avatar($current_user->ID, 40, '', '', ['class' => 'w-full h-full object-cover']); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <span>👤</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="px-4 mb-8">
        <div class="relative">
            <input 
                type="search" 
                id="cg-search" 
                placeholder="<?php esc_attr_e('Search for places...', 'capiznon-geo'); ?>"
                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-gray-300 focus:bg-white transition-colors placeholder:text-gray-400"
            >
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Category Quick Filters -->
    <div class="px-4 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-medium text-gray-900">
                Categories
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                View all
            </a>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-4">
            <button type="button" data-type="" class="cg-category-btn active flex flex-col items-center gap-2 flex-shrink-0">
                <div class="w-14 h-14 bg-gray-900 rounded-xl flex items-center justify-center transition-colors">
                    <span class="text-xl text-white">✨</span>
                </div>
                <span class="text-xs font-medium text-gray-900">All</span>
            </button>
            <button type="button" data-type="food-dining" class="cg-category-btn flex flex-col items-center gap-2 flex-shrink-0">
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <span class="text-xl">🍽️</span>
                </div>
                <span class="text-xs font-medium text-gray-600">Food</span>
            </button>
            <button type="button" data-type="accommodation" class="cg-category-btn flex flex-col items-center gap-2 flex-shrink-0">
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <span class="text-xl">🏨</span>
                </div>
                <span class="text-xs font-medium text-gray-600">Stay</span>
            </button>
            <button type="button" data-type="attractions" class="cg-category-btn flex flex-col items-center gap-2 flex-shrink-0">
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <span class="text-xl">⭐</span>
                </div>
                <span class="text-xs font-medium text-gray-600">See</span>
            </button>
            <button type="button" data-type="shopping" class="cg-category-btn flex flex-col items-center gap-2 flex-shrink-0">
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <span class="text-xl">🛍️</span>
                </div>
                <span class="text-xs font-medium text-gray-600">Shop</span>
            </button>
        </div>
    </div>

    <!-- Near Me Section (Hidden if empty) -->
    <section id="cg-near-me-section" class="mb-12 hidden">
        <div class="px-4 flex items-center justify-between mb-4">
            <h2 class="text-base font-medium text-gray-900">
                Near You
            </h2>
            <a href="<?php echo esc_url(home_url('/map/')); ?>?near=me" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                View all
            </a>
        </div>
        <div id="cg-near-me-carousel" class="cg-carousel">
            <!-- Cards populated via JS -->
        </div>
    </section>

    <!-- Featured Section -->
    <section id="cg-featured-section" class="mb-12 mt-4">
        <div class="px-4 flex items-center justify-between mb-4">
            <h2 class="text-base font-medium text-gray-900">
                Featured
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>?featured=1" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                View all
            </a>
        </div>
        <div id="cg-featured-carousel" class="cg-carousel mt-4">
            <?php
            $featured = new WP_Query([
                'post_type' => 'cg_location',
                'posts_per_page' => 10,
                'meta_query' => [
                    [
                        'key' => '_cg_featured',
                        'value' => '1',
                        'compare' => '='
                    ]
                ]
            ]);
            
            if ($featured->have_posts()) :
                while ($featured->have_posts()) : $featured->the_post();
                    get_template_part('template-parts/card', 'location-carousel');
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="px-6 sm:px-8 lg:px-12 w-full text-center py-12 text-stone-400">
                    <p><?php esc_html_e('No featured locations yet', 'capiznon-geo'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </section>

    <!-- Food Section -->
    <section id="cg-food-section" class="mb-14 mt-4">
        <div class="px-6 sm:px-8 lg:px-12 flex items-center justify-between mb-5">
            <h2 class="text-sm font-semibold text-stone-500 uppercase tracking-wider flex items-center gap-2">
                <span>🍽️</span>
                <?php esc_html_e('Food & Dining', 'capiznon-geo'); ?>
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>?type=food-dining" class="text-amber-600/80 font-medium text-sm hover:text-amber-700 transition-colors">
                <?php esc_html_e('View all', 'capiznon-geo'); ?>
            </a>
        </div>
        <div id="cg-food-carousel" class="cg-carousel">
            <?php
            $food = new WP_Query([
                'post_type' => 'cg_location',
                'posts_per_page' => 6,
                'orderby' => 'rand',
                'tax_query' => [
                    [
                        'taxonomy' => 'location_type',
                        'field' => 'slug',
                        'terms' => ['food-dining', 'restaurants', 'cafes', 'bars', 'street-food', 'bakeries'],
                    ]
                ]
            ]);
            
            if ($food->have_posts()) :
                while ($food->have_posts()) : $food->the_post();
                    get_template_part('template-parts/card', 'location-carousel');
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="px-6 sm:px-8 lg:px-12 w-full text-center py-12 text-stone-400">
                    <p><?php esc_html_e('No food locations yet', 'capiznon-geo'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </section>

    <!-- Stay Section -->
    <section id="cg-stay-section" class="mb-14 mt-4">
        <div class="px-6 sm:px-8 lg:px-12 flex items-center justify-between mb-5">
            <h2 class="text-sm font-semibold text-stone-500 uppercase tracking-wider flex items-center gap-2">
                <span>🏨</span>
                <?php esc_html_e('Places to Stay', 'capiznon-geo'); ?>
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>?type=accommodation" class="text-amber-600/80 font-medium text-sm hover:text-amber-700 transition-colors">
                <?php esc_html_e('View all', 'capiznon-geo'); ?>
            </a>
        </div>
        <div id="cg-stay-carousel" class="cg-carousel">
            <?php
            $stay = new WP_Query([
                'post_type' => 'cg_location',
                'posts_per_page' => 6,
                'orderby' => 'rand',
                'tax_query' => [
                    [
                        'taxonomy' => 'location_type',
                        'field' => 'slug',
                        'terms' => ['accommodation', 'hotels', 'resorts', 'guesthouses', 'homestays', 'hostels'],
                    ]
                ]
            ]);
            
            if ($stay->have_posts()) :
                while ($stay->have_posts()) : $stay->the_post();
                    get_template_part('template-parts/card', 'location-carousel');
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="px-6 sm:px-8 lg:px-12 w-full text-center py-12 text-stone-400">
                    <p><?php esc_html_e('No accommodation yet', 'capiznon-geo'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </section>

    <!-- See Section -->
    <section id="cg-see-section" class="mb-14 mt-4">
        <div class="px-6 sm:px-8 lg:px-12 flex items-center justify-between mb-5">
            <h2 class="text-sm font-semibold text-stone-500 uppercase tracking-wider flex items-center gap-2">
                <span>⭐</span>
                <?php esc_html_e('Things to See', 'capiznon-geo'); ?>
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>?type=attractions" class="text-amber-600/80 font-medium text-sm hover:text-amber-700 transition-colors">
                <?php esc_html_e('View all', 'capiznon-geo'); ?>
            </a>
        </div>
        <div id="cg-see-carousel" class="cg-carousel">
            <?php
            $see = new WP_Query([
                'post_type' => 'cg_location',
                'posts_per_page' => 6,
                'orderby' => 'rand',
                'tax_query' => [
                    [
                        'taxonomy' => 'location_type',
                        'field' => 'slug',
                        'terms' => ['attractions', 'beaches', 'historical', 'parks', 'museums', 'religious'],
                    ]
                ]
            ]);
            
            if ($see->have_posts()) :
                while ($see->have_posts()) : $see->the_post();
                    get_template_part('template-parts/card', 'location-carousel');
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="px-6 sm:px-8 lg:px-12 w-full text-center py-12 text-stone-400">
                    <p><?php esc_html_e('No attractions yet', 'capiznon-geo'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </section>

    <!-- Shop Section -->
    <section id="cg-shop-section" class="mb-14 mt-4">
        <div class="px-6 sm:px-8 lg:px-12 flex items-center justify-between mb-5">
            <h2 class="text-sm font-semibold text-stone-500 uppercase tracking-wider flex items-center gap-2">
                <span>🛍️</span>
                <?php esc_html_e('Shopping', 'capiznon-geo'); ?>
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>?type=shopping" class="text-amber-600/80 font-medium text-sm hover:text-amber-700 transition-colors">
                <?php esc_html_e('View all', 'capiznon-geo'); ?>
            </a>
        </div>
        <div id="cg-shop-carousel" class="cg-carousel">
            <?php
            $shop = new WP_Query([
                'post_type' => 'cg_location',
                'posts_per_page' => 6,
                'orderby' => 'rand',
                'tax_query' => [
                    [
                        'taxonomy' => 'location_type',
                        'field' => 'slug',
                        'terms' => ['shopping', 'markets', 'malls', 'souvenirs', 'local-products'],
                    ]
                ]
            ]);
            
            if ($shop->have_posts()) :
                while ($shop->have_posts()) : $shop->the_post();
                    get_template_part('template-parts/card', 'location-carousel');
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="px-6 sm:px-8 lg:px-12 w-full text-center py-12 text-stone-400">
                    <p><?php esc_html_e('No shopping locations yet', 'capiznon-geo'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </section>

    </div><!-- .max-w-7xl -->

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category filter buttons
    const categoryBtns = document.querySelectorAll('.cg-category-btn');
    
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Update active state
            categoryBtns.forEach(b => {
                const icon = b.querySelector('div');
                const label = b.querySelector('span:last-child');
                if (b === this) {
                    b.classList.add('active');
                    icon.classList.remove('bg-white/90', 'border', 'border-stone-100/80');
                    icon.classList.add('bg-gradient-to-br', 'from-amber-200/90', 'to-orange-200/90');
                    label.classList.remove('text-stone-500');
                    label.classList.add('text-stone-600');
                } else {
                    b.classList.remove('active');
                    icon.classList.add('bg-white/90', 'border', 'border-stone-100/80');
                    icon.classList.remove('bg-gradient-to-br', 'from-amber-200/90', 'to-orange-200/90');
                    label.classList.add('text-stone-500');
                    label.classList.remove('text-stone-600');
                }
            });
            
            // Filter sections based on type
            filterSections(type);
        });
    });
    
    function filterSections(type) {
        const sections = {
            'food-dining': 'cg-food-section',
            'accommodation': 'cg-stay-section',
            'attractions': 'cg-see-section',
            'shopping': 'cg-shop-section'
        };
        
        if (!type) {
            // Show all sections
            Object.values(sections).forEach(id => {
                document.getElementById(id)?.classList.remove('hidden');
            });
            document.getElementById('cg-featured-section')?.classList.remove('hidden');
        } else {
            // Hide all, show only selected
            Object.entries(sections).forEach(([key, id]) => {
                const section = document.getElementById(id);
                if (section) {
                    if (key === type) {
                        section.classList.remove('hidden');
                    } else {
                        section.classList.add('hidden');
                    }
                }
            });
            // Hide featured when filtering by type
            document.getElementById('cg-featured-section')?.classList.add('hidden');
        }
    }
    
    // Search functionality
    const searchInput = document.getElementById('cg-search');
    let searchTimeout;
    
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const query = this.value.trim();
            if (query.length >= 2) {
                // Redirect to archive with search
                window.location.href = `<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>?search=${encodeURIComponent(query)}`;
            }
        }, 500);
    });
    
    searchInput?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = `<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>?search=${encodeURIComponent(query)}`;
            }
        }
    });
    
    // Near Me functionality
    if (navigator.geolocation) {
        // Don't auto-request - wait for user interaction or show prompt
        const nearMeSection = document.getElementById('cg-near-me-section');
        
        // Check if we have cached location
        const cachedLat = localStorage.getItem('cg_user_lat');
        const cachedLng = localStorage.getItem('cg_user_lng');
        
        if (cachedLat && cachedLng) {
            loadNearbyLocations(parseFloat(cachedLat), parseFloat(cachedLng));
        }
    }
    
    async function loadNearbyLocations(lat, lng) {
        try {
            const response = await fetch(`<?php echo esc_url(rest_url('capiznon-geo/v1/locations')); ?>?lat=${lat}&lng=${lng}&radius=1`);
            if (!response.ok) return;
            
            const data = await response.json();
            
            if (data.locations && data.locations.length > 0) {
                const section = document.getElementById('cg-near-me-section');
                const carousel = document.getElementById('cg-near-me-carousel');
                
                if (section && carousel) {
                    section.classList.remove('hidden');
                    carousel.innerHTML = data.locations.map(loc => createLocationCard(loc)).join('');
                }
            }
        } catch (error) {
            console.error('Error loading nearby locations:', error);
        }
    }
    
    function createLocationCard(location) {
        const distance = location.distance ? `${location.distance.toFixed(1)}km` : '';
        const image = location.thumbnail || '';
        const imageHtml = image 
            ? `<img src="${image}" alt="${location.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">`
            : `<div class="w-full h-full bg-gradient-to-br from-stone-100 to-amber-50 flex items-center justify-center"><span class="text-3xl opacity-50">🏝️</span></div>`;
        
        return `
            <a href="${location.permalink}" class="w-52 group">
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-stone-100/80 overflow-hidden hover:shadow-md hover:border-amber-200/50 transition-all duration-300">
                    <div class="aspect-[4/3] relative overflow-hidden">
                        ${imageHtml}
                        ${location.featured ? '<span class="absolute top-2.5 left-2.5 bg-gradient-to-r from-rose-400 to-pink-400 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm uppercase tracking-wide">Hot</span>' : ''}
                        <button type="button" class="absolute top-2.5 right-2.5 w-7 h-7 bg-white/80 backdrop-blur-sm rounded-full flex items-center justify-center text-stone-300 hover:text-rose-400 transition-colors shadow-sm">
                            ❤️
                        </button>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-stone-700 text-sm leading-tight line-clamp-2">${location.title}</h3>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-stone-400">${location.type || ''}</span>
                            ${distance ? `<span class="text-xs text-amber-600/70 font-medium">${distance}</span>` : ''}
                        </div>
                    </div>
                </div>
            </a>
        `;
    }
});
</script>

<style>
/* Carousel container - hide scrollbar completely */
.cg-carousel {
    display: flex;
    gap: 1.25rem;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 0.5rem 1.5rem 1.5rem 1.5rem;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    
    /* Hide scrollbar - all browsers */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE/Edge */
}

@media (min-width: 640px) {
    .cg-carousel {
}

.cg-carousel::-webkit-scrollbar {
    display: none;
}

.cg-carousel > * {
    flex-shrink: 0;
    scroll-snap-align: start;
}

/* Category button active state */
.cg-category-btn.active > div:first-child {
    background-color: rgb(17 24 39);
}

.cg-category-btn.active > span:last-child {
    color: rgb(17 24 39);
}
</style>

<?php 
// Use regular footer on front page
get_footer(); 
?>
