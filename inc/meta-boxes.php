<?php
/**
 * Meta Boxes for Location Post Type
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Register meta boxes
 */
function capiznon_geo_register_meta_boxes() {
    add_meta_box(
        'cg_location_details',
        __('Location Details', 'capiznon-geo'),
        'capiznon_geo_location_details_callback',
        'cg_location',
        'normal',
        'high'
    );

    add_meta_box(
        'cg_location_coordinates',
        __('Map Coordinates', 'capiznon-geo'),
        'capiznon_geo_coordinates_callback',
        'cg_location',
        'normal',
        'high'
    );

    add_meta_box(
        'cg_location_hours',
        __('Operating Hours', 'capiznon-geo'),
        'capiznon_geo_hours_callback',
        'cg_location',
        'normal',
        'default'
    );

    add_meta_box(
        'cg_location_contact',
        __('Contact Information', 'capiznon-geo'),
        'capiznon_geo_contact_callback',
        'cg_location',
        'side',
        'default'
    );

    add_meta_box(
        'cg_location_gallery',
        __('Photo Gallery', 'capiznon-geo'),
        'capiznon_geo_gallery_callback',
        'cg_location',
        'normal',
        'default'
    );

    add_meta_box(
        'cg_location_options',
        __('Display Options', 'capiznon-geo'),
        'capiznon_geo_options_callback',
        'cg_location',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'capiznon_geo_register_meta_boxes');

/**
 * Location Details meta box
 */
function capiznon_geo_location_details_callback($post) {
    wp_nonce_field('cg_location_meta', 'cg_location_nonce');

    $address = get_post_meta($post->ID, '_cg_address', true);
    $address_line2 = get_post_meta($post->ID, '_cg_address_line2', true);
    $city = get_post_meta($post->ID, '_cg_city', true) ?: 'Roxas City';
    $province = get_post_meta($post->ID, '_cg_province', true) ?: 'Capiz';
    $postal_code = get_post_meta($post->ID, '_cg_postal_code', true);
    $country = get_post_meta($post->ID, '_cg_country', true) ?: 'Philippines';
    ?>
    <table class="form-table">
        <tr>
            <th><label for="cg_address"><?php _e('Street Address', 'capiznon-geo'); ?></label></th>
            <td>
                <input type="text" id="cg_address" name="cg_address" value="<?php echo esc_attr($address); ?>" class="large-text">
            </td>
        </tr>
        <tr>
            <th><label for="cg_address_line2"><?php _e('Address Line 2', 'capiznon-geo'); ?></label></th>
            <td>
                <input type="text" id="cg_address_line2" name="cg_address_line2" value="<?php echo esc_attr($address_line2); ?>" class="large-text">
                <p class="description"><?php _e('Building name, floor, unit number, etc.', 'capiznon-geo'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="cg_city"><?php _e('City', 'capiznon-geo'); ?></label></th>
            <td>
                <input type="text" id="cg_city" name="cg_city" value="<?php echo esc_attr($city); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="cg_province"><?php _e('Province', 'capiznon-geo'); ?></label></th>
            <td>
                <input type="text" id="cg_province" name="cg_province" value="<?php echo esc_attr($province); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="cg_postal_code"><?php _e('Postal Code', 'capiznon-geo'); ?></label></th>
            <td>
                <input type="text" id="cg_postal_code" name="cg_postal_code" value="<?php echo esc_attr($postal_code); ?>" class="small-text">
            </td>
        </tr>
        <tr>
            <th><label for="cg_country"><?php _e('Country', 'capiznon-geo'); ?></label></th>
            <td>
                <input type="text" id="cg_country" name="cg_country" value="<?php echo esc_attr($country); ?>" class="regular-text">
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Coordinates meta box with map picker
 */
function capiznon_geo_coordinates_callback($post) {
    $lat = get_post_meta($post->ID, '_cg_latitude', true) ?: CAPIZNON_GEO_DEFAULT_LAT;
    $lng = get_post_meta($post->ID, '_cg_longitude', true) ?: CAPIZNON_GEO_DEFAULT_LNG;
    ?>
    <div id="cg-map-picker" style="height: 400px; margin-bottom: 15px; border: 1px solid #ddd;"></div>
    
    <table class="form-table">
        <tr>
            <th><label for="cg_latitude"><?php _e('Latitude', 'capiznon-geo'); ?></label></th>
            <td>
                <input type="text" id="cg_latitude" name="cg_latitude" value="<?php echo esc_attr($lat); ?>" class="regular-text" step="any">
            </td>
        </tr>
        <tr>
            <th><label for="cg_longitude"><?php _e('Longitude', 'capiznon-geo'); ?></label></th>
            <td>
                <input type="text" id="cg_longitude" name="cg_longitude" value="<?php echo esc_attr($lng); ?>" class="regular-text" step="any">
            </td>
        </tr>
    </table>
    
    <p class="description">
        <?php _e('Click on the map to set the location, or enter coordinates manually.', 'capiznon-geo'); ?>
    </p>
    <?php
}

/**
 * Operating Hours meta box
 */
function capiznon_geo_hours_callback($post) {
    $hours = get_post_meta($post->ID, '_cg_hours', true) ?: [];
    $is_24_hours = get_post_meta($post->ID, '_cg_is_24_hours', true);
    $is_temporarily_closed = get_post_meta($post->ID, '_cg_temporarily_closed', true);

    $days = [
        'monday'    => __('Monday', 'capiznon-geo'),
        'tuesday'   => __('Tuesday', 'capiznon-geo'),
        'wednesday' => __('Wednesday', 'capiznon-geo'),
        'thursday'  => __('Thursday', 'capiznon-geo'),
        'friday'    => __('Friday', 'capiznon-geo'),
        'saturday'  => __('Saturday', 'capiznon-geo'),
        'sunday'    => __('Sunday', 'capiznon-geo'),
    ];
    ?>
    <p>
        <label>
            <input type="checkbox" name="cg_is_24_hours" value="1" <?php checked($is_24_hours, '1'); ?>>
            <?php _e('Open 24 Hours', 'capiznon-geo'); ?>
        </label>
        &nbsp;&nbsp;
        <label>
            <input type="checkbox" name="cg_temporarily_closed" value="1" <?php checked($is_temporarily_closed, '1'); ?>>
            <?php _e('Temporarily Closed', 'capiznon-geo'); ?>
        </label>
    </p>

    <table class="widefat" id="cg-hours-table">
        <thead>
            <tr>
                <th><?php _e('Day', 'capiznon-geo'); ?></th>
                <th><?php _e('Open', 'capiznon-geo'); ?></th>
                <th><?php _e('Close', 'capiznon-geo'); ?></th>
                <th><?php _e('Closed', 'capiznon-geo'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($days as $key => $label) : 
                $day_hours = $hours[$key] ?? ['open' => '09:00', 'close' => '17:00', 'closed' => false];
            ?>
            <tr>
                <td><strong><?php echo esc_html($label); ?></strong></td>
                <td>
                    <input type="time" name="cg_hours[<?php echo $key; ?>][open]" 
                           value="<?php echo esc_attr($day_hours['open'] ?? '09:00'); ?>">
                </td>
                <td>
                    <input type="time" name="cg_hours[<?php echo $key; ?>][close]" 
                           value="<?php echo esc_attr($day_hours['close'] ?? '17:00'); ?>">
                </td>
                <td>
                    <input type="checkbox" name="cg_hours[<?php echo $key; ?>][closed]" value="1" 
                           <?php checked(!empty($day_hours['closed'])); ?>>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Contact Information meta box
 */
function capiznon_geo_contact_callback($post) {
    $phone = get_post_meta($post->ID, '_cg_phone', true);
    $phone2 = get_post_meta($post->ID, '_cg_phone2', true);
    $email = get_post_meta($post->ID, '_cg_email', true);
    $website = get_post_meta($post->ID, '_cg_website', true);
    $facebook = get_post_meta($post->ID, '_cg_facebook', true);
    $instagram = get_post_meta($post->ID, '_cg_instagram', true);
    ?>
    <p>
        <label for="cg_phone"><?php _e('Phone', 'capiznon-geo'); ?></label><br>
        <input type="tel" id="cg_phone" name="cg_phone" value="<?php echo esc_attr($phone); ?>" class="widefat">
    </p>
    <p>
        <label for="cg_phone2"><?php _e('Alt. Phone', 'capiznon-geo'); ?></label><br>
        <input type="tel" id="cg_phone2" name="cg_phone2" value="<?php echo esc_attr($phone2); ?>" class="widefat">
    </p>
    <p>
        <label for="cg_email"><?php _e('Email', 'capiznon-geo'); ?></label><br>
        <input type="email" id="cg_email" name="cg_email" value="<?php echo esc_attr($email); ?>" class="widefat">
    </p>
    <p>
        <label for="cg_website"><?php _e('Website', 'capiznon-geo'); ?></label><br>
        <input type="url" id="cg_website" name="cg_website" value="<?php echo esc_attr($website); ?>" class="widefat" placeholder="https://">
    </p>
    <p>
        <label for="cg_facebook"><?php _e('Facebook', 'capiznon-geo'); ?></label><br>
        <input type="url" id="cg_facebook" name="cg_facebook" value="<?php echo esc_attr($facebook); ?>" class="widefat" placeholder="https://facebook.com/...">
    </p>
    <p>
        <label for="cg_instagram"><?php _e('Instagram', 'capiznon-geo'); ?></label><br>
        <input type="text" id="cg_instagram" name="cg_instagram" value="<?php echo esc_attr($instagram); ?>" class="widefat" placeholder="@username">
    </p>
    <?php
}

/**
 * Photo Gallery meta box
 */
function capiznon_geo_gallery_callback($post) {
    $gallery = get_post_meta($post->ID, '_cg_gallery', true) ?: [];
    ?>
    <div id="cg-gallery-container">
        <ul id="cg-gallery-images" class="cg-gallery-list">
            <?php foreach ($gallery as $image_id) : 
                $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                if ($image) :
            ?>
            <li class="cg-gallery-item" data-id="<?php echo esc_attr($image_id); ?>">
                <img src="<?php echo esc_url($image[0]); ?>" alt="">
                <input type="hidden" name="cg_gallery[]" value="<?php echo esc_attr($image_id); ?>">
                <button type="button" class="cg-remove-image">&times;</button>
            </li>
            <?php endif; endforeach; ?>
        </ul>
        <button type="button" id="cg-add-gallery-images" class="button">
            <?php _e('Add Images', 'capiznon-geo'); ?>
        </button>
    </div>
    <style>
        .cg-gallery-list { display: flex; flex-wrap: wrap; gap: 10px; list-style: none; padding: 0; margin: 0 0 10px; }
        .cg-gallery-item { position: relative; width: 100px; height: 100px; }
        .cg-gallery-item img { width: 100%; height: 100%; object-fit: cover; border: 1px solid #ddd; }
        .cg-remove-image { position: absolute; top: -5px; right: -5px; background: #dc3545; color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 14px; line-height: 1; }
    </style>
    <?php
}

/**
 * Display Options meta box
 */
function capiznon_geo_options_callback($post) {
    $featured = get_post_meta($post->ID, '_cg_featured', true);
    $marker_color = get_post_meta($post->ID, '_cg_marker_color', true) ?: '#e74c3c';
    $marker_icon = get_post_meta($post->ID, '_cg_marker_icon', true) ?: 'default';
    $price_level = get_post_meta($post->ID, '_cg_price_level', true) ?: 'moderate';
    $beachfront = get_post_meta($post->ID, '_cg_beachfront', true);
    $indoor_aircon = get_post_meta($post->ID, '_cg_indoor_aircon', true);
    $parking = get_post_meta($post->ID, '_cg_parking', true);
    $popularity = get_post_meta($post->ID, '_cg_popularity_score', true);

    $suits = [
        'solo'    => get_post_meta($post->ID, '_cg_suits_solo', true),
        'couples' => get_post_meta($post->ID, '_cg_suits_couples', true),
        'family'  => get_post_meta($post->ID, '_cg_suits_family', true),
        'barkada' => get_post_meta($post->ID, '_cg_suits_barkada', true),
        'work'    => get_post_meta($post->ID, '_cg_suits_work', true),
    ];
    ?>
    <p>
        <label>
            <input type="checkbox" name="cg_featured" value="1" <?php checked($featured, '1'); ?>>
            <strong><?php _e('Featured Location', 'capiznon-geo'); ?></strong>
        </label>
        <br><span class="description"><?php _e('Featured locations appear prominently on the map.', 'capiznon-geo'); ?></span>
    </p>
    <p>
        <label for="cg_marker_color"><?php _e('Marker Color', 'capiznon-geo'); ?></label><br>
        <input type="color" id="cg_marker_color" name="cg_marker_color" value="<?php echo esc_attr($marker_color); ?>">
    </p>
    <p>
        <label for="cg_marker_icon"><?php _e('Marker Icon', 'capiznon-geo'); ?></label><br>
        <select id="cg_marker_icon" name="cg_marker_icon" class="widefat">
            <option value="default" <?php selected($marker_icon, 'default'); ?>><?php _e('Default', 'capiznon-geo'); ?></option>
            <option value="restaurant" <?php selected($marker_icon, 'restaurant'); ?>><?php _e('Restaurant', 'capiznon-geo'); ?></option>
            <option value="cafe" <?php selected($marker_icon, 'cafe'); ?>><?php _e('Cafe', 'capiznon-geo'); ?></option>
            <option value="bar" <?php selected($marker_icon, 'bar'); ?>><?php _e('Bar', 'capiznon-geo'); ?></option>
            <option value="hotel" <?php selected($marker_icon, 'hotel'); ?>><?php _e('Hotel', 'capiznon-geo'); ?></option>
            <option value="shop" <?php selected($marker_icon, 'shop'); ?>><?php _e('Shop', 'capiznon-geo'); ?></option>
            <option value="attraction" <?php selected($marker_icon, 'attraction'); ?>><?php _e('Attraction', 'capiznon-geo'); ?></option>
            <option value="beach" <?php selected($marker_icon, 'beach'); ?>><?php _e('Beach', 'capiznon-geo'); ?></option>
            <option value="church" <?php selected($marker_icon, 'church'); ?>><?php _e('Church', 'capiznon-geo'); ?></option>
        </select>
    </p>
    <hr>
    <p>
        <label for="cg_price_level"><strong><?php _e('Price Level', 'capiznon-geo'); ?></strong></label><br>
        <select id="cg_price_level" name="cg_price_level" class="widefat">
            <option value="budget" <?php selected($price_level, 'budget'); ?>><?php _e('Budget', 'capiznon-geo'); ?></option>
            <option value="moderate" <?php selected($price_level, 'moderate'); ?>><?php _e('Moderate', 'capiznon-geo'); ?></option>
            <option value="premium" <?php selected($price_level, 'premium'); ?>><?php _e('Premium', 'capiznon-geo'); ?></option>
        </select>
    </p>
    <p>
        <label><input type="checkbox" name="cg_beachfront" value="1" <?php checked($beachfront, '1'); ?>> <?php _e('Beachfront / nature', 'capiznon-geo'); ?></label><br>
        <label><input type="checkbox" name="cg_indoor_aircon" value="1" <?php checked($indoor_aircon, '1'); ?>> <?php _e('Indoor / air-conditioned', 'capiznon-geo'); ?></label><br>
        <label><input type="checkbox" name="cg_parking" value="1" <?php checked($parking, '1'); ?>> <?php _e('Good parking / accessible', 'capiznon-geo'); ?></label>
    </p>
    <p>
        <label for="cg_popularity_score"><strong><?php _e('Popularity Score (0–100)', 'capiznon-geo'); ?></strong></label><br>
        <input type="number" min="0" max="100" step="1" name="cg_popularity_score" id="cg_popularity_score" value="<?php echo esc_attr($popularity); ?>" class="small-text">
    </p>
    <p>
        <strong><?php _e('Group suitability', 'capiznon-geo'); ?></strong><br>
        <label><input type="checkbox" name="cg_suits_solo" value="1" <?php checked($suits['solo'], '1'); ?>> <?php _e('Solo', 'capiznon-geo'); ?></label><br>
        <label><input type="checkbox" name="cg_suits_couples" value="1" <?php checked($suits['couples'], '1'); ?>> <?php _e('Couples', 'capiznon-geo'); ?></label><br>
        <label><input type="checkbox" name="cg_suits_family" value="1" <?php checked($suits['family'], '1'); ?>> <?php _e('Family with kids', 'capiznon-geo'); ?></label><br>
        <label><input type="checkbox" name="cg_suits_barkada" value="1" <?php checked($suits['barkada'], '1'); ?>> <?php _e('Barkada / big group', 'capiznon-geo'); ?></label><br>
        <label><input type="checkbox" name="cg_suits_work" value="1" <?php checked($suits['work'], '1'); ?>> <?php _e('Work / formal', 'capiznon-geo'); ?></label>
    </p>
    <?php
}

/**
 * Save meta box data
 */
function capiznon_geo_save_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['cg_location_nonce']) || !wp_verify_nonce($_POST['cg_location_nonce'], 'cg_location_meta')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Location Details
    $text_fields = [
        'cg_address'       => '_cg_address',
        'cg_address_line2' => '_cg_address_line2',
        'cg_city'          => '_cg_city',
        'cg_province'      => '_cg_province',
        'cg_postal_code'   => '_cg_postal_code',
        'cg_country'       => '_cg_country',
    ];

    foreach ($text_fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
        }
    }

    // Coordinates
    if (isset($_POST['cg_latitude'])) {
        update_post_meta($post_id, '_cg_latitude', floatval($_POST['cg_latitude']));
    }
    if (isset($_POST['cg_longitude'])) {
        update_post_meta($post_id, '_cg_longitude', floatval($_POST['cg_longitude']));
    }

    // Contact Info
    $contact_fields = [
        'cg_phone'     => '_cg_phone',
        'cg_phone2'    => '_cg_phone2',
        'cg_email'     => '_cg_email',
        'cg_website'   => '_cg_website',
        'cg_facebook'  => '_cg_facebook',
        'cg_instagram' => '_cg_instagram',
    ];

    foreach ($contact_fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            if ($field === 'cg_email') {
                $value = sanitize_email($value);
            } elseif (in_array($field, ['cg_website', 'cg_facebook'])) {
                $value = esc_url_raw($value);
            } else {
                $value = sanitize_text_field($value);
            }
            update_post_meta($post_id, $meta_key, $value);
        }
    }

    // Operating Hours
    if (isset($_POST['cg_hours']) && is_array($_POST['cg_hours'])) {
        $hours = [];
        foreach ($_POST['cg_hours'] as $day => $data) {
            $hours[sanitize_key($day)] = [
                'open'   => sanitize_text_field($data['open'] ?? ''),
                'close'  => sanitize_text_field($data['close'] ?? ''),
                'closed' => !empty($data['closed']),
            ];
        }
        update_post_meta($post_id, '_cg_hours', $hours);
    }

    // Checkboxes
    update_post_meta($post_id, '_cg_is_24_hours', isset($_POST['cg_is_24_hours']) ? '1' : '');
    update_post_meta($post_id, '_cg_temporarily_closed', isset($_POST['cg_temporarily_closed']) ? '1' : '');
    update_post_meta($post_id, '_cg_featured', isset($_POST['cg_featured']) ? '1' : '');

    // Display Options
    if (isset($_POST['cg_marker_color'])) {
        update_post_meta($post_id, '_cg_marker_color', sanitize_hex_color($_POST['cg_marker_color']));
    }
    if (isset($_POST['cg_marker_icon'])) {
        update_post_meta($post_id, '_cg_marker_icon', sanitize_text_field($_POST['cg_marker_icon']));
    }

    // Amenities / constraints meta
    if (isset($_POST['cg_price_level'])) {
        $price_level = sanitize_text_field($_POST['cg_price_level']);
        update_post_meta($post_id, '_cg_price_level', $price_level);
    }
    update_post_meta($post_id, '_cg_beachfront', isset($_POST['cg_beachfront']) ? '1' : '');
    update_post_meta($post_id, '_cg_indoor_aircon', isset($_POST['cg_indoor_aircon']) ? '1' : '');
    update_post_meta($post_id, '_cg_parking', isset($_POST['cg_parking']) ? '1' : '');

    if (isset($_POST['cg_popularity_score'])) {
        $pop = $_POST['cg_popularity_score'] === '' ? '' : max(0, min(100, floatval($_POST['cg_popularity_score'])));
        update_post_meta($post_id, '_cg_popularity_score', $pop);
    }

    // Group suitability flags
    update_post_meta($post_id, '_cg_suits_solo', isset($_POST['cg_suits_solo']) ? '1' : '');
    update_post_meta($post_id, '_cg_suits_couples', isset($_POST['cg_suits_couples']) ? '1' : '');
    update_post_meta($post_id, '_cg_suits_family', isset($_POST['cg_suits_family']) ? '1' : '');
    update_post_meta($post_id, '_cg_suits_barkada', isset($_POST['cg_suits_barkada']) ? '1' : '');
    update_post_meta($post_id, '_cg_suits_work', isset($_POST['cg_suits_work']) ? '1' : '');

    // Gallery
    if (isset($_POST['cg_gallery']) && is_array($_POST['cg_gallery'])) {
        $gallery = array_map('absint', $_POST['cg_gallery']);
        update_post_meta($post_id, '_cg_gallery', $gallery);
    } else {
        delete_post_meta($post_id, '_cg_gallery');
    }
}
add_action('save_post_cg_location', 'capiznon_geo_save_meta');

/**
 * Register meta fields for REST API
 */
function capiznon_geo_register_meta() {
    $meta_fields = [
        '_cg_latitude'           => ['type' => 'number'],
        '_cg_longitude'          => ['type' => 'number'],
        '_cg_address'            => ['type' => 'string'],
        '_cg_address_line2'      => ['type' => 'string'],
        '_cg_city'               => ['type' => 'string'],
        '_cg_province'           => ['type' => 'string'],
        '_cg_postal_code'        => ['type' => 'string'],
        '_cg_country'            => ['type' => 'string'],
        '_cg_phone'              => ['type' => 'string'],
        '_cg_phone2'             => ['type' => 'string'],
        '_cg_email'              => ['type' => 'string'],
        '_cg_website'            => ['type' => 'string'],
        '_cg_facebook'           => ['type' => 'string'],
        '_cg_instagram'          => ['type' => 'string'],
        '_cg_featured'           => ['type' => 'boolean'],
        '_cg_marker_color'       => ['type' => 'string'],
        '_cg_marker_icon'        => ['type' => 'string'],
        '_cg_is_24_hours'        => ['type' => 'boolean'],
        '_cg_temporarily_closed' => ['type' => 'boolean'],
        '_cg_price_level'        => ['type' => 'string'],
        '_cg_beachfront'         => ['type' => 'boolean'],
        '_cg_indoor_aircon'      => ['type' => 'boolean'],
        '_cg_parking'            => ['type' => 'boolean'],
        '_cg_popularity_score'   => ['type' => 'number'],
        '_cg_suits_solo'         => ['type' => 'boolean'],
        '_cg_suits_couples'      => ['type' => 'boolean'],
        '_cg_suits_family'       => ['type' => 'boolean'],
        '_cg_suits_barkada'      => ['type' => 'boolean'],
        '_cg_suits_work'         => ['type' => 'boolean'],
    ];

    foreach ($meta_fields as $key => $args) {
        register_post_meta('cg_location', $key, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => $args['type'],
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);
    }
}
add_action('init', 'capiznon_geo_register_meta');

/**
 * Register Recommendation meta box for Recommendations page template
 */
function capiznon_geo_register_recommendation_metabox() {
    add_meta_box(
        'cg_recommendation_defaults',
        __('Recommendation Defaults', 'capiznon-geo'),
        'capiznon_geo_recommendation_metabox_callback',
        'page',
        'side',
        'default'
    );
}
add_action('add_meta_boxes_page', 'capiznon_geo_register_recommendation_metabox');

function capiznon_geo_recommendation_metabox_callback($post) {
    $template = get_page_template_slug($post->ID);
    if ($template !== 'recommendations.php') {
        echo '<p class="description">' . esc_html__('Assign the "Recommendations" template to use these defaults.', 'capiznon-geo') . '</p>';
        return;
    }

    wp_nonce_field('cg_recommendation_meta', 'cg_recommendation_nonce');

    $intent      = get_post_meta($post->ID, '_cg_rec_intent', true) ?: 'any';
    $vibes       = get_post_meta($post->ID, '_cg_rec_vibes', true);
    $constraints = get_post_meta($post->ID, '_cg_rec_constraints', true);
    $group       = get_post_meta($post->ID, '_cg_rec_group', true) ?: 'any';
    $lat         = get_post_meta($post->ID, '_cg_rec_lat', true);
    $lng         = get_post_meta($post->ID, '_cg_rec_lng', true);

    $vibes = is_array($vibes) ? $vibes : [];
    $constraints = is_array($constraints) ? $constraints : [];

    $intent_options = [
        'any'           => __('Surprise me', 'capiznon-geo'),
        'food-dining'   => __('Eat & drink', 'capiznon-geo'),
        'accommodation' => __('Places to stay', 'capiznon-geo'),
        'attractions'   => __('Things to see', 'capiznon-geo'),
        'shopping'      => __('Shopping', 'capiznon-geo'),
    ];

    $vibe_options = [
        'cozy-quiet'       => __('Quiet & cozy', 'capiznon-geo'),
        'family-friendly'  => __('Family-friendly', 'capiznon-geo'),
        'insta-worthy'     => __('Instagrammable views', 'capiznon-geo'),
        'lively-night-out' => __('Night out / barkada', 'capiznon-geo'),
        'romantic'         => __('Romantic / special', 'capiznon-geo'),
        'any'              => __("I don't mind", 'capiznon-geo'),
    ];

    $constraint_options = [
        'near'          => __('Near me / short ride', 'capiznon-geo'),
        'budget'        => __('Budget-friendly', 'capiznon-geo'),
        'open-now'      => __('Open now / tonight', 'capiznon-geo'),
        'beachfront'    => __('Beachfront / nature', 'capiznon-geo'),
        'indoor-aircon' => __('Air-conditioned / indoor', 'capiznon-geo'),
        'parking'       => __('Good parking / accessible', 'capiznon-geo'),
    ];

    $groups = [
        'any'     => __('No preference', 'capiznon-geo'),
        'solo'    => __('Just me', 'capiznon-geo'),
        'couple'  => __('Couple', 'capiznon-geo'),
        'family'  => __('Family with kids', 'capiznon-geo'),
        'barkada' => __('Barkada / big group', 'capiznon-geo'),
        'work'    => __('Work / formal', 'capiznon-geo'),
    ];
    ?>
    <p>
        <label for="cg_rec_intent"><strong><?php esc_html_e('Intent', 'capiznon-geo'); ?></strong></label><br>
        <select name="cg_rec_intent" id="cg_rec_intent" class="widefat">
            <?php foreach ($intent_options as $val => $label) : ?>
                <option value="<?php echo esc_attr($val); ?>" <?php selected($intent, $val); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <strong><?php esc_html_e('Vibes (multi-select)', 'capiznon-geo'); ?></strong><br>
        <?php foreach ($vibe_options as $val => $label) : ?>
            <label style="display:block;margin-bottom:4px;">
                <input type="checkbox" name="cg_rec_vibes[]" value="<?php echo esc_attr($val); ?>" <?php checked(in_array($val, $vibes, true)); ?>>
                <?php echo esc_html($label); ?>
            </label>
        <?php endforeach; ?>
    </p>
    <p>
        <strong><?php esc_html_e('Constraints (multi-select)', 'capiznon-geo'); ?></strong><br>
        <?php foreach ($constraint_options as $val => $label) : ?>
            <label style="display:block;margin-bottom:4px;">
                <input type="checkbox" name="cg_rec_constraints[]" value="<?php echo esc_attr($val); ?>" <?php checked(in_array($val, $constraints, true)); ?>>
                <?php echo esc_html($label); ?>
            </label>
        <?php endforeach; ?>
    </p>
    <p>
        <label for="cg_rec_group"><strong><?php esc_html_e('Group', 'capiznon-geo'); ?></strong></label><br>
        <select name="cg_rec_group" id="cg_rec_group" class="widefat">
            <?php foreach ($groups as $val => $label) : ?>
                <option value="<?php echo esc_attr($val); ?>" <?php selected($group, $val); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="cg_rec_lat"><strong><?php esc_html_e('Latitude (optional)', 'capiznon-geo'); ?></strong></label>
        <input type="text" name="cg_rec_lat" id="cg_rec_lat" value="<?php echo esc_attr($lat); ?>" class="widefat" placeholder="11.58">
    </p>
    <p>
        <label for="cg_rec_lng"><strong><?php esc_html_e('Longitude (optional)', 'capiznon-geo'); ?></strong></label>
        <input type="text" name="cg_rec_lng" id="cg_rec_lng" value="<?php echo esc_attr($lng); ?>" class="widefat" placeholder="122.75">
    </p>
    <p class="description"><?php esc_html_e('Defaults apply when this page is used as the Recommendations target.', 'capiznon-geo'); ?></p>
    <?php
}

function capiznon_geo_save_recommendation_meta($post_id) {
    if (!isset($_POST['cg_recommendation_nonce']) || !wp_verify_nonce($_POST['cg_recommendation_nonce'], 'cg_recommendation_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $template = get_page_template_slug($post_id);
    if ($template !== 'recommendations.php') {
        return;
    }

    $allowed_intent = ['any', 'food-dining', 'accommodation', 'attractions', 'shopping'];
    $intent = isset($_POST['cg_rec_intent']) ? sanitize_text_field($_POST['cg_rec_intent']) : 'any';
    if (!in_array($intent, $allowed_intent, true)) {
        $intent = 'any';
    }
    update_post_meta($post_id, '_cg_rec_intent', $intent);

    $allowed_vibes = ['cozy-quiet', 'family-friendly', 'insta-worthy', 'lively-night-out', 'romantic', 'any'];
    $vibes = [];
    if (!empty($_POST['cg_rec_vibes']) && is_array($_POST['cg_rec_vibes'])) {
        foreach ($_POST['cg_rec_vibes'] as $v) {
            $v = sanitize_text_field($v);
            if (in_array($v, $allowed_vibes, true)) {
                $vibes[] = $v;
            }
        }
    }
    update_post_meta($post_id, '_cg_rec_vibes', array_values(array_unique($vibes)));

    $allowed_constraints = ['near', 'budget', 'open-now', 'beachfront', 'indoor-aircon', 'parking'];
    $constraints = [];
    if (!empty($_POST['cg_rec_constraints']) && is_array($_POST['cg_rec_constraints'])) {
        foreach ($_POST['cg_rec_constraints'] as $c) {
            $c = sanitize_text_field($c);
            if (in_array($c, $allowed_constraints, true)) {
                $constraints[] = $c;
            }
        }
    }
    update_post_meta($post_id, '_cg_rec_constraints', array_values(array_unique($constraints)));

    $allowed_groups = ['any', 'solo', 'couple', 'family', 'barkada', 'work'];
    $group = isset($_POST['cg_rec_group']) ? sanitize_text_field($_POST['cg_rec_group']) : 'any';
    if (!in_array($group, $allowed_groups, true)) {
        $group = 'any';
    }
    update_post_meta($post_id, '_cg_rec_group', $group);

    $lat = isset($_POST['cg_rec_lat']) && is_numeric($_POST['cg_rec_lat']) ? floatval($_POST['cg_rec_lat']) : '';
    $lng = isset($_POST['cg_rec_lng']) && is_numeric($_POST['cg_rec_lng']) ? floatval($_POST['cg_rec_lng']) : '';
    update_post_meta($post_id, '_cg_rec_lat', $lat);
    update_post_meta($post_id, '_cg_rec_lng', $lng);
}
add_action('save_post_page', 'capiznon_geo_save_recommendation_meta');

/**
 * Register recommendation defaults meta for REST
 */
function capiznon_geo_register_recommendation_meta() {
    $fields = [
        '_cg_rec_intent'      => ['type' => 'string'],
        '_cg_rec_vibes'       => ['type' => 'array', 'items' => ['type' => 'string']],
        '_cg_rec_constraints' => ['type' => 'array', 'items' => ['type' => 'string']],
        '_cg_rec_group'       => ['type' => 'string'],
        '_cg_rec_lat'         => ['type' => 'number'],
        '_cg_rec_lng'         => ['type' => 'number'],
    ];

    foreach ($fields as $key => $args) {
        register_post_meta('page', $key, [
            'show_in_rest'  => isset($args['items'])
                ? [
                    'schema' => [
                        'type'  => 'array',
                        'items' => $args['items'],
                    ],
                ]
                : true,
            'single'        => true,
            'type'          => $args['type'],
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);
    }
}
add_action('init', 'capiznon_geo_register_recommendation_meta');
