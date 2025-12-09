<?php
/**
 * Recommendation Engine for cg_location
 *
 * @package Capiznon_Geo
 */

defined('ABSPATH') || exit;

class Capiznon_Geo_Recommender {
    protected static $intent_map = [
        'food-dining'   => ['food-dining', 'restaurants', 'cafes', 'bars', 'street-food', 'bakeries'],
        'accommodation' => ['accommodation', 'hotels', 'resorts', 'guesthouses', 'homestays', 'hostels'],
        'attractions'   => ['attractions', 'beaches', 'historical', 'parks', 'museums', 'religious'],
        'shopping'      => ['shopping', 'markets', 'malls', 'souvenirs', 'local-products'],
    ];

    protected static $intent_labels = [
        'food-dining'   => 'Eat & drink',
        'accommodation' => 'Places to stay',
        'attractions'   => 'Things to see',
        'shopping'      => 'Shopping',
        'any'           => 'Surprise me',
    ];

    protected static $vibe_labels = [
        'cozy-quiet'      => 'Quiet & cozy',
        'family-friendly' => 'Family-friendly',
        'insta-worthy'    => 'Instagrammable views',
        'lively-night-out'=> 'Night out / barkada',
        'romantic'        => 'Romantic / special',
        'any'             => "I don't mind",
    ];

    protected static $group_meta = [
        'solo'    => '_cg_suits_solo',
        'couple'  => '_cg_suits_couples',
        'family'  => '_cg_suits_family',
        'barkada' => '_cg_suits_barkada',
        'work'    => '_cg_suits_work',
    ];

    protected static $allowed_constraints = [
        'near',
        'budget',
        'open-now',
        'beachfront',
        'indoor-aircon',
        'parking',
    ];

    public static function normalize_params(array $raw): array {
        $intent      = isset($raw['cg_intent']) ? sanitize_text_field($raw['cg_intent']) : 'any';
        $vibe        = isset($raw['cg_vibe']) ? sanitize_text_field($raw['cg_vibe']) : 'any';
        $group       = isset($raw['cg_group']) ? sanitize_text_field($raw['cg_group']) : 'any';
        $constraints = [];

        if (!in_array($intent, array_merge(array_keys(self::$intent_map), ['any']), true)) {
            $intent = 'any';
        }

        if (!array_key_exists($vibe, self::$vibe_labels)) {
            $vibe = 'any';
        }

        if (!array_key_exists($group, self::$group_meta) && $group !== 'any') {
            $group = 'any';
        }

        if (!empty($raw['cg_constraints'])) {
            $parts = explode(',', sanitize_text_field($raw['cg_constraints']));
            foreach ($parts as $part) {
                $part = trim($part);
                if (in_array($part, self::$allowed_constraints, true)) {
                    $constraints[] = $part;
                }
            }
            $constraints = array_values(array_unique($constraints));
        }

        $lat = isset($raw['cg_lat']) && is_numeric($raw['cg_lat']) ? floatval($raw['cg_lat']) : null;
        $lng = isset($raw['cg_lng']) && is_numeric($raw['cg_lng']) ? floatval($raw['cg_lng']) : null;

        $normalized = [
            'intent'      => $intent,
            'vibe'        => $vibe,
            'constraints' => $constraints,
            'group'       => $group,
            'lat'         => $lat,
            'lng'         => $lng,
            'limit'       => 24,
            'radius_km'   => 5,
        ];

        if (!in_array('near', $constraints, true) || $lat === null || $lng === null) {
            $normalized['radius_km'] = null;
        }

        return $normalized;
    }

