<?php
/**
 * Template Part: Location Carousel Card
 * Minimalist Design
 *
 * @package Capiznon_Geo
 */

$post_id = get_the_ID();
$featured = get_post_meta($post_id, '_cg_featured', true);
$lat = get_post_meta($post_id, '_cg_latitude', true);
$lng = get_post_meta($post_id, '_cg_longitude', true);

// Get location type
$types = get_the_terms($post_id, 'location_type');
$type_name = $types && !is_wp_error($types) ? $types[0]->name : '';

// Get area
$areas = get_the_terms($post_id, 'location_area');
$area_name = $areas && !is_wp_error($areas) ? $areas[0]->name : '';
?>

<a href="<?php the_permalink(); ?>" class="w-64 group">
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-gray-300 transition-colors">
        <!-- Image -->
        <div class="aspect-video relative overflow-hidden bg-gray-100">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('location-card', ['class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300']); ?>
            <?php else : ?>
                <div class="w-full h-full flex items-center justify-center">
                    <span class="text-3xl text-gray-400">🏝️</span>
                </div>
            <?php endif; ?>
            
            <?php if ($featured) : ?>
                <span class="absolute top-2 left-2 bg-black text-white text-xs font-medium px-2 py-1 rounded">
                    Featured
                </span>
            <?php endif; ?>
        </div>
        
        <!-- Content -->
        <div class="p-4">
            <h3 class="font-medium text-gray-900 text-sm line-clamp-2">
                <?php the_title(); ?>
            </h3>
            
            <div class="flex items-center justify-between mt-2">
                <?php if ($type_name) : ?>
                    <span class="text-xs text-gray-500"><?php echo esc_html($type_name); ?></span>
                <?php endif; ?>
                
                <?php if ($area_name) : ?>
                    <span class="text-xs text-gray-500"><?php echo esc_html($area_name); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</a>
