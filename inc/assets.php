<?php
/**
 * Asset Management
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Enqueue frontend scripts and styles
 */
function capiznon_geo_enqueue_assets() {
    // Tailwind CSS - Production built version
    wp_enqueue_style(
        'tailwindcss',
        CAPIZNON_GEO_URI . '/assets/css/tailwind-built.css',
        [],
        '3.4.0'
    );

    // Outfit font - Local pinned version
    wp_enqueue_style(
        'google-fonts-outfit',
        CAPIZNON_GEO_URI . '/assets/vendor/outfit.css',
        [],
        '1.0.0'
    );

    // Leaflet CSS - Local pinned version 1.9.4
    wp_enqueue_style(
        'leaflet',
        CAPIZNON_GEO_URI . '/assets/vendor/leaflet.css',
        [],
        '1.9.4'
    );

    // Leaflet MarkerCluster CSS - Local pinned version 1.4.1
    wp_enqueue_style(
        'leaflet-markercluster',
        CAPIZNON_GEO_URI . '/assets/vendor/markercluster.css',
        ['leaflet'],
        '1.4.1'
    );

    wp_enqueue_style(
        'leaflet-markercluster-default',
        CAPIZNON_GEO_URI . '/assets/vendor/markercluster.default.css',
        ['leaflet-markercluster'],
        '1.4.1'
    );

    // Theme styles (custom overrides)
    wp_enqueue_style(
        'capiznon-geo-style',
        CAPIZNON_GEO_URI . '/assets/css/main.css',
        ['leaflet', 'leaflet-markercluster'],
        CAPIZNON_GEO_VERSION
    );

    // Single location bohemian styles
    if (is_singular('cg_location')) {
        wp_enqueue_style(
            'capiznon-geo-single',
            CAPIZNON_GEO_URI . '/assets/css/single-location.css',
            ['capiznon-geo-style'],
            CAPIZNON_GEO_VERSION
        );
    }

    // Leaflet JS - Local pinned version 1.9.4
    wp_enqueue_script(
        'leaflet',
        CAPIZNON_GEO_URI . '/assets/vendor/leaflet.js',
        [],
        '1.9.4',
        true
    );

    // Leaflet MarkerCluster JS - Local pinned version 1.4.1
    wp_enqueue_script(
        'leaflet-markercluster',
        CAPIZNON_GEO_URI . '/assets/vendor/markercluster.js',
        ['leaflet'],
        '1.4.1',
        true
    );

    // Theme map script
    wp_enqueue_script(
        'capiznon-geo-map',
        CAPIZNON_GEO_URI . '/assets/js/map.js',
        ['leaflet', 'leaflet-markercluster'],
        CAPIZNON_GEO_VERSION,
        true
    );

    // Theme main script
    wp_enqueue_script(
        'capiznon-geo-main',
        CAPIZNON_GEO_URI . '/assets/js/main.js',
        ['capiznon-geo-map'],
        CAPIZNON_GEO_VERSION,
        true
    );

    // Recommender quiz (registered, enqueued when component is used)
    wp_register_script(
        'capiznon-geo-recommender-quiz',
        CAPIZNON_GEO_URI . '/assets/js/recommender-quiz.js',
        ['capiznon-geo-main'],
        CAPIZNON_GEO_VERSION,
        true
    );

    // Localize script with settings
    wp_localize_script('capiznon-geo-map', 'capiznonGeo', [
        'restUrl'    => rest_url('capiznon-geo/v1/'),
        'nonce'      => wp_create_nonce('wp_rest'),
        'defaultLat' => floatval(get_option('capiznon_geo_default_lat', CAPIZNON_GEO_DEFAULT_LAT)),
        'defaultLng' => floatval(get_option('capiznon_geo_default_lng', CAPIZNON_GEO_DEFAULT_LNG)),
        'defaultZoom' => intval(get_option('capiznon_geo_default_zoom', CAPIZNON_GEO_DEFAULT_ZOOM)),
        'mapboxToken' => get_option('capiznon_geo_mapbox_token', ''),
        'themeUrl'   => CAPIZNON_GEO_URI,
        'isLoggedIn' => is_user_logged_in(),
        'loginUrl'   => wp_login_url(home_url()),
        'strings'    => [
            'loading'            => __('Loading...', 'capiznon-geo'),
            'noResults'          => __('No locations found', 'capiznon-geo'),
            'error'              => __('Error loading locations', 'capiznon-geo'),
            'getDirections'      => __('Get Directions', 'capiznon-geo'),
            'nearby'             => __('Nearby', 'capiznon-geo'),
            'open'               => __('Open', 'capiznon-geo'),
            'closed'             => __('Closed', 'capiznon-geo'),
            'kmAway'             => __('%s km away', 'capiznon-geo'),
            'recordVisit'        => __('Record Visit', 'capiznon-geo'),
            'loginToVisit'       => __('Login to record visits', 'capiznon-geo'),
            'visitRecorded'      => __('Visit recorded!', 'capiznon-geo'),
            'food'               => __('Food', 'capiznon-geo'),
            'service'            => __('Service', 'capiznon-geo'),
            'today'              => __('Today', 'capiznon-geo'),
            'save'               => __('Save Visit', 'capiznon-geo'),
            'cancel'             => __('Cancel', 'capiznon-geo'),
            'myVisits'           => __('My visits here', 'capiznon-geo'),
            'noVisitsYetMy'      => __('You have no visits recorded here yet.', 'capiznon-geo'),
            'loadingYourVisits'  => __('Loading your visits…', 'capiznon-geo'),
            'visitsCountLabel'   => __('visits', 'capiznon-geo'),
            'lastVisit'          => __('Last visit', 'capiznon-geo'),
            'foodShort'          => __('Food', 'capiznon-geo'),
            'serviceShort'       => __('Service', 'capiznon-geo'),
            'addPlace'           => __('Add place here', 'capiznon-geo'),
            'addMyLocation'      => __('Add my location', 'capiznon-geo'),
            'addLocationTitle'   => __('Add a place', 'capiznon-geo'),
            'addLocationSaved'   => __('Place added successfully.', 'capiznon-geo'),
            'addLocationSavedPending' => __('Place submitted for review. It will appear on the map once approved.', 'capiznon-geo'),
            'addLocationHelper'  => __('Depending on your role, new places may need to be reviewed before they appear on the map.', 'capiznon-geo'),
            'addLocationGeoErrorTitle' => __('Location access required', 'capiznon-geo'),
            'addLocationGeoErrorBody'  => __('We could not access your current location. Please enable location permissions in your browser settings, or add a place by tapping on the map instead.', 'capiznon-geo'),
            'addLocationTitleRequired' => __('Please enter a place name.', 'capiznon-geo'),
            'addLocationTypeRequired'  => __('Please choose a location type.', 'capiznon-geo'),
        ],
    ]);
}
add_action('wp_enqueue_scripts', 'capiznon_geo_enqueue_assets');

