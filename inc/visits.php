<?php
/**
 * Location Visits Functionality
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Create visits table on theme activation
 */
function capiznon_geo_create_visits_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'cg_visits';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        location_id bigint(20) unsigned NOT NULL,
        visit_date date NOT NULL,
        food_rating tinyint(1) unsigned DEFAULT NULL,
        service_rating tinyint(1) unsigned DEFAULT NULL,
        notes text DEFAULT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY location_id (location_id),
        KEY visit_date (visit_date),
        UNIQUE KEY user_location_date (user_id, location_id, visit_date)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'capiznon_geo_create_visits_table');

/**
 * Also create table on init if it doesn't exist (for development)
 */
function capiznon_geo_maybe_create_visits_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cg_visits';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        capiznon_geo_create_visits_table();
    }
}
add_action('init', 'capiznon_geo_maybe_create_visits_table', 1);

/**
 * Register visits REST API endpoints
 */
function capiznon_geo_register_visits_routes() {
    $namespace = 'capiznon-geo/v1';
    
    // Create a visit
    register_rest_route($namespace, '/visits', [
        'methods'             => 'POST',
        'callback'            => 'capiznon_geo_create_visit',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'args'                => [
            'location_id' => [
                'required'          => true,
                'type'              => 'integer',
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
            ],
            'visit_date' => [
                'required'          => true,
                'type'              => 'string',
                'validate_callback' => function($param) {
                    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $param);
                },
            ],
            'food_rating' => [
                'type'              => 'integer',
                'validate_callback' => function($param) {
                    return $param === null || ($param >= 1 && $param <= 5);
                },
            ],
            'service_rating' => [
                'type'              => 'integer',
                'validate_callback' => function($param) {
                    return $param === null || ($param >= 1 && $param <= 5);
                },
            ],
            'notes' => [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
            ],
        ],
    ]);
    
    // Get visits for a location (current user's visits)
    register_rest_route($namespace, '/visits/location/(?P<location_id>\d+)', [
        'methods'             => 'GET',
        'callback'            => 'capiznon_geo_get_location_visits',
        'permission_callback' => '__return_true',
        'args'                => [
            'location_id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ],
        ],
    ]);
    
    // Get user's visits
    register_rest_route($namespace, '/visits/user', [
        'methods'             => 'GET',
        'callback'            => 'capiznon_geo_get_user_visits',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
    ]);
    
    // Delete a visit
    register_rest_route($namespace, '/visits/(?P<id>\d+)', [
        'methods'             => 'DELETE',
        'callback'            => 'capiznon_geo_delete_visit',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'args'                => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ],
        ],
    ]);
    
    // Update a visit
    register_rest_route($namespace, '/visits/(?P<id>\d+)', [
        'methods'             => 'PUT',
        'callback'            => 'capiznon_geo_update_visit',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'args'                => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ],
            'food_rating' => [
                'type'              => 'integer',
                'validate_callback' => function($param) {
                    return $param === null || ($param >= 1 && $param <= 5);
                },
            ],
            'service_rating' => [
                'type'              => 'integer',
                'validate_callback' => function($param) {
                    return $param === null || ($param >= 1 && $param <= 5);
                },
            ],
            'notes' => [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
            ],
        ],
    ]);
}
add_action('rest_api_init', 'capiznon_geo_register_visits_routes');

/**
 * Create a new visit
 */
function capiznon_geo_create_visit($request) {
    global $wpdb;
    
    $user_id = get_current_user_id();
    $location_id = absint($request->get_param('location_id'));
    $visit_date = sanitize_text_field($request->get_param('visit_date'));
    $food_rating = $request->get_param('food_rating');
    $service_rating = $request->get_param('service_rating');
    $notes = $request->get_param('notes');
    
    // Verify location exists
    $location = get_post($location_id);
    if (!$location || $location->post_type !== 'cg_location') {
        return new WP_Error('invalid_location', __('Location not found', 'capiznon-geo'), ['status' => 404]);
    }
    
    $table_name = $wpdb->prefix . 'cg_visits';
    
    // Check if visit already exists for this date
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE user_id = %d AND location_id = %d AND visit_date = %s",
        $user_id, $location_id, $visit_date
    ));
    
    if ($existing) {
        // Update existing visit
        $result = $wpdb->update(
            $table_name,
            [
                'food_rating'    => $food_rating,
                'service_rating' => $service_rating,
                'notes'          => $notes,
            ],
            [
                'id' => $existing,
            ],
            ['%d', '%d', '%s'],
            ['%d']
        );
        
        if ($result === false) {
            return new WP_Error('db_error', __('Failed to update visit', 'capiznon-geo'), ['status' => 500]);
        }
        
        $visit_id = $existing;
    } else {
        // Insert new visit
        $result = $wpdb->insert(
            $table_name,
            [
                'user_id'        => $user_id,
                'location_id'    => $location_id,
                'visit_date'     => $visit_date,
                'food_rating'    => $food_rating,
                'service_rating' => $service_rating,
                'notes'          => $notes,
            ],
            ['%d', '%d', '%s', '%d', '%d', '%s']
        );
        
        if ($result === false) {
            return new WP_Error('db_error', __('Failed to create visit', 'capiznon-geo'), ['status' => 500]);
        }
        
        $visit_id = $wpdb->insert_id;
    }
    
    // Get the visit data
    $visit = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $visit_id
    ));
    
    return rest_ensure_response([
        'success' => true,
        'visit'   => capiznon_geo_format_visit($visit),
        'message' => $existing ? __('Visit updated', 'capiznon-geo') : __('Visit recorded', 'capiznon-geo'),
    ]);
}

