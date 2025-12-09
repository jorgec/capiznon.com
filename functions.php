<?php
/**
 * Capiznon Geo Theme Functions
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Theme constants
define('CAPIZNON_GEO_VERSION', '1.0.0');
define('CAPIZNON_GEO_DIR', get_template_directory());
define('CAPIZNON_GEO_URI', get_template_directory_uri());

// Default map center (Roxas City, Capiz, Philippines)
define('CAPIZNON_GEO_DEFAULT_LAT', 11.5853);
define('CAPIZNON_GEO_DEFAULT_LNG', 122.7511);
define('CAPIZNON_GEO_DEFAULT_ZOOM', 14);

/**
 * Autoload theme classes
 */
spl_autoload_register(function ($class) {
    $prefix = 'Capiznon_Geo\\';
    $base_dir = CAPIZNON_GEO_DIR . '/inc/classes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . strtolower(str_replace('_', '-', $relative_class)) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Include required files
 */
require_once CAPIZNON_GEO_DIR . '/inc/setup.php';
require_once CAPIZNON_GEO_DIR . '/inc/post-types.php';
require_once CAPIZNON_GEO_DIR . '/inc/taxonomies.php';
require_once CAPIZNON_GEO_DIR . '/inc/meta-boxes.php';
require_once CAPIZNON_GEO_DIR . '/inc/rest-api.php';
require_once CAPIZNON_GEO_DIR . '/inc/recommender.php';
require_once CAPIZNON_GEO_DIR . '/inc/template-functions.php';
require_once CAPIZNON_GEO_DIR . '/inc/assets.php';
require_once CAPIZNON_GEO_DIR . '/inc/visits.php';
require_once CAPIZNON_GEO_DIR . '/inc/scaffold.php';
require_once CAPIZNON_GEO_DIR . '/inc/query-filters.php';

/**
 * Show admin notice for pending locations
 */
function capiznon_geo_pending_locations_notice() {
    if (!is_admin()) return;

    if (!current_user_can('edit_posts')) {
        return;
    }

    $pending = get_posts([
        'post_type'      => 'cg_location',
        'post_status'    => 'pending',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    if (empty($pending)) {
        return;
    }

    // Get full count efficiently
    $counts = wp_count_posts('cg_location');
    $pending_count = isset($counts->pending) ? (int) $counts->pending : 0;
    if ($pending_count <= 0) {
        return;
    }

    $url = admin_url('edit.php?post_type=cg_location&post_status=pending');
    ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <?php
            printf(
                /* translators: %d: number of pending locations */
                esc_html__('%d location(s) are awaiting review. %s', 'capiznon-geo'),
                $pending_count,
                sprintf(
                    '<a href="%s">%s</a>',
                    esc_url($url),
                    esc_html__('Review pending locations', 'capiznon-geo')
                )
            );
            ?>
        </p>
    </div>
    <?php
}
add_action('admin_notices', 'capiznon_geo_pending_locations_notice');

function capiznon_geo_location_custom_column($column, $post_id) {
    if ($column === 'cg_pending_frontend') {
        $post = get_post($post_id);
        if ($post && $post->post_status === 'pending' && get_post_meta($post_id, '_cg_created_via_frontend', true)) {
            echo '<span class="cg-status-badge cg-status-pending-frontend">' . esc_html__('Pending (frontend)', 'capiznon-geo') . '</span>';
        }
    }
}
add_action('manage_cg_location_posts_custom_column', 'capiznon_geo_location_custom_column', 10, 2);
