<?php
/**
 * Custom Taxonomies
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Register custom taxonomies for locations
 */
function capiznon_geo_register_taxonomies() {
    // Location Type (hierarchical - like categories)
    // e.g., Food & Dining > Restaurant > Fine Dining
    $type_labels = [
        'name'                       => _x('Location Types', 'taxonomy general name', 'capiznon-geo'),
        'singular_name'              => _x('Location Type', 'taxonomy singular name', 'capiznon-geo'),
        'search_items'               => __('Search Types', 'capiznon-geo'),
        'all_items'                  => __('All Types', 'capiznon-geo'),
        'parent_item'                => __('Parent Type', 'capiznon-geo'),
        'parent_item_colon'          => __('Parent Type:', 'capiznon-geo'),
        'edit_item'                  => __('Edit Type', 'capiznon-geo'),
        'update_item'                => __('Update Type', 'capiznon-geo'),
        'add_new_item'               => __('Add New Type', 'capiznon-geo'),
        'new_item_name'              => __('New Type Name', 'capiznon-geo'),
        'menu_name'                  => __('Types', 'capiznon-geo'),
        'view_item'                  => __('View Type', 'capiznon-geo'),
        'popular_items'              => __('Popular Types', 'capiznon-geo'),
        'separate_items_with_commas' => __('Separate types with commas', 'capiznon-geo'),
        'add_or_remove_items'        => __('Add or remove types', 'capiznon-geo'),
        'choose_from_most_used'      => __('Choose from the most used types', 'capiznon-geo'),
        'not_found'                  => __('No types found.', 'capiznon-geo'),
        'back_to_items'              => __('← Back to Types', 'capiznon-geo'),
    ];

    register_taxonomy('location_type', ['cg_location'], [
        'labels'            => $type_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'type', 'with_front' => false],
    ]);

    // Location Tags (non-hierarchical - like tags)
    // e.g., wifi, parking, pet-friendly, outdoor-seating
    $tag_labels = [
        'name'                       => _x('Location Tags', 'taxonomy general name', 'capiznon-geo'),
        'singular_name'              => _x('Location Tag', 'taxonomy singular name', 'capiznon-geo'),
        'search_items'               => __('Search Tags', 'capiznon-geo'),
        'all_items'                  => __('All Tags', 'capiznon-geo'),
        'edit_item'                  => __('Edit Tag', 'capiznon-geo'),
        'update_item'                => __('Update Tag', 'capiznon-geo'),
        'add_new_item'               => __('Add New Tag', 'capiznon-geo'),
        'new_item_name'              => __('New Tag Name', 'capiznon-geo'),
        'menu_name'                  => __('Tags', 'capiznon-geo'),
        'popular_items'              => __('Popular Tags', 'capiznon-geo'),
        'separate_items_with_commas' => __('Separate tags with commas', 'capiznon-geo'),
        'add_or_remove_items'        => __('Add or remove tags', 'capiznon-geo'),
        'choose_from_most_used'      => __('Choose from the most used tags', 'capiznon-geo'),
        'not_found'                  => __('No tags found.', 'capiznon-geo'),
        'back_to_items'              => __('← Back to Tags', 'capiznon-geo'),
    ];

    register_taxonomy('location_tag', ['cg_location'], [
        'labels'            => $tag_labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'tag', 'with_front' => false],
    ]);

    // Area/Neighborhood
    // e.g., Downtown, Baybay Beach, Pueblo de Panay
    $area_labels = [
        'name'                       => _x('Areas', 'taxonomy general name', 'capiznon-geo'),
        'singular_name'              => _x('Area', 'taxonomy singular name', 'capiznon-geo'),
        'search_items'               => __('Search Areas', 'capiznon-geo'),
        'all_items'                  => __('All Areas', 'capiznon-geo'),
        'parent_item'                => __('Parent Area', 'capiznon-geo'),
        'parent_item_colon'          => __('Parent Area:', 'capiznon-geo'),
        'edit_item'                  => __('Edit Area', 'capiznon-geo'),
        'update_item'                => __('Update Area', 'capiznon-geo'),
        'add_new_item'               => __('Add New Area', 'capiznon-geo'),
        'new_item_name'              => __('New Area Name', 'capiznon-geo'),
        'menu_name'                  => __('Areas', 'capiznon-geo'),
        'not_found'                  => __('No areas found.', 'capiznon-geo'),
        'back_to_items'              => __('← Back to Areas', 'capiznon-geo'),
    ];

    register_taxonomy('location_area', ['cg_location'], [
        'labels'            => $area_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => false,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'area', 'with_front' => false],
    ]);

    // Price Range
    $price_labels = [
        'name'              => _x('Price Ranges', 'taxonomy general name', 'capiznon-geo'),
        'singular_name'     => _x('Price Range', 'taxonomy singular name', 'capiznon-geo'),
        'menu_name'         => __('Price Ranges', 'capiznon-geo'),
        'all_items'         => __('All Price Ranges', 'capiznon-geo'),
        'edit_item'         => __('Edit Price Range', 'capiznon-geo'),
        'update_item'       => __('Update Price Range', 'capiznon-geo'),
        'add_new_item'      => __('Add New Price Range', 'capiznon-geo'),
        'new_item_name'     => __('New Price Range', 'capiznon-geo'),
        'not_found'         => __('No price ranges found.', 'capiznon-geo'),
    ];

    register_taxonomy('location_price', ['cg_location'], [
        'labels'            => $price_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => false,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => false,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'price', 'with_front' => false],
    ]);

    // Cuisine (for food places)
    $cuisine_labels = [
        'name'                       => _x('Cuisines', 'taxonomy general name', 'capiznon-geo'),
        'singular_name'              => _x('Cuisine', 'taxonomy singular name', 'capiznon-geo'),
        'search_items'               => __('Search Cuisines', 'capiznon-geo'),
        'all_items'                  => __('All Cuisines', 'capiznon-geo'),
        'edit_item'                  => __('Edit Cuisine', 'capiznon-geo'),
        'update_item'                => __('Update Cuisine', 'capiznon-geo'),
        'add_new_item'               => __('Add New Cuisine', 'capiznon-geo'),
        'new_item_name'              => __('New Cuisine Name', 'capiznon-geo'),
        'menu_name'                  => __('Cuisines', 'capiznon-geo'),
        'popular_items'              => __('Popular Cuisines', 'capiznon-geo'),
        'separate_items_with_commas' => __('Separate cuisines with commas', 'capiznon-geo'),
        'add_or_remove_items'        => __('Add or remove cuisines', 'capiznon-geo'),
        'choose_from_most_used'      => __('Choose from the most used cuisines', 'capiznon-geo'),
        'not_found'                  => __('No cuisines found.', 'capiznon-geo'),
        'back_to_items'              => __('← Back to Cuisines', 'capiznon-geo'),
    ];

    register_taxonomy('location_cuisine', ['cg_location'], [
        'labels'            => $cuisine_labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => false,
        'show_in_nav_menus' => false,
        'show_tagcloud'     => false,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'cuisine', 'with_front' => false],
    ]);

    // Location Vibe (non-hierarchical)
    $vibe_labels = [
        'name'                       => _x('Location Vibes', 'taxonomy general name', 'capiznon-geo'),
        'singular_name'              => _x('Location Vibe', 'taxonomy singular name', 'capiznon-geo'),
        'search_items'               => __('Search Vibes', 'capiznon-geo'),
        'all_items'                  => __('All Vibes', 'capiznon-geo'),
        'edit_item'                  => __('Edit Vibe', 'capiznon-geo'),
        'update_item'                => __('Update Vibe', 'capiznon-geo'),
        'add_new_item'               => __('Add New Vibe', 'capiznon-geo'),
        'new_item_name'              => __('New Vibe Name', 'capiznon-geo'),
        'menu_name'                  => __('Vibes', 'capiznon-geo'),
        'popular_items'              => __('Popular Vibes', 'capiznon-geo'),
        'separate_items_with_commas' => __('Separate vibes with commas', 'capiznon-geo'),
        'add_or_remove_items'        => __('Add or remove vibes', 'capiznon-geo'),
        'choose_from_most_used'      => __('Choose from the most used vibes', 'capiznon-geo'),
        'not_found'                  => __('No vibes found.', 'capiznon-geo'),
        'back_to_items'              => __('← Back to Vibes', 'capiznon-geo'),
    ];

    register_taxonomy('location_vibe', ['cg_location'], [
        'labels'            => $vibe_labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => false,
        'show_in_nav_menus' => false,
        'show_tagcloud'     => false,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'vibe', 'with_front' => false],
    ]);
}
add_action('init', 'capiznon_geo_register_taxonomies');