/**
 * Get visits for a location
 */
function capiznon_geo_get_location_visits($request) {
    global $wpdb;
    
    $location_id = absint($request['location_id']);
    $table_name = $wpdb->prefix . 'cg_visits';
    
    // Get aggregate stats for the location
    $stats = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            COUNT(*) as total_visits,
            AVG(food_rating) as avg_food_rating,
            AVG(service_rating) as avg_service_rating,
            COUNT(DISTINCT user_id) as unique_visitors
        FROM $table_name 
        WHERE location_id = %d",
        $location_id
    ));
    
    // Get current user's visits if logged in
    $user_visits = [];
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $visits = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE location_id = %d AND user_id = %d ORDER BY visit_date DESC",
            $location_id, $user_id
        ));
        
        foreach ($visits as $visit) {
            $user_visits[] = capiznon_geo_format_visit($visit);
        }
    }
    
    return rest_ensure_response([
        'location_id'        => $location_id,
        'total_visits'       => (int) $stats->total_visits,
        'unique_visitors'    => (int) $stats->unique_visitors,
        'avg_food_rating'    => $stats->avg_food_rating ? round((float) $stats->avg_food_rating, 1) : null,
        'avg_service_rating' => $stats->avg_service_rating ? round((float) $stats->avg_service_rating, 1) : null,
        'user_visits'        => $user_visits,
    ]);
}

/**
 * Get current user's visits
 */
function capiznon_geo_get_user_visits($request) {
    global $wpdb;
    
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'cg_visits';
    
    $visits = $wpdb->get_results($wpdb->prepare(
        "SELECT v.*, p.post_title as location_title 
        FROM $table_name v
        LEFT JOIN {$wpdb->posts} p ON v.location_id = p.ID
        WHERE v.user_id = %d 
        ORDER BY v.visit_date DESC",
        $user_id
    ));
    
    $formatted = [];
    foreach ($visits as $visit) {
        $data = capiznon_geo_format_visit($visit);
        $data['location_title'] = $visit->location_title;
        $data['location_url'] = get_permalink($visit->location_id);
        $formatted[] = $data;
    }
    
    return rest_ensure_response([
        'visits' => $formatted,
        'total'  => count($formatted),
    ]);
}

/**
 * Delete a visit
 */
function capiznon_geo_delete_visit($request) {
    global $wpdb;
    
    $visit_id = absint($request['id']);
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'cg_visits';
    
    // Verify ownership
    $visit = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
        $visit_id, $user_id
    ));
    
    if (!$visit) {
        return new WP_Error('not_found', __('Visit not found', 'capiznon-geo'), ['status' => 404]);
    }
    
    $result = $wpdb->delete($table_name, ['id' => $visit_id], ['%d']);
    
    if ($result === false) {
        return new WP_Error('db_error', __('Failed to delete visit', 'capiznon-geo'), ['status' => 500]);
    }
    
    return rest_ensure_response([
        'success' => true,
        'message' => __('Visit deleted', 'capiznon-geo'),
    ]);
}

/**
 * Update a visit
 */
function capiznon_geo_update_visit($request) {
    global $wpdb;
    
    $visit_id = absint($request['id']);
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'cg_visits';
    
    // Verify ownership
    $visit = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
        $visit_id, $user_id
    ));
    
    if (!$visit) {
        return new WP_Error('not_found', __('Visit not found', 'capiznon-geo'), ['status' => 404]);
    }
    
    $update_data = [];
    $update_format = [];
    
    if ($request->has_param('food_rating')) {
        $update_data['food_rating'] = $request->get_param('food_rating');
        $update_format[] = '%d';
    }
    
    if ($request->has_param('service_rating')) {
        $update_data['service_rating'] = $request->get_param('service_rating');
        $update_format[] = '%d';
    }
    
    if ($request->has_param('notes')) {
        $update_data['notes'] = $request->get_param('notes');
        $update_format[] = '%s';
    }
    
    if (empty($update_data)) {
        return new WP_Error('no_data', __('No data to update', 'capiznon-geo'), ['status' => 400]);
    }
    
    $result = $wpdb->update($table_name, $update_data, ['id' => $visit_id], $update_format, ['%d']);
    
    if ($result === false) {
        return new WP_Error('db_error', __('Failed to update visit', 'capiznon-geo'), ['status' => 500]);
    }
    
    // Get updated visit
    $updated = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $visit_id
    ));
    
    return rest_ensure_response([
        'success' => true,
        'visit'   => capiznon_geo_format_visit($updated),
        'message' => __('Visit updated', 'capiznon-geo'),
    ]);
}

/**
 * Format visit data for API response
 */
function capiznon_geo_format_visit($visit) {
    return [
        'id'             => (int) $visit->id,
        'user_id'        => (int) $visit->user_id,
        'location_id'    => (int) $visit->location_id,
        'visit_date'     => $visit->visit_date,
        'food_rating'    => $visit->food_rating ? (int) $visit->food_rating : null,
        'service_rating' => $visit->service_rating ? (int) $visit->service_rating : null,
        'notes'          => $visit->notes,
        'created_at'     => $visit->created_at,
        'updated_at'     => $visit->updated_at,
    ];
}