    public static function get_recommendations(array $params): array {
        $args  = self::build_query_args($params);
        $query = new WP_Query($args);
        $results = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = $query->post;
                $score_data = self::score_location($post, $params);

                $results[] = [
                    'ID'            => $post->ID,
                    'title'         => get_the_title($post),
                    'permalink'     => get_permalink($post),
                    'thumbnail_url' => get_the_post_thumbnail_url($post, 'medium_large') ?: '',
                    'distance_km'   => $score_data['distance_km'],
                    'score'         => $score_data['score'],
                    'tags'          => $score_data['matched_tags'],
                ];
            }
            wp_reset_postdata();
        }

        usort($results, function($a, $b) {
            if ($a['score'] === $b['score']) {
                return strcasecmp($a['title'], $b['title']);
            }
            return ($a['score'] > $b['score']) ? -1 : 1;
        });

        $explanation = self::build_explanation($params);

        return [
            'params'      => $params,
            'explanation' => $explanation,
            'results'     => $results,
        ];
    }

    protected static function build_query_args(array $params): array {
        $tax_query = [];

        if ($params['intent'] !== 'any' && isset(self::$intent_map[$params['intent']])) {
            $tax_query[] = [
                'taxonomy' => 'location_type',
                'field'    => 'slug',
                'terms'    => self::$intent_map[$params['intent']],
            ];
        }

        if ($params['vibe'] !== 'any') {
            $tax_query[] = [
                'taxonomy' => 'location_vibe',
                'field'    => 'slug',
                'terms'    => [$params['vibe']],
            ];
        }

        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }

        return [
            'post_type'      => 'cg_location',
            'posts_per_page' => 50,
            'tax_query'      => $tax_query,
        ];
    }

    protected static function score_location(WP_Post $post, array $params): array {
        $score = 10;
        $matched_tags = [];
        $distance_km = null;

        // Intent
        if ($params['intent'] !== 'any' && isset(self::$intent_map[$params['intent']])) {
            $types = wp_get_post_terms($post->ID, 'location_type', ['fields' => 'slugs']);
            if (!is_wp_error($types) && array_intersect($types, self::$intent_map[$params['intent']])) {
                $score += 20;
                $matched_tags[] = self::$intent_labels[$params['intent']];
            }
        }

        // Vibe
        if ($params['vibe'] !== 'any') {
            $vibes = wp_get_post_terms($post->ID, 'location_vibe', ['fields' => 'slugs']);
            if (!is_wp_error($vibes) && in_array($params['vibe'], $vibes, true)) {
                $score += 15;
                $matched_tags[] = self::$vibe_labels[$params['vibe']];
            }
        }

        // Group suitability
        if ($params['group'] !== 'any' && isset(self::$group_meta[$params['group']])) {
            $meta_key = self::$group_meta[$params['group']];
            $suits = get_post_meta($post->ID, $meta_key, true);
            if (!empty($suits)) {
                $score += 12;
                $matched_tags[] = self::group_label($params['group']);
            }
        }

        // Constraints
        foreach ($params['constraints'] as $constraint) {
            switch ($constraint) {
                case 'budget':
                    $price_level = get_post_meta($post->ID, '_cg_price_level', true);
                    if ($price_level === 'budget') {
                        $score += 10;
                        $matched_tags[] = __('Budget-friendly', 'capiznon-geo');
                    }
                    break;
                case 'beachfront':
                    if (!empty(get_post_meta($post->ID, '_cg_beachfront', true))) {
                        $score += 10;
                        $matched_tags[] = __('Beachfront / nature', 'capiznon-geo');
                    }
                    break;
                case 'indoor-aircon':
                    if (!empty(get_post_meta($post->ID, '_cg_indoor_aircon', true))) {
                        $score += 8;
                        $matched_tags[] = __('Indoor / air-conditioned', 'capiznon-geo');
                    }
                    break;
                case 'parking':
                    if (!empty(get_post_meta($post->ID, '_cg_parking', true))) {
                        $score += 6;
                        $matched_tags[] = __('Good parking', 'capiznon-geo');
                    }
                    break;
                case 'open-now':
                    // Placeholder hook
                    break;
                case 'near':
                    $distance_km = self::compute_distance($params, $post->ID);
                    if ($distance_km !== null && $params['radius_km']) {
                        if ($distance_km <= $params['radius_km']) {
                            $score += 15;
                            $matched_tags[] = __('Near you', 'capiznon-geo');
                        } elseif ($distance_km <= ($params['radius_km'] * 2)) {
                            $score += 8;
                            $matched_tags[] = __('Within short ride', 'capiznon-geo');
                        }
                    }
                    break;
            }
        }

        // Popularity
        $popularity = get_post_meta($post->ID, '_cg_popularity_score', true);
        if ($popularity !== '' && is_numeric($popularity)) {
            $contribution = min(15, floatval($popularity) / 10);
            $score += $contribution;
            if ($contribution >= 10) {
                $matched_tags[] = __('Popular choice', 'capiznon-geo');
            }
        }

        // Jitter
        $score += mt_rand(0, 3);

        return [
            'score'        => $score,
            'matched_tags' => $matched_tags,
            'distance_km'  => $distance_km,
        ];
    }

    protected static function compute_distance(array $params, int $post_id): ?float {
        if (empty($params['lat']) || empty($params['lng'])) {
            return null;
        }

        $lat = get_post_meta($post_id, '_cg_latitude', true);
        $lng = get_post_meta($post_id, '_cg_longitude', true);
        if ($lat === '' || $lng === '' || !is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }

        $earth_radius = 6371; // km
        $dLat = deg2rad(floatval($lat) - $params['lat']);
        $dLng = deg2rad(floatval($lng) - $params['lng']);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($params['lat'])) * cos(deg2rad(floatval($lat))) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earth_radius * $c, 2);
    }

    protected static function build_explanation(array $params): array {
        $explanation = [];

        if ($params['intent'] !== 'any' && isset(self::$intent_labels[$params['intent']])) {
            $explanation[] = self::$intent_labels[$params['intent']];
        }
        if ($params['vibe'] !== 'any' && isset(self::$vibe_labels[$params['vibe']])) {
            $explanation[] = self::$vibe_labels[$params['vibe']];
        }
        if ($params['group'] !== 'any') {
            $explanation[] = self::group_label($params['group']);
        }

        foreach ($params['constraints'] as $constraint) {
            $explanation[] = self::constraint_label($constraint);
        }

        return array_values(array_filter($explanation));
    }

    protected static function group_label(string $group): string {
        $map = [
            'solo'    => __('Great for solo', 'capiznon-geo'),
            'couple'  => __('Good for couples', 'capiznon-geo'),
            'family'  => __('Family-friendly', 'capiznon-geo'),
            'barkada' => __('Great for barkada', 'capiznon-geo'),
            'work'    => __('Work / formal', 'capiznon-geo'),
        ];
        return $map[$group] ?? '';
    }

    protected static function constraint_label(string $constraint): string {
        $map = [
            'near'          => __('Near you', 'capiznon-geo'),
            'budget'        => __('Budget-friendly', 'capiznon-geo'),
            'open-now'      => __('Open now', 'capiznon-geo'),
            'beachfront'    => __('Beachfront / nature', 'capiznon-geo'),
            'indoor-aircon' => __('Indoor / air-conditioned', 'capiznon-geo'),
            'parking'       => __('Good parking', 'capiznon-geo'),
        ];
        return $map[$constraint] ?? '';
    }
}
