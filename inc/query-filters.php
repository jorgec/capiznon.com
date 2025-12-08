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

    // Handle Tag/Amenities (support both ?amenity= and legacy ?tag= to avoid WP post_tag conflicts)
    $tag = null;
    if (isset($_GET['amenity']) && !empty($_GET['amenity'])) {
        $tag = sanitize_text_field($_GET['amenity']);
    } elseif (isset($_GET['tag']) && !empty($_GET['tag'])) {
        // Map the WP reserved "tag" query var to our custom taxonomy
        $tag = sanitize_text_field($_GET['tag']);
        $query->set('tag', '');
        $query->set('tag_id', '');
    }

    if ($tag) {
        $tax_query[] = [
            'taxonomy' => 'location_tag',
            'field'    => 'slug',
            'terms'    => $tag,
        ];
        $has_tax_changes = true;
    }

    // Handle Price (support symbol params and map to actual slug)
    if (isset($_GET['price']) && !empty($_GET['price'])) {
        // Pass raw value - resolve function handles decoding
        $price_slug = capiznon_geo_resolve_price_slug($_GET['price']);
        $tax_query[] = [
            'taxonomy' => 'location_price',
            'field'    => 'slug',
            'terms'    => $price_slug,
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
 * Map a price query param (possibly encoded symbol) to a valid slug
 */
function capiznon_geo_resolve_price_slug($value) {
    if (empty($value)) {
        return $value;
    }

    // Fully decode - handle multiple levels of URL encoding
    $decoded = $value;
    $max_iterations = 5;
    for ($i = 0; $i < $max_iterations; $i++) {
        $next = rawurldecode($decoded);
        if ($next === $decoded) break;
        $decoded = $next;
    }
    
    $symbol_count = preg_match_all('/₱/u', $decoded, $matches);
    
    // Build candidates from various decode/sanitize combinations
    $candidates = array_unique(array_filter([
        $value,
        $decoded,
        sanitize_title($decoded),
        sanitize_text_field($decoded),
    ]));

    $terms = get_terms([
        'taxonomy'   => 'location_price',
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return $value;
    }

    if (function_exists('capiznon_geo_order_price_terms')) {
        $terms = capiznon_geo_order_price_terms($terms);
    }

    foreach ($terms as $term) {
        $term_symbol_count = preg_match_all('/₱/u', $term->name, $matches);
        if ($term_symbol_count === 0 && preg_match('/^p+$/i', $term->slug)) {
            $term_symbol_count = strlen($term->slug);
        }

        foreach ($candidates as $candidate) {
            // Direct slug match
            if ($term->slug === $candidate) {
                return $term->slug;
            }
            // Name match (in case the param is the label)
            if (strcasecmp($term->name, $candidate) === 0) {
                return $term->slug;
            }
            // Symbol-only match (e.g., ₱₱)
            if ($symbol_count > 0 && $term_symbol_count === $symbol_count) {
                return $term->slug;
            }
        }
    }

    // If the param was symbol-based, map to the nth price tier after ordering
    if ($symbol_count > 0) {
        $index = min(count($terms) - 1, max(0, $symbol_count - 1));
        if (isset($terms[$index])) {
            return $terms[$index]->slug;
        }
    }

    // Fallback to sanitized decoded value
    $fallback = sanitize_title($decoded);
    
    // Debug logging (remove in production)
    error_log("capiznon_geo_resolve_price_slug: value=$value, decoded=$decoded, symbol_count=$symbol_count, fallback=$fallback");
    
    return $fallback;
}

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
