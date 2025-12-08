<?php
/**
 * Single Location Template - Bohemian Beach Vibe
 *
 * @package Capiznon_Geo
 */

get_header();

while (have_posts()) : the_post();
    $lat = get_post_meta(get_the_ID(), '_cg_latitude', true);
    $lng = get_post_meta(get_the_ID(), '_cg_longitude', true);
    $address = capiznon_geo_get_address();
    $phone = get_post_meta(get_the_ID(), '_cg_phone', true);
    $email = get_post_meta(get_the_ID(), '_cg_email', true);
    $website = get_post_meta(get_the_ID(), '_cg_website', true);
    $facebook = get_post_meta(get_the_ID(), '_cg_facebook', true);
    $instagram = get_post_meta(get_the_ID(), '_cg_instagram', true);
    $gallery = capiznon_geo_get_gallery();
    $is_featured = get_post_meta(get_the_ID(), '_cg_featured', true);
    $cuisines = get_the_terms(get_the_ID(), 'location_cuisine');
    $areas = get_the_terms(get_the_ID(), 'location_area');
    $price = get_the_terms(get_the_ID(), 'location_price');
    $tags = get_the_terms(get_the_ID(), 'location_tag');
?>

<main id="main" class="flex-1 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    
    <!-- Hero Section with Art Nouveau flair -->
    <div class="relative h-80 md:h-96 overflow-hidden">
        <?php if (has_post_thumbnail()) : ?>
            <div class="absolute inset-0">
                <img 
                    src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'location-hero')); ?>" 
                    alt="<?php the_title_attribute(); ?>"
                    class="w-full h-full object-cover"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-amber-900/70 via-orange-800/40 to-transparent"></div>
            </div>
        <?php else : ?>
            <div class="absolute inset-0 bg-gradient-to-br from-amber-400 via-orange-400 to-rose-400">
                <div class="absolute inset-0 bg-white/20"></div>
            </div>
        <?php endif; ?>
        
        <!-- Art Nouveau decorative wave -->
        <svg class="absolute bottom-0 left-0 right-0 text-amber-50" viewBox="0 0 1440 120" fill="currentColor">
            <path d="M0,64 C240,96 480,32 720,64 C960,96 1200,32 1440,64 L1440,120 L0,120 Z"/>
        </svg>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-end pb-12">
            <!-- Breadcrumb with beachy icons -->
            <nav class="mb-6">
                <ol class="flex items-center gap-2 text-sm text-amber-100">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-white transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                        <?php esc_html_e('Home', 'capiznon-geo'); ?>
                    </a></li>
                    <li><span class="text-amber-200">✦</span></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>" class="hover:text-white transition-colors"><?php esc_html_e('Discover', 'capiznon-geo'); ?></a></li>
                    <li><span class="text-amber-200">✦</span></li>
                    <li class="text-white font-medium"><?php the_title(); ?></li>
                </ol>
            </nav>

            <!-- Type & Cuisine Badges -->
            <div class="flex flex-wrap gap-2 mb-4">
                <?php 
                $types = get_the_terms(get_the_ID(), 'location_type');
                if ($types && !is_wp_error($types)) : 
                    foreach ($types as $type) :
                ?>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/90 text-amber-900 backdrop-blur-sm border border-amber-200">
                        <span class="mr-2">🌴</span>
                        <?php echo esc_html($type->name); ?>
                    </span>
                <?php 
                    endforeach;
                endif;
                
                // Cuisines
                if ($cuisines && !is_wp_error($cuisines)) :
                    foreach ($cuisines as $cuisine) :
                ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose-100/80 text-rose-800 backdrop-blur-sm">
                        <?php echo esc_html($cuisine->name); ?>
                    </span>
                <?php 
                    endforeach;
                endif;
                
                // Price range
                if ($price && !is_wp_error($price)) :
                    foreach ($price as $price_term) :
                ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100/80 text-green-800 backdrop-blur-sm">
                        <?php echo esc_html($price_term->name); ?>
                    </span>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>

            <!-- Title with featured star -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-3 drop-shadow-lg">
                <?php the_title(); ?>
                <?php if ($is_featured) : ?>
                    <span class="inline-flex items-center ml-3 text-yellow-300" title="<?php esc_attr_e('Featured Location', 'capiznon-geo'); ?>">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </span>
                <?php endif; ?>
            </h1>

            <!-- Status with beachy icons -->
            <?php echo capiznon_geo_open_status_badge(); ?>
        </div>
    </div>

    <!-- Content with bohemian layout -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-10">
                
                <!-- About Section with artistic flair -->
                <section class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-lg border border-amber-100 p-8 md:p-10 relative overflow-hidden">
                    <!-- Decorative corner -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-100 to-orange-100 rounded-bl-full opacity-50"></div>
                    
                    <h2 class="text-2xl md:text-3xl font-bold text-amber-900 mb-6 flex items-center">
                        <span class="mr-3 text-3xl">🌊</span>
                        <?php esc_html_e('The Vibe', 'capiznon-geo'); ?>
                    </h2>
                    <div class="prose prose-lg prose-amber max-w-none relative z-10">
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Enhanced Tags -->
                    <?php if ($tags && !is_wp_error($tags)) : ?>
                    <div class="mt-8 pt-6 border-t border-amber-100">
                        <h3 class="text-sm font-medium text-amber-700 mb-3 uppercase tracking-wide">✨ Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($tags as $tag) : ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-amber-50 to-orange-50 text-amber-800 border border-amber-200">
                                    #<?php echo esc_html($tag->name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- Gallery with bohemian grid -->
                <?php if (!empty($gallery)) : ?>
                <section class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-lg border border-amber-100 p-8 md:p-10">
                    <h2 class="text-2xl md:text-3xl font-bold text-amber-900 mb-6 flex items-center">
                        <span class="mr-3 text-3xl">📸</span>
                        <?php esc_html_e('Moments', 'capiznon-geo'); ?>
                    </h2>
                    <div class="cg-gallery">
                        <?php foreach ($gallery as $image) : ?>
                            <div class="cg-gallery-item group" data-full="<?php echo esc_url($image['full']); ?>">
                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="rounded-2xl transition-transform duration-300 group-hover:scale-105">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Map with organic styling -->
                <?php if ($lat && $lng) : ?>
                <section class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-lg border border-amber-100 overflow-hidden">
                    <div class="p-6 border-b border-amber-100">
                        <h2 class="text-2xl font-bold text-amber-900 flex items-center">
                            <span class="mr-3 text-3xl">🗺️</span>
                            <?php esc_html_e('Find Us', 'capiznon-geo'); ?>
                        </h2>
                    </div>
                    <div id="cg-location-map" class="h-80"></div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const map = L.map('cg-location-map').setView([<?php echo esc_js($lat); ?>, <?php echo esc_js($lng); ?>], 16);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap'
                            }).addTo(map);
                            L.marker([<?php echo esc_js($lat); ?>, <?php echo esc_js($lng); ?>]).addTo(map);
                        });
                    </script>
                </section>
                <?php endif; ?>

                <!-- Nearby Locations with beachy theme -->
                <?php if ($lat && $lng) : ?>
                <section class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl shadow-lg border border-amber-100 p-8 md:p-10">
                    <h2 class="text-2xl md:text-3xl font-bold text-amber-900 mb-6 flex items-center">
                        <span class="mr-3 text-3xl">🏝️</span>
                        <?php esc_html_e('Nearby Locations', 'capiznon-geo'); ?>
                    </h2>
                    <div id="cg-nearby-locations" class="space-y-3">
                        <div class="cg-loading">
                            <div class="cg-spinner"></div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            loadNearbyLocations(<?php echo esc_js($lat); ?>, <?php echo esc_js($lng); ?>, 'cg-nearby-locations', {
                                radius: 1,
                                exclude: <?php echo get_the_ID(); ?>,
                                limit: 5
                            });
                        });
                    </script>
                </section>
                <?php endif; ?>
            </div>

            <!-- Sidebar with bohemian cards -->
            <div class="space-y-8">
                
                <!-- Contact Card with artistic design -->
                <div class="bg-gradient-to-br from-white to-amber-50 backdrop-blur-sm rounded-3xl shadow-lg border border-amber-100 p-8 relative overflow-hidden">
                    <!-- Decorative pattern -->
                    <div class="absolute top-0 right-0 w-24 h-24 opacity-10">
                        <svg viewBox="0 0 100 100" fill="currentColor" class="text-amber-600">
                            <path d="M50,10 Q60,30 80,30 Q60,50 50,70 Q40,50 20,30 Q40,30 50,10 Z"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-amber-900 mb-6 flex items-center">
                        <span class="mr-3 text-2xl">🌺</span>
                        <?php esc_html_e('Get in Touch', 'capiznon-geo'); ?>
                    </h3>
                    
                    <div class="space-y-5 relative z-10">
                        <?php if ($address) : ?>
                        <div class="flex gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="text-sm text-amber-800 leading-relaxed"><?php echo $address; ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($phone) : ?>
                        <a href="tel:<?php echo esc_attr($phone); ?>" class="flex gap-4 group hover:bg-amber-50/50 -mx-2 px-2 py-2 rounded-2xl transition-colors">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-amber-900 group-hover:text-orange-700"><?php echo esc_html($phone); ?></div>
                        </a>
                        <?php endif; ?>

                        <?php if ($email) : ?>
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="flex gap-4 group hover:bg-amber-50/50 -mx-2 px-2 py-2 rounded-2xl transition-colors">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-amber-900 group-hover:text-orange-700"><?php echo esc_html($email); ?></div>
                        </a>
                        <?php endif; ?>

                        <?php if ($website) : ?>
                        <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener" class="flex gap-4 group hover:bg-amber-50/50 -mx-2 px-2 py-2 rounded-2xl transition-colors">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-amber-900 group-hover:text-orange-700 truncate"><?php echo esc_html(preg_replace('#^https?://#', '', $website)); ?></div>
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Social Links with beachy styling -->
                    <?php if ($facebook || $instagram) : ?>
                    <div class="flex gap-3 mt-6 pt-6 border-t border-amber-100">
                        <?php if ($facebook) : ?>
                        <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center text-blue-700 hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($instagram) : ?>
                        <a href="https://instagram.com/<?php echo esc_attr(ltrim($instagram, '@')); ?>" target="_blank" rel="noopener" class="w-12 h-12 bg-gradient-to-br from-pink-100 to-rose-200 rounded-2xl flex items-center justify-center text-pink-700 hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Directions Button with beachy design -->
                    <?php if ($lat && $lng) : ?>
                    <a 
                        href="<?php echo esc_url(capiznon_geo_get_directions_url()); ?>" 
                        target="_blank" 
                        rel="noopener"
                        class="mt-6 w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold rounded-2xl hover:from-amber-600 hover:to-orange-600 transform hover:scale-105 transition-all shadow-lg"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <?php esc_html_e('Navigate Here', 'capiznon-geo'); ?>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Hours Card with bohemian design -->
                <?php 
                $hours = capiznon_geo_get_hours();
                if ($hours && is_array($hours)) : 
                ?>
                <div class="bg-gradient-to-br from-white to-orange-50 backdrop-blur-sm rounded-3xl shadow-lg border border-amber-100 p-8 relative overflow-hidden">
                    <!-- Decorative sun pattern -->
                    <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
                        <svg viewBox="0 0 100 100" fill="currentColor" class="text-orange-500">
                            <circle cx="50" cy="50" r="15"/>
                            <path d="M50,10 L50,25 M50,75 L50,90 M10,50 L25,50 M75,50 L90,50 M22,22 L32,32 M68,68 L78,78 M78,22 L68,32 M32,68 L22,78" stroke="currentColor" stroke-width="3"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-amber-900 mb-6 flex items-center relative z-10">
                        <span class="mr-3 text-2xl">🕐</span>
                        <?php esc_html_e('Opening Hours', 'capiznon-geo'); ?>
                    </h3>
                    <div class="space-y-3 text-sm relative z-10">
                        <?php 
                        $days = [
                            'monday'    => __('Monday', 'capiznon-geo'),
                            'tuesday'   => __('Tuesday', 'capiznon-geo'),
                            'wednesday' => __('Wednesday', 'capiznon-geo'),
                            'thursday'  => __('Thursday', 'capiznon-geo'),
                            'friday'    => __('Friday', 'capiznon-geo'),
                            'saturday'  => __('Saturday', 'capiznon-geo'),
                            'sunday'    => __('Sunday', 'capiznon-geo'),
                        ];
                        $today = strtolower(date('l'));
                        
                        foreach ($days as $key => $label) :
                            $day_hours = $hours[$key] ?? null;
                            $is_today = ($key === $today);
                        ?>
                        <div class="flex justify-between items-center py-2 px-3 rounded-xl <?php echo $is_today ? 'bg-gradient-to-r from-amber-100 to-orange-100 font-medium text-amber-900' : 'text-amber-700'; ?>">
                            <span class="flex items-center gap-2">
                                <?php if ($is_today) : ?><span class="text-lg">☀️</span><?php endif; ?>
                                <?php echo esc_html($label); ?>
                            </span>
                            <span>
                                <?php if (!$day_hours || !empty($day_hours['closed'])) : ?>
                                    <span class="text-rose-600 font-medium"><?php esc_html_e('Closed', 'capiznon-geo'); ?></span>
                                <?php else : ?>
                                    <span class="font-medium"><?php echo esc_html($day_hours['open'] . ' - ' . $day_hours['close']); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php elseif (is_string($hours)) : ?>
                <div class="bg-gradient-to-br from-white to-orange-50 backdrop-blur-sm rounded-3xl shadow-lg border border-amber-100 p-8">
                    <h3 class="text-xl font-bold text-amber-900 mb-4 flex items-center">
                        <span class="mr-3 text-2xl">🕐</span>
                        <?php esc_html_e('Opening Hours', 'capiznon-geo'); ?>
                    </h3>
                    <div class="prose prose-amber max-w-none">
                        <?php echo $hours; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Area Info Card -->
                <?php if ($areas && !is_wp_error($areas)) : ?>
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 backdrop-blur-sm rounded-3xl shadow-lg border border-amber-100 p-8">
                    <h4 class="text-lg font-bold mb-6 flex items-center gap-2">
                        <span class="w-8 h-1 bg-gradient-to-r from-amber-400 to-orange-400 rounded-full"></span>
                        <?php esc_html_e('Locations', 'capiznon-geo'); ?>
                    </h4>
                    <div class="space-y-2">
                        <?php foreach ($areas as $area) : ?>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/70 text-amber-900 border border-amber-200">
                                <?php echo esc_html($area->name); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<?php
endwhile;
get_footer();
