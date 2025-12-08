<?php
/**
 * Template Functions
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get formatted address for a location
 */
function capiznon_geo_get_address($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    
    $parts = [];
    
    $street = get_post_meta($post_id, '_cg_address', true);
    $line2 = get_post_meta($post_id, '_cg_address_line2', true);
    $city = get_post_meta($post_id, '_cg_city', true);
    $province = get_post_meta($post_id, '_cg_province', true);
    $postal = get_post_meta($post_id, '_cg_postal_code', true);
    
    if ($street) {
        $parts[] = $street;
    }
    if ($line2) {
        $parts[] = $line2;
    }
    
    $city_line = [];
    if ($city) $city_line[] = $city;
    if ($province) $city_line[] = $province;
    if ($postal) $city_line[] = $postal;
    
    if (!empty($city_line)) {
        $parts[] = implode(', ', $city_line);
    }
    
    return implode('<br>', $parts);
}

/**
 * Get operating hours for a location
 */
function capiznon_geo_get_hours($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    
    if (get_post_meta($post_id, '_cg_temporarily_closed', true)) {
        return '<span class="status-closed">' . __('Temporarily Closed', 'capiznon-geo') . '</span>';
    }
    
    if (get_post_meta($post_id, '_cg_is_24_hours', true)) {
        return '<span class="status-open">' . __('Open 24 Hours', 'capiznon-geo') . '</span>';
    }
    
    $hours = get_post_meta($post_id, '_cg_hours', true);
    if (empty($hours)) {
        return '';
    }
    
    return $hours;
}

/**
 * Check if location is currently open
 */
function capiznon_geo_is_open($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    
    if (get_post_meta($post_id, '_cg_temporarily_closed', true)) {
        return false;
    }
    
    if (get_post_meta($post_id, '_cg_is_24_hours', true)) {
        return true;
    }
    
    $hours = get_post_meta($post_id, '_cg_hours', true);
    if (empty($hours)) {
        return null; // Unknown
    }
    
    $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    $today = strtolower(date('l'));
    $now = date('H:i');
    
    if (!isset($hours[$today])) {
        return null;
    }
    
    $day_hours = $hours[$today];
    
    if (!empty($day_hours['closed'])) {
        return false;
    }
    
    $open = $day_hours['open'] ?? '';
    $close = $day_hours['close'] ?? '';
    
    if (!$open || !$close) {
        return null;
    }
    
    return ($now >= $open && $now <= $close);
}

/**
 * Get open status badge
 */
function capiznon_geo_open_status_badge($post_id = null) {
    $is_open = capiznon_geo_is_open($post_id);
    
    if ($is_open === true) {
        return '<span class="status-badge status-open">' . __('Open', 'capiznon-geo') . '</span>';
    } elseif ($is_open === false) {
        return '<span class="status-badge status-closed">' . __('Closed', 'capiznon-geo') . '</span>';
    }
    
    return '';
}

/**
 * Get location types as a formatted string
 */
function capiznon_geo_get_types($post_id = null, $link = true) {
    $post_id = $post_id ?: get_the_ID();
    $terms = get_the_terms($post_id, 'location_type');
    
    if (!$terms || is_wp_error($terms)) {
        return '';
    }
    
    $output = [];
    foreach ($terms as $term) {
        if ($link) {
            $output[] = '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
        } else {
            $output[] = esc_html($term->name);
        }
    }
    
    return implode(', ', $output);
}

/**
 * Get location tags as badges
 */
function capiznon_geo_get_tags($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $terms = get_the_terms($post_id, 'location_tag');
    
    if (!$terms || is_wp_error($terms)) {
        return '';
    }
    
    $output = '<div class="location-tags">';
    foreach ($terms as $term) {
        $output .= '<a href="' . esc_url(get_term_link($term)) . '" class="tag-badge">' . esc_html($term->name) . '</a>';
    }
    $output .= '</div>';
    
    return $output;
}

/**
 * Get Google Maps directions URL
 */
function capiznon_geo_get_directions_url($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    
    $lat = get_post_meta($post_id, '_cg_latitude', true);
    $lng = get_post_meta($post_id, '_cg_longitude', true);
    
    if (!$lat || !$lng) {
        return '';
    }
    
    return 'https://www.google.com/maps/dir/?api=1&destination=' . $lat . ',' . $lng;
}

/**
 * Get location gallery images
 */
function capiznon_geo_get_gallery($post_id = null, $size = 'location-gallery') {
    $post_id = $post_id ?: get_the_ID();
    $gallery_ids = get_post_meta($post_id, '_cg_gallery', true);
    
    if (empty($gallery_ids)) {
        return [];
    }
    
    $images = [];
    foreach ($gallery_ids as $id) {
        $img = wp_get_attachment_image_src($id, $size);
        $full = wp_get_attachment_image_src($id, 'full');
        if ($img) {
            $images[] = [
                'id'    => $id,
                'url'   => $img[0],
                'full'  => $full ? $full[0] : $img[0],
                'alt'   => get_post_meta($id, '_wp_attachment_image_alt', true),
                'title' => get_the_title($id),
            ];
        }
    }
    
    return $images;
}

/**
 * Calculate distance between two coordinates (Haversine formula)
 */
function capiznon_geo_calculate_distance($lat1, $lng1, $lat2, $lng2) {
    $earth_radius = 6371; // km
    
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);
    $lng1 = deg2rad($lng1);
    $lng2 = deg2rad($lng2);
    
    $dlat = $lat2 - $lat1;
    $dlng = $lng2 - $lng1;
    
    $a = sin($dlat / 2) * sin($dlat / 2) +
         cos($lat1) * cos($lat2) *
         sin($dlng / 2) * sin($dlng / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    return $earth_radius * $c;
}

/**
 * Format distance for display
 */
function capiznon_geo_format_distance($km) {
    if ($km < 1) {
        return round($km * 1000) . ' m';
    }
    return round($km, 1) . ' km';
}

/**
 * Add body classes
 */
function capiznon_geo_body_classes($classes) {
    if (is_front_page()) {
        $classes[] = 'map-view';
    }
    
    if (is_singular('cg_location')) {
        $classes[] = 'single-location';
    }
    
    if (is_post_type_archive('cg_location')) {
        $classes[] = 'location-archive';
    }
    
    return $classes;
}
add_filter('body_class', 'capiznon_geo_body_classes');
