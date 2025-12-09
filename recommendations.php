<?php
/**
 * Template Name: Recommendations
 *
 * @package Capiznon_Geo
 */

get_header();

$raw     = $_GET ?? [];
$params  = Capiznon_Geo_Recommender::normalize_params($raw);
$data    = Capiznon_Geo_Recommender::get_recommendations($params);
$results = $data['results'] ?? [];
$explain = $data['explanation'] ?? [];
?>

<main id="main" class="flex-1 bg-white/70 backdrop-blur-sm min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <div class="bg-white/90 border border-amber-100 rounded-2xl shadow-sm p-6">
            <p class="text-xs uppercase tracking-wide text-amber-700 font-semibold mb-2"><?php esc_html_e('Recommendations', 'capiznon-geo'); ?></p>
            <h1 class="text-3xl font-bold text-amber-900 mb-3"><?php esc_html_e('Places picked for you', 'capiznon-geo'); ?></h1>
            <?php if (!empty($explain)) : ?>
                <p class="text-sm text-amber-800">
                    <?php esc_html_e('Because you chose:', 'capiznon-geo'); ?>
                    <span class="font-semibold"><?php echo esc_html(implode(' • ', $explain)); ?></span>
                </p>
            <?php else : ?>
                <p class="text-sm text-amber-700"><?php esc_html_e('Browse your personalized picks.', 'capiznon-geo'); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($results)) : ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php
                foreach ($results as $result) :
                    $post_obj = get_post($result['ID']);
                    if (!$post_obj) {
                        continue;
                    }
                    setup_postdata($post_obj);
                    ?>
                    <div class="bg-white/90 border border-amber-100 rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <a href="<?php echo esc_url($result['permalink']); ?>" class="block">
                            <?php if (!empty($result['thumbnail_url'])) : ?>
                                <div class="aspect-video overflow-hidden bg-amber-50">
                                    <img src="<?php echo esc_url($result['thumbnail_url']); ?>" alt="<?php echo esc_attr($result['title']); ?>" class="w-full h-full object-cover">
                                </div>
                            <?php else : ?>
                                <div class="aspect-video flex items-center justify-center bg-gradient-to-br from-amber-100 to-orange-100">
                                    <span class="text-4xl">🏝️</span>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="p-4 space-y-2">
                            <a href="<?php echo esc_url($result['permalink']); ?>" class="block">
                                <h2 class="text-lg font-semibold text-amber-900 leading-tight"><?php echo esc_html($result['title']); ?></h2>
                            </a>
                            <div class="flex items-center justify-between text-xs text-amber-700">
                                <?php if ($result['distance_km'] !== null) : ?>
                                    <span><?php echo esc_html(sprintf(__('%.1f km away', 'capiznon-geo'), $result['distance_km'])); ?></span>
                                <?php endif; ?>
                                <span class="ml-auto font-semibold text-amber-800"><?php echo esc_html(number_format($result['score'], 1)); ?></span>
                            </div>
                            <?php if (!empty($result['tags'])) : ?>
                                <div class="flex flex-wrap gap-1.5">
                                    <?php foreach ($result['tags'] as $tag_label) : ?>
                                        <span class="px-2 py-1 bg-amber-50 text-amber-800 text-[11px] font-semibold rounded-full border border-amber-100"><?php echo esc_html($tag_label); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <div class="bg-white/90 border border-amber-100 rounded-2xl shadow-sm p-8 text-center text-amber-800">
                <p class="text-lg font-semibold mb-2"><?php esc_html_e("We don't have a great match yet.", 'capiznon-geo'); ?></p>
                <p class="text-sm mb-4"><?php esc_html_e('Try adjusting your answers or browse all places.', 'capiznon-geo'); ?></p>
                <div class="flex justify-center gap-3">
                    <a class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold" href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>"><?php esc_html_e('Browse all locations', 'capiznon-geo'); ?></a>
                    <a class="px-4 py-2 rounded-xl bg-white border border-amber-200 text-amber-800 font-semibold" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back home', 'capiznon-geo'); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
