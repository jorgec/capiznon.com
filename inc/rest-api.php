<?php
/**
 * REST API Endpoints
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Register custom REST API endpoints
 */
function capiznon_geo_register_rest_routes() {
    $namespace = 'capiznon-geo/v1';

    // Get all locations for map
    register_rest_route($namespace, '/locations', [
        'methods'             => 'GET',
        'callback'            => 'capiznon_geo_get_locations',
        'permission_callback' => '__return_true',
        'args'                => [
            'type'    => [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'area'    => [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'tag'     => [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'price'   => [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'cuisine' => [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'search'  => [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'featured' => [
                'type'              => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
            ],
        ],
    ]);

    // Get single location
    register_rest_route($namespace, '/locations/(?P<id>\d+)', [
        'methods'             => 'GET',
        'callback'            => 'capiznon_geo_get_location',
        'permission_callback' => '__return_true',
        'args'                => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ],
        ],
    ]);

    // Get nearby locations
    register_rest_route($namespace, '/locations/nearby', [
        'methods'             => 'GET',
        'callback'            => 'capiznon_geo_get_nearby_locations',
        'permission_callback' => '__return_true',
        'args'                => [
            'lat' => [
                'required'          => true,
                'type'              => 'number',
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ],
            'lng' => [
                'required'          => true,
                'type'              => 'number',
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ],
            'radius' => [
                'type'              => 'number',
                'default'           => 1,
            ],
            'exclude' => [
                'type'              => 'integer',
            ],
            'type' => [
                'type'              => 'string',
            ],
            'limit' => [
                'type'              => 'integer',
                'default'           => 10,
            ],
        ],
    ]);

    // Get filter options (types, areas, tags)
    register_rest_route($namespace, '/filters', [
        'methods'             => 'GET',
        'callback'            => 'capiznon_geo_get_filters',
        'permission_callback' => '__return_true',
    ]);

    // Create new location
    register_rest_route($namespace, '/locations', [
        'methods'             => 'POST',
        'callback'            => 'capiznon_geo_create_location',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'args'                => [
            'title' => [
                'type'              => 'string',
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'type'  => [
                'type'              => 'string',
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'lat'   => [
                'type'              => 'number',
                'required'          => true,
            ],
            'lng'   => [
                'type'              => 'number',
                'required'          => true,
            ],
        ],
    ]);
}
add_action('rest_api_init', 'capiznon_geo_register_rest_routes');

/**
 * Create a new location from the frontend
 */
function capiznon_geo_create_location(WP_REST_Request $request) {
    if (!is_user_logged_in()) {
        return new WP_Error('forbidden', __('You must be logged in to add locations.', 'capiznon-geo'), ['status' => 401]);
    }

    $title   = sanitize_text_field($request->get_param('title'));
    $type    = sanitize_text_field($request->get_param('type'));
    $lat     = floatval($request->get_param('lat'));
    $lng     = floatval($request->get_param('lng'));
    $excerpt = sanitize_textarea_field($request->get_param('excerpt'));
    $address = sanitize_text_field($request->get_param('address'));

    if (empty($title) || empty($type) || !$lat || !$lng) {
        return new WP_Error('invalid_data', __('Missing required fields.', 'capiznon-geo'), ['status' => 400]);
    }

    // Determine post status based on capability
    $status = current_user_can('publish_posts') ? 'publish' : 'pending';

    $post_id = wp_insert_post([
        'post_type'    => 'cg_location',
        'post_title'   => $title,
        'post_excerpt' => $excerpt,
        'post_status'  => $status,
    ], true);

    if (is_wp_error($post_id)) {
        return new WP_Error('create_failed', __('Could not create location.', 'capiznon-geo'), ['status' => 500]);
    }

    // Set location type
    if ($type) {
        wp_set_post_terms($post_id, [$type], 'location_type', false);
    }

    // Set cuisines (optional, expects array of slugs)
    $cuisines = $request->get_param('cuisines');
    if (is_array($cuisines) && !empty($cuisines)) {
        $cuisine_slugs = array_map('sanitize_text_field', $cuisines);
        wp_set_post_terms($post_id, $cuisine_slugs, 'location_cuisine', false);
    }

    // Save basic meta
    update_post_meta($post_id, '_cg_latitude', $lat);
    update_post_meta($post_id, '_cg_longitude', $lng);
    if (!empty($address)) {
        update_post_meta($post_id, '_cg_address', $address);
    }

    // Mark as created via frontend so we can show a Pending badge in admin
    update_post_meta($post_id, '_cg_created_via_frontend', 1);

    // Handle photo uploads (optional)
    $files = $request->get_file_params();
    if (!empty($files['photos'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $photos = $files['photos'];
        $gallery_ids = [];

        // Normalize single vs multiple uploads
        if (!is_array($photos['name'])) {
            $photos = [
                'name'     => [$photos['name']],
                'type'     => [$photos['type']],
                'tmp_name' => [$photos['tmp_name']],
                'error'    => [$photos['error']],
                'size'     => [$photos['size']],
            ];
        }

        foreach ($photos['name'] as $index => $name) {
            if ($photos['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file_array = [
                'name'     => $name,
                'type'     => $photos['type'][$index],
                'tmp_name' => $photos['tmp_name'][$index],
                'error'    => 0,
                'size'     => $photos['size'][$index],
            ];

            $attachment_id = media_handle_sideload($file_array, $post_id);
            if (!is_wp_error($attachment_id)) {
                $gallery_ids[] = $attachment_id;
            }
        }

        if (!empty($gallery_ids)) {
            update_post_meta($post_id, '_cg_gallery', $gallery_ids);
        }
    }

    $location = capiznon_geo_format_location(get_post($post_id));

    return rest_ensure_response([
        'location' => $location,
        'status'   => $status,
    ]);
}

/**
 * Get all locations for map display
 */
function capiznon_geo_get_locations($request) {
    $args = [
        'post_type'      => 'cg_location',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => '_cg_latitude',
                'compare' => 'EXISTS',
            ],
            [
                'key'     => '_cg_longitude',
                'compare' => 'EXISTS',
            ],
        ],
    ];

    // Filter by type
    if ($type = $request->get_param('type')) {
        $args['tax_query'][] = [
            'taxonomy' => 'location_type',
            'field'    => 'slug',
            'terms'    => explode(',', $type),
        ];
    }

    // Filter by area
    if ($area = $request->get_param('area')) {
        $args['tax_query'][] = [
            'taxonomy' => 'location_area',
            'field'    => 'slug',
            'terms'    => explode(',', $area),
        ];
    }

    // Filter by tag
    if ($tag = $request->get_param('tag')) {
        $args['tax_query'][] = [
            'taxonomy' => 'location_tag',
            'field'    => 'slug',
            'terms'    => explode(',', $tag),
        ];
    }

    // Filter by price
    if ($price = $request->get_param('price')) {
        $args['tax_query'][] = [
            'taxonomy' => 'location_price',
            'field'    => 'slug',
            'terms'    => explode(',', $price),
        ];
    }

    // Filter by cuisine
    if ($cuisine = $request->get_param('cuisine')) {
        $args['tax_query'][] = [
            'taxonomy' => 'location_cuisine',
            'field'    => 'slug',
            'terms'    => explode(',', $cuisine),
        ];
    }

    // Filter featured only
    if ($request->get_param('featured')) {
        $args['meta_query'][] = [
            'key'   => '_cg_featured',
            'value' => '1',
        ];
    }

    // Search
    if ($search = $request->get_param('search')) {
        $args['s'] = $search;
    }

    // Set tax_query relation if multiple taxonomies
    if (isset($args['tax_query']) && count($args['tax_query']) > 1) {
        $args['tax_query']['relation'] = 'AND';
    }

    $query = new WP_Query($args);
    $locations = [];

    foreach ($query->posts as $post) {
        $locations[] = capiznon_geo_format_location($post);
    }

    return rest_ensure_response([
        'locations' => $locations,
        'total'     => $query->found_posts,
    ]);
}

/**
 * Get single location
 */
function capiznon_geo_get_location($request) {
    $post = get_post($request['id']);

    if (!$post || $post->post_type !== 'cg_location' || $post->post_status !== 'publish') {
        return new WP_Error('not_found', __('Location not found', 'capiznon-geo'), ['status' => 404]);
    }

    return rest_ensure_response(capiznon_geo_format_location($post, true));
}

/**
 * Get nearby locations using Haversine formula
 */
function capiznon_geo_get_nearby_locations($request) {
    global $wpdb;

    $lat = floatval($request->get_param('lat'));
    $lng = floatval($request->get_param('lng'));
    $radius = floatval($request->get_param('radius') ?: 1); // in kilometers
    $exclude = absint($request->get_param('exclude'));
    $type = sanitize_text_field($request->get_param('type'));
    $limit = min(absint($request->get_param('limit') ?: 10), 50);

    // Build the Haversine formula with escaped values
    $haversine = sprintf(
        "(6371 * acos(
            LEAST(1, GREATEST(-1,
                cos(radians(%f)) * 
                cos(radians(CAST(lat.meta_value AS DECIMAL(10,8)))) * 
                cos(radians(CAST(lng.meta_value AS DECIMAL(11,8))) - radians(%f)) + 
                sin(radians(%f)) * 
                sin(radians(CAST(lat.meta_value AS DECIMAL(10,8))))
            ))
        ))",
        $lat, $lng, $lat
    );

    // Build query without prepare for the haversine part (values already sanitized)
    $sql = "
        SELECT DISTINCT p.ID, 
               {$haversine} AS distance
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} lat ON p.ID = lat.post_id AND lat.meta_key = '_cg_latitude'
        INNER JOIN {$wpdb->postmeta} lng ON p.ID = lng.post_id AND lng.meta_key = '_cg_longitude'
        WHERE p.post_type = 'cg_location'
          AND p.post_status = 'publish'
          AND lat.meta_value != ''
          AND lng.meta_value != ''
          AND lat.meta_value IS NOT NULL
          AND lng.meta_value IS NOT NULL
        HAVING distance <= " . floatval($radius) . "
        ORDER BY distance ASC
        LIMIT " . intval($limit + 1);

    $results = $wpdb->get_results($sql);
    $locations = [];

    foreach ($results as $result) {
        // Skip excluded location
        if ($exclude && $result->ID == $exclude) {
            continue;
        }

        $post = get_post($result->ID);
        if (!$post) continue;

        // Filter by type if specified
        if ($type) {
            $types = wp_get_post_terms($post->ID, 'location_type', ['fields' => 'slugs']);
            if (!in_array($type, $types)) {
                continue;
            }
        }

        $location = capiznon_geo_format_location($post);
        $location['distance'] = round($result->distance, 2);
        $locations[] = $location;

        if (count($locations) >= $limit) {
            break;
        }
    }

    return rest_ensure_response([
        'locations' => $locations,
        'center'    => ['lat' => $lat, 'lng' => $lng],
        'radius'    => $radius,
    ]);
}

/**
 * Get filter options
 */
function capiznon_geo_get_filters() {
    $types = get_terms([
        'taxonomy'   => 'location_type',
        'hide_empty' => false,
    ]);

    $areas = get_terms([
        'taxonomy'   => 'location_area',
        'hide_empty' => true,
    ]);

    $tags = get_terms([
        'taxonomy'   => 'location_tag',
        'hide_empty' => true,
    ]);

    $prices = get_terms([
        'taxonomy'   => 'location_price',
        'hide_empty' => false, // show all price ranges for filtering
    ]);
    $prices = capiznon_geo_order_price_terms($prices);

    $cuisines = get_terms([
        'taxonomy'   => 'location_cuisine',
        'hide_empty' => false, // show full list so filter displays even if no posts yet
    ]);

    $format_terms = function($terms) {
        if (is_wp_error($terms) || empty($terms)) return [];
        
        $result = [];
        foreach ($terms as $term) {
            $result[] = [
                'id'     => $term->term_id,
                'name'   => html_entity_decode($term->name),
                'slug'   => $term->slug,
                'count'  => $term->count,
                'parent' => $term->parent,
            ];
        }
        return $result;
    };

    return rest_ensure_response([
        'types'    => $format_terms($types),
        'areas'    => $format_terms($areas),
        'tags'     => $format_terms($tags),
        'prices'   => $format_terms($prices),
        'cuisines' => $format_terms($cuisines),
    ]);
}

/**
 * Sort price terms in a custom order (₱ to ₱₱₱₱, fallback by name)
 */
if (!function_exists('capiznon_geo_order_price_terms')) {
function capiznon_geo_order_price_terms($terms) {
    if (is_wp_error($terms) || empty($terms)) {
        return $terms;
    }

    $priority_map = [
        'budget'      => 1,
        'moderate'    => 2,
        'upscale'     => 3,
        'fine dining' => 4,
    ];

    $rank_term = function($term) use ($priority_map) {
        $symbol_count = preg_match_all('/₱/u', $term->name, $matches);
        if ($symbol_count === 0 && preg_match('/^p+$/i', $term->slug)) {
            $symbol_count = strlen($term->slug);
        }

        $priority = 99;
        foreach ($priority_map as $needle => $p) {
            if (stripos($term->name, $needle) !== false || stripos($term->slug, sanitize_title($needle)) !== false) {
                $priority = $p;
                break;
            }
        }

        // Prefer explicit priority, otherwise use symbol count, otherwise bottom
        $rank = ($priority !== 99) ? $priority : ($symbol_count > 0 ? $symbol_count : 99);
        return [$rank, $symbol_count];
    };

    usort($terms, function($a, $b) use ($rank_term) {
        [$a_rank, $a_symbols] = $rank_term($a);
        [$b_rank, $b_symbols] = $rank_term($b);

        if ($a_rank === $b_rank) {
            if ($a_symbols === $b_symbols) {
                return strcasecmp($a->name, $b->name);
            }
            return $a_symbols <=> $b_symbols;
        }
        return $a_rank <=> $b_rank;
    });

    return $terms;
}
}

/**
 * Format location data for API response
 */
function capiznon_geo_format_location($post, $full = false) {
    $lat = get_post_meta($post->ID, '_cg_latitude', true);
    $lng = get_post_meta($post->ID, '_cg_longitude', true);

    $types = wp_get_post_terms($post->ID, 'location_type', ['fields' => 'all']);
    $type_data = [];
    if (!is_wp_error($types)) {
        foreach ($types as $type) {
            $type_data[] = [
                'id'   => $type->term_id,
                'name' => html_entity_decode($type->name),
                'slug' => $type->slug,
            ];
        }
    }

    $thumbnail_id = get_post_thumbnail_id($post->ID);
    $thumbnail = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'location-card') : null;

    $cuisines = wp_get_post_terms($post->ID, 'location_cuisine', ['fields' => 'all']);
    $cuisine_data = [];
    if (!is_wp_error($cuisines)) {
        foreach ($cuisines as $cuisine) {
            $cuisine_data[] = [
                'id'   => $cuisine->term_id,
                'name' => html_entity_decode($cuisine->name),
                'slug' => $cuisine->slug,
            ];
        }
    }

    $data = [
        'id'           => $post->ID,
        'title'        => $post->post_title,
        'slug'         => $post->post_name,
        'url'          => get_permalink($post->ID),
        'lat'          => $lat ? floatval($lat) : null,
        'lng'          => $lng ? floatval($lng) : null,
        'types'        => $type_data,
        'cuisines'     => $cuisine_data,
        'featured'     => (bool) get_post_meta($post->ID, '_cg_featured', true),
        'marker_color' => get_post_meta($post->ID, '_cg_marker_color', true) ?: '#e74c3c',
        'marker_icon'  => get_post_meta($post->ID, '_cg_marker_icon', true) ?: 'default',
        'thumbnail'    => $thumbnail ? $thumbnail[0] : null,
        'excerpt'      => wp_trim_words($post->post_excerpt ?: $post->post_content, 20),
    ];

    // Include full details for single location requests
    if ($full) {
        $data['content'] = apply_filters('the_content', $post->post_content);
        $data['address'] = [
            'street'      => get_post_meta($post->ID, '_cg_address', true),
            'line2'       => get_post_meta($post->ID, '_cg_address_line2', true),
            'city'        => get_post_meta($post->ID, '_cg_city', true),
            'province'    => get_post_meta($post->ID, '_cg_province', true),
            'postal_code' => get_post_meta($post->ID, '_cg_postal_code', true),
            'country'     => get_post_meta($post->ID, '_cg_country', true),
        ];
        $data['contact'] = [
            'phone'     => get_post_meta($post->ID, '_cg_phone', true),
            'phone2'    => get_post_meta($post->ID, '_cg_phone2', true),
            'email'     => get_post_meta($post->ID, '_cg_email', true),
            'website'   => get_post_meta($post->ID, '_cg_website', true),
            'facebook'  => get_post_meta($post->ID, '_cg_facebook', true),
            'instagram' => get_post_meta($post->ID, '_cg_instagram', true),
        ];
        $data['hours'] = get_post_meta($post->ID, '_cg_hours', true) ?: [];
        $data['is_24_hours'] = (bool) get_post_meta($post->ID, '_cg_is_24_hours', true);
        $data['temporarily_closed'] = (bool) get_post_meta($post->ID, '_cg_temporarily_closed', true);

        // Gallery
        $gallery_ids = get_post_meta($post->ID, '_cg_gallery', true) ?: [];
        $gallery = [];
        foreach ($gallery_ids as $id) {
            $img = wp_get_attachment_image_src($id, 'location-gallery');
            $thumb = wp_get_attachment_image_src($id, 'thumbnail');
            if ($img) {
                $gallery[] = [
                    'id'        => $id,
                    'url'       => $img[0],
                    'thumbnail' => $thumb ? $thumb[0] : $img[0],
                    'alt'       => get_post_meta($id, '_wp_attachment_image_alt', true),
                ];
            }
        }
        $data['gallery'] = $gallery;

        // Areas
        $areas = wp_get_post_terms($post->ID, 'location_area', ['fields' => 'all']);
        $data['areas'] = [];
        if (!is_wp_error($areas)) {
            foreach ($areas as $area) {
                $data['areas'][] = [
                    'id'   => $area->term_id,
                    'name' => $area->name,
                    'slug' => $area->slug,
                ];
            }
        }

        // Tags
        $tags = wp_get_post_terms($post->ID, 'location_tag', ['fields' => 'all']);
        $data['tags'] = [];
        if (!is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $data['tags'][] = [
                    'id'   => $tag->term_id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            }
        }

        // Price
        $prices = wp_get_post_terms($post->ID, 'location_price', ['fields' => 'all']);
        $data['price'] = null;
        if (!is_wp_error($prices) && !empty($prices)) {
            $data['price'] = [
                'id'   => $prices[0]->term_id,
                'name' => $prices[0]->name,
                'slug' => $prices[0]->slug,
            ];
        }
    }

    return $data;
}
