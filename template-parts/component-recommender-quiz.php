<?php
/**
 * Recommendation Quiz Component
 *
 * @package Capiznon_Geo
 */

defined('ABSPATH') || exit;

// Ensure script is registered and localized with recommendations URL
$recommendations_page = get_page_by_path('recommendations');
$recommendations_url = $recommendations_page ? get_permalink($recommendations_page->ID) : home_url('/recommendations/');

wp_enqueue_script('capiznon-geo-recommender-quiz');
wp_localize_script('capiznon-geo-recommender-quiz', 'cgRecommenderData', [
    'recommendationsUrl' => $recommendations_url,
]);
?>

<section id="cg-recommender-quiz" data-cg-recommender-root class="mx-4 mb-8 bg-white/80 backdrop-blur-sm border border-amber-100 rounded-2xl shadow-sm">
    <div class="p-5 sm:p-6 space-y-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-amber-700 font-semibold"><?php esc_html_e('Not sure where to go?', 'capiznon-geo'); ?></p>
                <h2 class="text-xl font-bold text-amber-900 mt-1"><?php esc_html_e('Answer 4 quick questions and get suggestions', 'capiznon-geo'); ?></h2>
            </div>
            <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-3 py-1 rounded-full" data-cg-step-indicator>Step 1 of 4</span>
        </div>

        <div class="space-y-5">
            <!-- Step 1: Intent -->
            <div data-cg-step="1" class="cg-quiz-step space-y-3">
                <h3 class="text-sm font-semibold text-amber-900"><?php esc_html_e('What do you feel like doing?', 'capiznon-geo'); ?></h3>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $intents = [
                        'food-dining'   => __('Eat & drink', 'capiznon-geo'),
                        'accommodation' => __('Places to stay', 'capiznon-geo'),
                        'attractions'   => __('Things to see', 'capiznon-geo'),
                        'shopping'      => __('Shopping', 'capiznon-geo'),
                        'any'           => __('Surprise me', 'capiznon-geo'),
                    ];
                    foreach ($intents as $value => $label) :
                    ?>
                        <button type="button" class="cg-quiz-chip" data-cg-step="1" data-cg-answer="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Step 2: Vibe -->
            <div data-cg-step="2" class="cg-quiz-step space-y-3 hidden">
                <h3 class="text-sm font-semibold text-amber-900"><?php esc_html_e('What vibe are you looking for?', 'capiznon-geo'); ?></h3>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $vibes = [
                        'cozy-quiet'       => __('Quiet & cozy', 'capiznon-geo'),
                        'family-friendly'  => __('Family-friendly', 'capiznon-geo'),
                        'insta-worthy'     => __('Instagrammable views', 'capiznon-geo'),
                        'lively-night-out' => __('Night out / barkada', 'capiznon-geo'),
                        'romantic'         => __('Romantic / special', 'capiznon-geo'),
                        'any'              => __("I don't mind", 'capiznon-geo'),
                    ];
                    foreach ($vibes as $value => $label) :
                    ?>
                        <button type="button" class="cg-quiz-chip" data-cg-step="2" data-cg-answer="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Step 3: Constraints (multi) -->
            <div data-cg-step="3" class="cg-quiz-step space-y-3 hidden">
                <h3 class="text-sm font-semibold text-amber-900"><?php esc_html_e('Any practical needs?', 'capiznon-geo'); ?></h3>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $constraints = [
                        'near'          => __('Near me / short ride', 'capiznon-geo'),
                        'budget'        => __('Budget-friendly', 'capiznon-geo'),
                        'open-now'      => __('Open now / tonight', 'capiznon-geo'),
                        'beachfront'    => __('Beachfront / nature', 'capiznon-geo'),
                        'indoor-aircon' => __('Air-conditioned / indoor', 'capiznon-geo'),
                        'parking'       => __('Good parking / accessible', 'capiznon-geo'),
                    ];
                    foreach ($constraints as $value => $label) :
                    ?>
                        <button type="button" class="cg-quiz-chip cg-quiz-chip-multi" data-cg-step="3" data-cg-answer="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Step 4: Group -->
            <div data-cg-step="4" class="cg-quiz-step space-y-3 hidden">
                <h3 class="text-sm font-semibold text-amber-900"><?php esc_html_e('Who are you with?', 'capiznon-geo'); ?></h3>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $groups = [
                        'solo'    => __('Just me', 'capiznon-geo'),
                        'couple'  => __('Couple', 'capiznon-geo'),
                        'family'  => __('Family with kids', 'capiznon-geo'),
                        'barkada' => __('Barkada / big group', 'capiznon-geo'),
                        'work'    => __('Work / formal', 'capiznon-geo'),
                        'any'     => __('No preference', 'capiznon-geo'),
                    ];
                    foreach ($groups as $value => $label) :
                    ?>
                        <button type="button" class="cg-quiz-chip" data-cg-step="4" data-cg-answer="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <button type="button" class="text-sm font-semibold text-amber-800 hover:text-amber-600 disabled:opacity-50" data-cg-quiz-prev><?php esc_html_e('Back', 'capiznon-geo'); ?></button>
            <div class="flex items-center gap-2">
                <button type="button" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-amber-800 border border-amber-200 hover:bg-amber-50 disabled:opacity-50" data-cg-quiz-next><?php esc_html_e('Next', 'capiznon-geo'); ?></button>
                <button type="button" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-orange-500 shadow hover:shadow-md disabled:opacity-60" data-cg-quiz-submit><?php esc_html_e('Show places', 'capiznon-geo'); ?></button>
            </div>
        </div>
    </div>
</section>

<style>
    #cg-recommender-quiz .cg-quiz-step.hidden {
        display: none;
    }
    #cg-recommender-quiz .cg-quiz-chip {
        padding: 0.55rem 0.85rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #e7f1f3, #dce8ea);
        border: 1px solid rgba(59, 130, 146, 0.25);
        color: #1f3f46;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    #cg-recommender-quiz .cg-quiz-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(59, 130, 146, 0.15);
    }
    #cg-recommender-quiz .cg-quiz-chip.active {
        background: linear-gradient(135deg, #9bd6da, #7fbfc5);
        border-color: rgba(59, 130, 146, 0.45);
        color: #0f2c32;
        box-shadow: 0 12px 28px rgba(59, 130, 146, 0.22);
    }
</style>
