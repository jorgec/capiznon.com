<?php
/**
 * Custom Post Types
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Register Location custom post type
 */
function capiznon_geo_register_post_types() {
    $labels = [
        'name'                  => _x('Locations', 'Post type general name', 'capiznon-geo'),
        'singular_name'         => _x('Location', 'Post type singular name', 'capiznon-geo'),
        'menu_name'             => _x('Locations', 'Admin Menu text', 'capiznon-geo'),
        'name_admin_bar'        => _x('Location', 'Add New on Toolbar', 'capiznon-geo'),
        'add_new'               => __('Add New', 'capiznon-geo'),
        'add_new_item'          => __('Add New Location', 'capiznon-geo'),
        'new_item'              => __('New Location', 'capiznon-geo'),
        'edit_item'             => __('Edit Location', 'capiznon-geo'),
        'view_item'             => __('View Location', 'capiznon-geo'),
        'all_items'             => __('All Locations', 'capiznon-geo'),
        'search_items'          => __('Search Locations', 'capiznon-geo'),
        'parent_item_colon'     => __('Parent Locations:', 'capiznon-geo'),
        'not_found'             => __('No locations found.', 'capiznon-geo'),
        'not_found_in_trash'    => __('No locations found in Trash.', 'capiznon-geo'),
        'featured_image'        => _x('Location Cover Image', 'Overrides the "Featured Image" phrase', 'capiznon-geo'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'capiznon-geo'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'capiznon-geo'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase', 'capiznon-geo'),
        'archives'              => _x('Location archives', 'The post type archive label', 'capiznon-geo'),
        'insert_into_item'      => _x('Insert into location', 'Overrides the "Insert into post" phrase', 'capiznon-geo'),
        'uploaded_to_this_item' => _x('Uploaded to this location', 'Overrides the "Uploaded to this post" phrase', 'capiznon-geo'),
        'filter_items_list'     => _x('Filter locations list', 'Screen reader text', 'capiznon-geo'),
        'items_list_navigation' => _x('Locations list navigation', 'Screen reader text', 'capiznon-geo'),
        'items_list'            => _x('Locations list', 'Screen reader text', 'capiznon-geo'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'location', 'with_front' => false],
        'capability_type'    => 'post',
        'has_archive'        => 'locations',
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-location',
        'supports'           => [
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'comments',
            'revisions',
            'custom-fields',
        ],
        'show_in_rest'       => true,
        'rest_base'          => 'locations',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    ];

    register_post_type('cg_location', $args);
}
add_action('init', 'capiznon_geo_register_post_types');

/**
 * Flush rewrite rules on theme activation
 */
function capiznon_geo_rewrite_flush() {
    capiznon_geo_register_post_types();
    capiznon_geo_register_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'capiznon_geo_rewrite_flush');

/**
 * Add custom columns to locations list
 */
function capiznon_geo_location_columns($columns) {
    $new_columns = [];
    
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['location_type'] = __('Type', 'capiznon-geo');
            $new_columns['location_coords'] = __('Coordinates', 'capiznon-geo');
        } elseif ($key === 'date') {
            $new_columns['location_featured'] = __('Featured', 'capiznon-geo');
            $new_columns['cg_pending_frontend'] = __('Status', 'capiznon-geo');
            $new_columns[$key] = $value;
        } else {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
add_filter('manage_cg_location_posts_columns', 'capiznon_geo_location_columns');

/**
 * Populate custom columns
 */
function capiznon_geo_location_column_content($column, $post_id) {
    switch ($column) {
        case 'location_type':
            $terms = get_the_terms($post_id, 'location_type');
            if ($terms && !is_wp_error($terms)) {
                $type_names = wp_list_pluck($terms, 'name');
                echo esc_html(implode(', ', $type_names));
            } else {
                echo '—';
            }
            break;

        case 'location_coords':
            $lat = get_post_meta($post_id, '_cg_latitude', true);
            $lng = get_post_meta($post_id, '_cg_longitude', true);
            if ($lat && $lng) {
                echo esc_html(number_format((float)$lat, 6) . ', ' . number_format((float)$lng, 6));
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;

        case 'location_featured':
            $featured = get_post_meta($post_id, '_cg_featured', true);
            echo $featured ? '★' : '—';
            break;
    }
}
add_action('manage_cg_location_posts_custom_column', 'capiznon_geo_location_column_content', 10, 2);

/**
 * Make columns sortable
 */
function capiznon_geo_sortable_columns($columns) {
    $columns['location_featured'] = 'location_featured';
    return $columns;
}
add_filter('manage_edit-cg_location_sortable_columns', 'capiznon_geo_sortable_columns');
