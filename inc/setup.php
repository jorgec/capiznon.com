<?php
/**
 * Theme Setup
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function capiznon_geo_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Add custom image sizes for locations
    add_image_size('location-card', 400, 300, true);
    add_image_size('location-gallery', 800, 600, true);
    add_image_size('location-hero', 1200, 600, true);

    // Register navigation menus
    register_nav_menus([
        'primary'   => __('Primary Menu', 'capiznon-geo'),
        'footer'    => __('Footer Menu', 'capiznon-geo'),
        'mobile'    => __('Mobile Menu', 'capiznon-geo'),
    ]);

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Add support for custom logo
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for wide and full alignment
    add_theme_support('align-wide');

    // Add support for editor styles
    add_theme_support('editor-styles');
}
add_action('after_setup_theme', 'capiznon_geo_setup');

/**
 * Register widget areas
 */
function capiznon_geo_widgets_init() {
    register_sidebar([
        'name'          => __('Sidebar', 'capiznon-geo'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in the sidebar.', 'capiznon-geo'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('Map Sidebar', 'capiznon-geo'),
        'id'            => 'map-sidebar',
        'description'   => __('Widgets displayed alongside the map.', 'capiznon-geo'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('Footer', 'capiznon-geo'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in the footer.', 'capiznon-geo'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ]);
}
add_action('widgets_init', 'capiznon_geo_widgets_init');

/**
 * Create default menus on theme activation
 */
function capiznon_geo_create_default_menus() {
    // Check if primary menu already exists
    $menu_name = 'Main Menu';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        // Create the menu
        $menu_id = wp_create_nav_menu($menu_name);
        
        if (!is_wp_error($menu_id)) {
            // Add menu items
            
            // Home
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'   => __('Home', 'capiznon-geo'),
                'menu-item-url'     => home_url('/'),
                'menu-item-status'  => 'publish',
                'menu-item-type'    => 'custom',
            ]);
            
            // Explore Map
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'   => __('Explore', 'capiznon-geo'),
                'menu-item-url'     => home_url('/'),
                'menu-item-status'  => 'publish',
                'menu-item-type'    => 'custom',
            ]);
            
            // All Locations
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'   => __('All Places', 'capiznon-geo'),
                'menu-item-url'     => get_post_type_archive_link('cg_location'),
                'menu-item-status'  => 'publish',
                'menu-item-type'    => 'custom',
            ]);
            
            // Food & Dining
            $food_term = get_term_by('slug', 'food-dining', 'location_type');
            if ($food_term) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title'     => __('Food & Dining', 'capiznon-geo'),
                    'menu-item-object'    => 'location_type',
                    'menu-item-object-id' => $food_term->term_id,
                    'menu-item-type'      => 'taxonomy',
                    'menu-item-status'    => 'publish',
                ]);
            }
            
            // Attractions
            $attractions_term = get_term_by('slug', 'attractions', 'location_type');
            if ($attractions_term) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title'     => __('Attractions', 'capiznon-geo'),
                    'menu-item-object'    => 'location_type',
                    'menu-item-object-id' => $attractions_term->term_id,
                    'menu-item-type'      => 'taxonomy',
                    'menu-item-status'    => 'publish',
                ]);
            }
            
            // About (link to a page if exists, otherwise custom)
            $about_page = get_page_by_path('about');
            if ($about_page) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title'     => __('About', 'capiznon-geo'),
                    'menu-item-object'    => 'page',
                    'menu-item-object-id' => $about_page->ID,
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                ]);
            }
            
            // Assign menu to locations
            $locations = get_theme_mod('nav_menu_locations');
            $locations['primary'] = $menu_id;
            $locations['footer'] = $menu_id;
            $locations['mobile'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
}
add_action('after_switch_theme', 'capiznon_geo_create_default_menus');

/**
 * Add theme options page
 */
function capiznon_geo_add_options_page() {
    add_theme_page(
        __('Capiznon Geo Settings', 'capiznon-geo'),
        __('Geo Settings', 'capiznon-geo'),
        'manage_options',
        'capiznon-geo-settings',
        'capiznon_geo_options_page'
    );
}
add_action('admin_menu', 'capiznon_geo_add_options_page');

/**
 * Register theme settings
 */
function capiznon_geo_register_settings() {
    register_setting('capiznon_geo_options', 'capiznon_geo_default_lat', [
        'type'              => 'number',
        'default'           => CAPIZNON_GEO_DEFAULT_LAT,
        'sanitize_callback' => 'floatval',
    ]);

    register_setting('capiznon_geo_options', 'capiznon_geo_default_lng', [
        'type'              => 'number',
        'default'           => CAPIZNON_GEO_DEFAULT_LNG,
        'sanitize_callback' => 'floatval',
    ]);

    register_setting('capiznon_geo_options', 'capiznon_geo_default_zoom', [
        'type'              => 'integer',
        'default'           => CAPIZNON_GEO_DEFAULT_ZOOM,
        'sanitize_callback' => 'absint',
    ]);

    register_setting('capiznon_geo_options', 'capiznon_geo_mapbox_token', [
        'type'              => 'string',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
}
add_action('admin_init', 'capiznon_geo_register_settings');

/**
 * Options page HTML
 */
function capiznon_geo_options_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('capiznon_geo_options');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="capiznon_geo_default_lat"><?php _e('Default Latitude', 'capiznon-geo'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="capiznon_geo_default_lat" name="capiznon_geo_default_lat" 
                               value="<?php echo esc_attr(get_option('capiznon_geo_default_lat', CAPIZNON_GEO_DEFAULT_LAT)); ?>" 
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="capiznon_geo_default_lng"><?php _e('Default Longitude', 'capiznon-geo'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="capiznon_geo_default_lng" name="capiznon_geo_default_lng" 
                               value="<?php echo esc_attr(get_option('capiznon_geo_default_lng', CAPIZNON_GEO_DEFAULT_LNG)); ?>" 
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="capiznon_geo_default_zoom"><?php _e('Default Zoom Level', 'capiznon-geo'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="capiznon_geo_default_zoom" name="capiznon_geo_default_zoom" 
                               value="<?php echo esc_attr(get_option('capiznon_geo_default_zoom', CAPIZNON_GEO_DEFAULT_ZOOM)); ?>" 
                               min="1" max="20" class="small-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="capiznon_geo_mapbox_token"><?php _e('Mapbox Access Token', 'capiznon-geo'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="capiznon_geo_mapbox_token" name="capiznon_geo_mapbox_token" 
                               value="<?php echo esc_attr(get_option('capiznon_geo_mapbox_token', '')); ?>" 
                               class="large-text">
                        <p class="description">
                            <?php _e('Optional. For custom map styles. Get one at <a href="https://mapbox.com" target="_blank">mapbox.com</a>', 'capiznon-geo'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
