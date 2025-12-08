<?php
/**
 * Capiznon Geo - Scaffold Data Seeder
 * 
 * This script populates the theme with initial data:
 * - Location types (Food & Dining, Accommodation, Attractions, Shopping)
 * - Areas (municipalities/cities in Capiz)
 * - Tags (amenities and features)
 * - Price ranges
 * - Sample locations (optional)
 *
 * @package Capiznon_Geo
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

class Capiznon_Geo_Scaffold
{
    /**
     * Initialize the scaffold system
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'handle_form_submission']);
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=cg_location',
            __('Data Scaffold', 'capiznon-geo'),
            __('Data Scaffold', 'capiznon-geo'),
            'manage_options',
            'capiznon-geo-scaffold',
            [$this, 'render_admin_page']
        );
    }

    /**
     * Handle form submission
     */
    public function handle_form_submission()
    {
        if (!isset($_POST['capiznon_scaffold_nonce']) || !wp_verify_nonce($_POST['capiznon_scaffold_nonce'], 'capiznon_scaffold_action')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['scaffold_action'])) {
            $action = sanitize_text_field($_POST['scaffold_action']);
            
            switch ($action) {
                case 'seed_all':
                    $this->seed_location_types();
                    $this->seed_areas();
                    $this->seed_tags();
                    $this->seed_price_ranges();
                    $this->seed_cuisines();
                    add_action('admin_notices', [$this, 'show_success_notice']);
                    break;
                    
                case 'seed_types':
                    $this->seed_location_types();
                    add_action('admin_notices', [$this, 'show_success_notice']);
                    break;
                    
                case 'seed_areas':
                    $this->seed_areas();
                    add_action('admin_notices', [$this, 'show_success_notice']);
                    break;
                    
                case 'seed_tags':
                    $this->seed_tags();
                    add_action('admin_notices', [$this, 'show_success_notice']);
                    break;
                    
                case 'seed_prices':
                    $this->seed_price_ranges();
                    add_action('admin_notices', [$this, 'show_success_notice']);
                    break;
                    
                case 'seed_cuisines':
                    $this->seed_cuisines();
                    add_action('admin_notices', [$this, 'show_success_notice']);
                    break;
                    
                case 'seed_sample_locations':
                    $this->seed_sample_locations();
                    add_action('admin_notices', [$this, 'show_success_notice']);
                    break;
            }
        }
    }

    /**
     * Seed location types
     */
    private function seed_location_types()
    {
        $types = [
            'food-dining' => [
                'name' => __('Food & Dining', 'capiznon-geo'),
                'description' => __('Restaurants, cafes, and food establishments', 'capiznon-geo'),
                'icon' => 'restaurant',
                'children' => [
                    'restaurants' => __('Restaurants', 'capiznon-geo'),
                    'cafes' => __('Cafes & Coffee Shops', 'capiznon-geo'),
                    'bars' => __('Bars & Nightlife', 'capiznon-geo'),
                    'street-food' => __('Street Food', 'capiznon-geo'),
                    'bakeries' => __('Bakeries', 'capiznon-geo'),
                ]
            ],
            'accommodation' => [
                'name' => __('Accommodation', 'capiznon-geo'),
                'description' => __('Hotels, resorts, and lodging', 'capiznon-geo'),
                'icon' => 'hotel',
                'children' => [
                    'hotels' => __('Hotels', 'capiznon-geo'),
                    'resorts' => __('Resorts', 'capiznon-geo'),
                    'guesthouses' => __('Guesthouses', 'capiznon-geo'),
                    'homestays' => __('Homestays', 'capiznon-geo'),
                    'hostels' => __('Hostels', 'capiznon-geo'),
                ]
            ],
            'attractions' => [
                'name' => __('Attractions', 'capiznon-geo'),
                'description' => __('Tourist spots and attractions', 'capiznon-geo'),
                'icon' => 'camera',
                'children' => [
                    'beaches' => __('Beaches', 'capiznon-geo'),
                    'historical' => __('Historical Sites', 'capiznon-geo'),
                    'parks' => __('Parks & Nature', 'capiznon-geo'),
                    'museums' => __('Museums', 'capiznon-geo'),
                    'religious' => __('Religious Sites', 'capiznon-geo'),
                ]
            ],
            'shopping' => [
                'name' => __('Shopping', 'capiznon-geo'),
                'description' => __('Markets, malls, and shopping areas', 'capiznon-geo'),
                'icon' => 'shopping-cart',
                'children' => [
                    'markets' => __('Markets', 'capiznon-geo'),
                    'malls' => __('Shopping Centers', 'capiznon-geo'),
                    'souvenirs' => __('Souvenir Shops', 'capiznon-geo'),
                    'local-products' => __('Local Products', 'capiznon-geo'),
                ]
            ],
            'services' => [
                'name' => __('Services', 'capiznon-geo'),
                'description' => __('Essential services and facilities', 'capiznon-geo'),
                'icon' => 'info',
                'children' => [
                    'banks' => __('Banks & ATMs', 'capiznon-geo'),
                    'hospitals' => __('Hospitals & Clinics', 'capiznon-geo'),
                    'government' => __('Government Offices', 'capiznon-geo'),
                    'transport' => __('Transportation', 'capiznon-geo'),
                ]
            ]
        ];

        foreach ($types as $slug => $data) {
            // Create parent type
            $parent_term = wp_insert_term($data['name'], 'location_type', [
                'slug' => $slug,
                'description' => $data['description']
            ]);

            if (!is_wp_error($parent_term)) {
                // Add icon as term meta
                update_term_meta($parent_term['term_id'], '_cg_icon', $data['icon']);
                
                // Create child types
                foreach ($data['children'] as $child_slug => $child_name) {
                    $child_term = wp_insert_term($child_name, 'location_type', [
                        'slug' => $child_slug,
                        'parent' => $parent_term['term_id']
                    ]);
                    
                    if (!is_wp_error($child_term)) {
                        // Inherit parent icon or set specific one
                        update_term_meta($child_term['term_id'], '_cg_icon', $data['icon']);
                    }
                }
            }
        }
    }

    /**
     * Seed areas (municipalities and cities in Capiz)
     */
    private function seed_areas()
    {
        $areas = [
            'roxas-city' => __('Roxas City', 'capiznon-geo'),
            'panay' => __('Panay', 'capiznon-geo'),
            'panitan' => __('Panitan', 'capiznon-geo'),
            'pilar' => __('Pilar', 'capiznon-geo'),
            'pontevedra' => __('Pontevedra', 'capiznon-geo'),
            'president-roxas' => __('President Roxas', 'capiznon-geo'),
            'sapi-an' => __('Sapi-an', 'capiznon-geo'),
            'sigma' => __('Sigma', 'capiznon-geo'),
            'tapaz' => __('Tapaz', 'capiznon-geo'),
            'dumarao' => __('Dumarao', 'capiznon-geo'),
            'ivisan' => __('Ivisan', 'capiznon-geo'),
            'jamindan' => __('Jamindan', 'capiznon-geo'),
            'ma-ayon' => __('Ma-ayon', 'capiznon-geo'),
            'mambusao' => __('Mambusao', 'capiznon-geo'),
            'dao' => __('Dao', 'capiznon-geo'),
            'dumalag' => __('Dumalag', 'capiznon-geo'),
        ];

        foreach ($areas as $slug => $name) {
            wp_insert_term($name, 'location_area', [
                'slug' => $slug
            ]);
        }
    }

    /**
     * Seed tags (amenities and features)
     */
    private function seed_tags()
    {
        $tags = [
            'wifi' => __('WiFi', 'capiznon-geo'),
            'parking' => __('Parking', 'capiznon-geo'),
            'aircon' => __('Air Conditioning', 'capiznon-geo'),
            'pet-friendly' => __('Pet Friendly', 'capiznon-geo'),
            'wheelchair-accessible' => __('Wheelchair Accessible', 'capiznon-geo'),
            'outdoor-seating' => __('Outdoor Seating', 'capiznon-geo'),
            'delivery' => __('Delivery Available', 'capiznon-geo'),
            'takeout' => __('Takeout Available', 'capiznon-geo'),
            'reservations' => __('Reservations Accepted', 'capiznon-geo'),
            'credit-card' => __('Credit Card Accepted', 'capiznon-geo'),
            'family-friendly' => __('Family Friendly', 'capiznon-geo'),
            'kids-playground' => __('Kids Playground', 'capiznon-geo'),
            'swimming-pool' => __('Swimming Pool', 'capiznon-geo'),
            'beach-access' => __('Beach Access', 'capiznon-geo'),
            'gym' => __('Gym/Fitness', 'capiznon-geo'),
            'spa' => __('Spa & Wellness', 'capiznon-geo'),
            '24-hours' => __('24 Hours', 'capiznon-geo'),
            'halal' => __('Halal', 'capiznon-geo'),
            'vegetarian' => __('Vegetarian Options', 'capiznon-geo'),
            'seafood-specialty' => __('Seafood Specialty', 'capiznon-geo'),
            'local-cuisine' => __('Local Cuisine', 'capiznon-geo'),
            'tourist-friendly' => __('Tourist Friendly', 'capiznon-geo'),
            'photo-spot' => __('Photo Spot', 'capiznon-geo'),
            'gift-shop' => __('Gift Shop', 'capiznon-geo'),
            'guided-tours' => __('Guided Tours', 'capiznon-geo'),
        ];

        foreach ($tags as $slug => $name) {
            wp_insert_term($name, 'location_tag', [
                'slug' => $slug
            ]);
        }
    }

    /**
     * Seed price ranges
     */
    private function seed_price_ranges()
    {
        $prices = [
            'budget' => __('Budget ($)', 'capiznon-geo'),
            'moderate' => __('Moderate ($$)', 'capiznon-geo'),
            'expensive' => __('Expensive ($$$)', 'capiznon-geo'),
            'luxury' => __('Luxury ($$$$)', 'capiznon-geo'),
        ];

        foreach ($prices as $slug => $name) {
            wp_insert_term($name, 'location_price', [
                'slug' => $slug
            ]);
        }
    }

    /**
     * Seed cuisines
     */
    private function seed_cuisines()
    {
        $cuisines = [
            'filipino' => __('Filipino', 'capiznon-geo'),
            'capiznon' => __('Capiznon Local', 'capiznon-geo'),
            'seafood' => __('Seafood', 'capiznon-geo'),
            'chinese' => __('Chinese', 'capiznon-geo'),
            'american' => __('American', 'capiznon-geo'),
            'italian' => __('Italian', 'capiznon-geo'),
            'japanese' => __('Japanese', 'capiznon-geo'),
            'korean' => __('Korean', 'capiznon-geo'),
            'thai' => __('Thai', 'capiznon-geo'),
            'vietnamese' => __('Vietnamese', 'capiznon-geo'),
            'indian' => __('Indian', 'capiznon-geo'),
            'mediterranean' => __('Mediterranean', 'capiznon-geo'),
            'mexican' => __('Mexican', 'capiznon-geo'),
            'cafe' => __('Cafe', 'capiznon-geo'),
            'bakery' => __('Bakery', 'capiznon-geo'),
            'barbecue' => __('Barbecue', 'capiznon-geo'),
            'vegetarian' => __('Vegetarian/Vegan', 'capiznon-geo'),
            'desserts' => __('Desserts & Sweets', 'capiznon-geo'),
            'international' => __('International', 'capiznon-geo'),
            'fusion' => __('Fusion', 'capiznon-geo'),
        ];

        foreach ($cuisines as $slug => $name) {
            wp_insert_term($name, 'location_cuisine', [
                'slug' => $slug
            ]);
        }
    }

    /**
     * Seed sample locations (optional)
     */
    private function seed_sample_locations()
    {
        $sample_locations = [
            [
                'title' => __('Baybay Beach', 'capiznon-geo'),
                'content' => __('A beautiful black sand beach perfect for swimming and sunset viewing. Located along the coast of Roxas City, this beach is a popular spot for locals and tourists alike.', 'capiznon-geo'),
                'type' => 'beaches',
                'area' => 'roxas-city',
                'lat' => 11.5853,
                'lng' => 122.7499,
                'featured' => true,
                'tags' => ['beach-access', 'photo-spot', 'family-friendly'],
            ],
            [
                'title' => __('Santa Monica Church', 'capiznon-geo'),
                'content' => __('A historic Spanish colonial church known for its beautiful architecture and religious significance. One of the oldest churches in Panay Island.', 'capiznon-geo'),
                'type' => 'religious',
                'area' => 'panay',
                'lat' => 11.5167,
                'lng' => 122.7833,
                'featured' => true,
                'tags' => ['historical', 'tourist-friendly', 'photo-spot'],
            ],
            [
                'title' => __('Capiz Provincial Capitol', 'capiznon-geo'),
                'content' => __('The seat of government of Capiz province. This beautiful colonial building is an architectural landmark and a must-visit for history enthusiasts.', 'capiznon-geo'),
                'type' => 'historical',
                'area' => 'roxas-city',
                'lat' => 11.5853,
                'lng' => 122.7499,
                'featured' => false,
                'tags' => ['historical', 'government', 'photo-spot'],
            ],
        ];

        foreach ($sample_locations as $location_data) {
            $post_id = wp_insert_post([
                'post_title' => $location_data['title'],
                'post_content' => $location_data['content'],
                'post_status' => 'publish',
                'post_type' => 'cg_location',
                'post_excerpt' => wp_trim_words($location_data['content'], 20),
            ]);

            if ($post_id && !is_wp_error($post_id)) {
                // Set location type
                if (isset($location_data['type'])) {
                    wp_set_object_terms($post_id, [$location_data['type']], 'location_type');
                }

                // Set area
                if (isset($location_data['area'])) {
                    wp_set_object_terms($post_id, [$location_data['area']], 'location_area');
                }

                // Set tags
                if (isset($location_data['tags'])) {
                    wp_set_object_terms($post_id, $location_data['tags'], 'location_tag');
                }

                // Set coordinates
                update_post_meta($post_id, '_cg_latitude', $location_data['lat']);
                update_post_meta($post_id, '_cg_longitude', $location_data['lng']);

                // Set featured status
                update_post_meta($post_id, '_cg_featured', $location_data['featured'] ? '1' : '0');
            }
        }
    }

    /**
     * Show success notice
     */
    public function show_success_notice()
    {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong><?php _e('Success!', 'capiznon-geo'); ?></strong> <?php _e('Scaffold data has been populated successfully.', 'capiznon-geo'); ?></p>
        </div>
        <?php
    }

    /**
     * Render admin page
     */
    public function render_admin_page()
    {
        ?>
        <div class="wrap">
            <h1><?php _e('Capiznon Geo Data Scaffold', 'capiznon-geo'); ?></h1>
            
            <div class="card">
                <h2><?php _e('Populate Initial Data', 'capiznon-geo'); ?></h2>
                <p><?php _e('This tool will populate your WordPress site with initial data for the Capiznon Geo theme. This includes location types, areas, tags, price ranges, and cuisines specific to Capiz province.', 'capiznon-geo'); ?></p>
                
                <p class="description">
                    <?php _e('<strong>Note:</strong> This will not duplicate existing data. It only creates terms that don\'t already exist.', 'capiznon-geo'); ?>
                </p>

                <form method="post" action="">
                    <?php wp_nonce_field('capiznon_scaffold_action', 'capiznon_scaffold_nonce'); ?>
                    
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><?php _e('Location Types', 'capiznon-geo'); ?></th>
                                <td>
                                    <p><?php _e('Creates hierarchical location types like Food & Dining, Accommodation, Attractions, etc.', 'capiznon-geo'); ?></p>
                                    <button type="submit" name="scaffold_action" value="seed_types" class="button">
                                        <?php _e('Create Location Types', 'capiznon-geo'); ?>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Areas', 'capiznon-geo'); ?></th>
                                <td>
                                    <p><?php _e('Creates all municipalities and cities in Capiz province.', 'capiznon-geo'); ?></p>
                                    <button type="submit" name="scaffold_action" value="seed_areas" class="button">
                                        <?php _e('Create Areas', 'capiznon-geo'); ?>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Tags', 'capiznon-geo'); ?></th>
                                <td>
                                    <p><?php _e('Creates amenity tags like WiFi, Parking, Pet-friendly, etc.', 'capiznon-geo'); ?></p>
                                    <button type="submit" name="scaffold_action" value="seed_tags" class="button">
                                        <?php _e('Create Tags', 'capiznon-geo'); ?>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Price Ranges', 'capiznon-geo'); ?></th>
                                <td>
                                    <p><?php _e('Creates price range categories from Budget to Luxury.', 'capiznon-geo'); ?></p>
                                    <button type="submit" name="scaffold_action" value="seed_prices" class="button">
                                        <?php _e('Create Price Ranges', 'capiznon-geo'); ?>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Cuisines', 'capiznon-geo'); ?></th>
                                <td>
                                    <p><?php _e('Creates cuisine types for restaurants and food establishments.', 'capiznon-geo'); ?></p>
                                    <button type="submit" name="scaffold_action" value="seed_cuisines" class="button">
                                        <?php _e('Create Cuisines', 'capiznon-geo'); ?>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Sample Locations', 'capiznon-geo'); ?></th>
                                <td>
                                    <p><?php _e('Creates a few sample locations to demonstrate the theme functionality.', 'capiznon-geo'); ?></p>
                                    <button type="submit" name="scaffold_action" value="seed_sample_locations" class="button">
                                        <?php _e('Create Sample Locations', 'capiznon-geo'); ?>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>
                    
                    <p>
                        <button type="submit" name="scaffold_action" value="seed_all" class="button button-primary">
                            <?php _e('Create All Data', 'capiznon-geo'); ?>
                        </button>
                        <span class="description"><?php _e('This will create all of the above data types at once.', 'capiznon-geo'); ?></span>
                    </p>
                </form>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2><?php _e('Current Data Status', 'capiznon-geo'); ?></h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Taxonomy', 'capiznon-geo'); ?></th>
                            <th><?php _e('Term Count', 'capiznon-geo'); ?></th>
                            <th><?php _e('Status', 'capiznon-geo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $taxonomies = [
                            'location_type' => __('Location Types', 'capiznon-geo'),
                            'location_area' => __('Areas', 'capiznon-geo'),
                            'location_tag' => __('Tags', 'capiznon-geo'),
                            'location_price' => __('Price Ranges', 'capiznon-geo'),
                            'location_cuisine' => __('Cuisines', 'capiznon-geo'),
                        ];

                        foreach ($taxonomies as $taxonomy => $label) {
                            $count = wp_count_terms($taxonomy, ['hide_empty' => false]);
                            $status = $count > 0 ? __('✅ Populated', 'capiznon-geo') : __('❌ Empty', 'capiznon-geo');
                            ?>
                            <tr>
                                <td><strong><?php echo $label; ?></strong></td>
                                <td><?php echo number_format_i18n($count); ?></td>
                                <td><?php echo $status; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                
                <p style="margin-top: 15px;">
                    <strong><?php _e('Locations:', 'capiznon-geo'); ?></strong> 
                    <?php 
                    $location_count = wp_count_posts('cg_location');
                    echo sprintf(
                        __('%d published, %d total', 'capiznon-geo'),
                        $location_count->publish,
                        $location_count->publish + $location_count->draft
                    );
                    ?>
                </p>
            </div>
        </div>
        <?php
    }
}

// Initialize the scaffold system
new Capiznon_Geo_Scaffold();
