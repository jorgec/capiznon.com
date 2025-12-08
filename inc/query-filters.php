<?php
/**
 * Query Filters for Location Archive
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Filter the location archive query based on URL parameters
 */
function capiznon_geo_filter_archive_query($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if (!is_post_type_archive('cg_location') && !is_tax(['location_type', 'location_area', 'location_tag', 'location_price', 'location_cuisine'])) {
        return;
    }

    // Handle search parameter
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $query->set('s', sanitize_text_field($_GET['search']));
    }

    $tax_query = $query->get('tax_query') ?: [];
    $meta_query = $query->get('meta_query') ?: [];
    $has_tax_changes = false;
    $has_meta_changes = false;

    // Handle Location Type
    if (isset($_GET['type']) && !empty($_GET['type'])) {
        $type = sanitize_text_field($_GET['type']);
        $tax_query[] = [
            'taxonomy' => 'location_type',
            'field'    => 'slug',
            'terms'    => $type,
        ];
        $has_tax_changes = true;
    }

    // Handle Area
    if (isset($_GET['area']) && !empty($_GET['area'])) {
        $area = sanitize_text_field($_GET['area']);
        $tax_query[] = [
            'taxonomy' => 'location_area',
            'field'    => 'slug',
            'terms'    => $area,
        ];
        $has_tax_changes = true;
    }

    // Handle Tag/Amenities
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
        $tag = sanitize_text_field($_GET['tag']);
        $tax_query[] = [
            'taxonomy' => 'location_tag',
            'field'    => 'slug',
            'terms'    => $tag,
        ];
        $has_tax_changes = true;
    }

    // Handle Price
    if (isset($_GET['price']) && !empty($_GET['price'])) {
        $price = sanitize_text_field($_GET['price']);
        $tax_query[] = [
            'taxonomy' => 'location_price',
            'field'    => 'slug',
            'terms'    => $price,
        ];
        $has_tax_changes = true;
    }

    // Handle Cuisine
    if (isset($_GET['cuisine']) && !empty($_GET['cuisine'])) {
        $cuisine = sanitize_text_field($_GET['cuisine']);
        $tax_query[] = [
            'taxonomy' => 'location_cuisine',
            'field'    => 'slug',
            'terms'    => $cuisine,
        ];
        $has_tax_changes = true;
    }

    // Handle Featured
    if (isset($_GET['featured']) && $_GET['featured'] === '1') {
        $meta_query[] = [
            'key'     => '_cg_featured',
            'value'   => '1',
            'compare' => '=',
        ];
        $has_meta_changes = true;
    }

    if ($has_tax_changes) {
        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }
        $query->set('tax_query', $tax_query);
    }

    if ($has_meta_changes) {
        if (count($meta_query) > 1) {
            $meta_query['relation'] = 'AND';
        }
        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'capiznon_geo_filter_archive_query');

/**
 * Force archive template for location searches
 */
function capiznon_geo_force_location_template($template) {
    if (is_search() && get_query_var('post_type') === 'cg_location') {
        $archive_template = locate_template('archive-cg_location.php');
        if ($archive_template) {
            return $archive_template;
        }
    }
    return $template;
}
add_filter('template_include', 'capiznon_geo_force_location_template');