/**
 * Enqueue admin scripts and styles
 */
function capiznon_geo_admin_assets($hook) {
    global $post_type;

    // Only load on location edit screens
    if ($post_type !== 'cg_location') {
        return;
    }

    // Leaflet for map picker - Local pinned version 1.9.4
    wp_enqueue_style(
        'leaflet',
        CAPIZNON_GEO_URI . '/assets/vendor/leaflet.css',
        [],
        '1.9.4'
    );

    wp_enqueue_script(
        'leaflet',
        CAPIZNON_GEO_URI . '/assets/vendor/leaflet.js',
        [],
        '1.9.4',
        true
    );

    // Admin styles
    wp_enqueue_style(
        'capiznon-geo-admin',
        CAPIZNON_GEO_URI . '/assets/css/admin.css',
        ['leaflet'],
        CAPIZNON_GEO_VERSION
    );

    // Media uploader for gallery
    wp_enqueue_media();

    // Admin scripts
    wp_enqueue_script(
        'capiznon-geo-admin',
        CAPIZNON_GEO_URI . '/assets/js/admin.js',
        ['jquery', 'leaflet'],
        CAPIZNON_GEO_VERSION,
        true
    );

    wp_localize_script('capiznon-geo-admin', 'capiznonGeoAdmin', [
        'defaultLat' => floatval(get_option('capiznon_geo_default_lat', CAPIZNON_GEO_DEFAULT_LAT)),
        'defaultLng' => floatval(get_option('capiznon_geo_default_lng', CAPIZNON_GEO_DEFAULT_LNG)),
        'defaultZoom' => intval(get_option('capiznon_geo_default_zoom', CAPIZNON_GEO_DEFAULT_ZOOM)),
        'strings' => [
            'selectImages' => __('Select Images', 'capiznon-geo'),
            'useImages'    => __('Add to Gallery', 'capiznon-geo'),
        ],
    ]);
}
add_action('admin_enqueue_scripts', 'capiznon_geo_admin_assets');

/**
 * Add preconnect for external resources
 */
function capiznon_geo_resource_hints($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        // Only keep OpenStreetMap tiles preconnect since maps still use external tiles
        $urls[] = [
            'href' => 'https://tile.openstreetmap.org',
            'crossorigin' => true,
        ];
    }
    return $urls;
}
add_filter('wp_resource_hints', 'capiznon_geo_resource_hints', 10, 2);