/**
 * Insert default terms on theme activation
 */
function capiznon_geo_insert_default_terms() {
    // Default Location Types
    $types = [
        'Food & Dining' => [
            'Restaurant',
            'Cafe',
            'Bar',
            'Eatery',
            'Fast Food',
            'Bakery',
            'Food Stall',
        ],
        'Shopping' => [
            'Mall',
            'Boutique',
            'Store',
            'Market',
            'Souvenir Shop',
        ],
        'Accommodation' => [
            'Hotel',
            'Resort',
            'Inn',
            'Pension House',
            'Hostel',
        ],
        'Attractions' => [
            'Beach',
            'Park',
            'Museum',
            'Church',
            'Historical Site',
            'Nature Spot',
        ],
        'Services' => [
            'Bank',
            'Hospital',
            'Pharmacy',
            'Gas Station',
            'Transportation Hub',
        ],
        'Entertainment' => [
            'Cinema',
            'KTV',
            'Sports Facility',
            'Events Venue',
        ],
    ];

    foreach ($types as $parent => $children) {
        $parent_term = term_exists($parent, 'location_type');
        if (!$parent_term) {
            $parent_term = wp_insert_term($parent, 'location_type');
        }
        
        if (!is_wp_error($parent_term)) {
            $parent_id = is_array($parent_term) ? $parent_term['term_id'] : $parent_term;
            foreach ($children as $child) {
                if (!term_exists($child, 'location_type')) {
                    wp_insert_term($child, 'location_type', ['parent' => $parent_id]);
                }
            }
        }
    }

    // Default Price Ranges
    $prices = [
        '₱' => 'Budget (Under ₱200)',
        '₱₱' => 'Moderate (₱200-500)',
        '₱₱₱' => 'Upscale (₱500-1000)',
        '₱₱₱₱' => 'Fine Dining (₱1000+)',
    ];

    foreach ($prices as $slug => $name) {
        if (!term_exists($slug, 'location_price')) {
            wp_insert_term($name, 'location_price', ['slug' => sanitize_title($slug)]);
        }
    }

    // Default Areas for Roxas City
    $areas = [
        'Downtown / Poblacion',
        'Baybay Beach',
        'Pueblo de Panay',
        'Culasi',
        'Libas',
        'Banica',
        'Cagay',
        'Dayao',
    ];

    foreach ($areas as $area) {
        if (!term_exists($area, 'location_area')) {
            wp_insert_term($area, 'location_area');
        }
    }

    // Default Cuisines
    $cuisines = [
        'filipino',
        'seafood',
        'bbq-grill',
        'asian',
        'japanese',
        'korean',
        'chinese',
        'indian',
        'italian',
        'american',
        'mexican',
        'fusion',
    ];

    foreach ($cuisines as $slug) {
        if (!term_exists($slug, 'location_cuisine')) {
            wp_insert_term(ucwords(str_replace('-', ' ', $slug)), 'location_cuisine', ['slug' => $slug]);
        }
    }

    // Default Vibes
    $vibes = [
        'cozy-quiet'      => __('Quiet & cozy', 'capiznon-geo'),
        'family-friendly' => __('Family-friendly', 'capiznon-geo'),
        'insta-worthy'    => __('Instagrammable views', 'capiznon-geo'),
        'lively-night-out'=> __('Night out / barkada', 'capiznon-geo'),
        'romantic'        => __('Romantic / special', 'capiznon-geo'),
    ];

    foreach ($vibes as $slug => $label) {
        if (!term_exists($slug, 'location_vibe')) {
            wp_insert_term($label, 'location_vibe', ['slug' => $slug]);
        }
    }

    // Default Tags
    $tags = [
        'wifi',
        'parking',
        'pet-friendly',
        'outdoor-seating',
        'air-conditioned',
        'accepts-credit-cards',
        'wheelchair-accessible',
        'family-friendly',
        'romantic',
        'live-music',
        'delivery',
        'takeout',
        'reservations',
        '24-hours',
        'halal',
        'vegetarian-options',
        'seafood',
        'local-cuisine',
    ];

    foreach ($tags as $tag) {
        if (!term_exists($tag, 'location_tag')) {
            wp_insert_term(ucwords(str_replace('-', ' ', $tag)), 'location_tag', ['slug' => $tag]);
        }
    }
}
add_action('after_switch_theme', 'capiznon_geo_insert_default_terms');
