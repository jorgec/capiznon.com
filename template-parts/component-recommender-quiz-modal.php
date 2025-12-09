<?php
/**
 * Wrapper modal for recommender quiz
 *
 * @package Capiznon_Geo
 */
?>
<div id="cg-quiz-modal" class="cg-modal hidden" role="dialog" aria-modal="true" aria-labelledby="cg-quiz-modal-title">
    <div class="cg-modal-overlay"></div>
    <div class="cg-modal-content">
        <div class="flex items-center justify-between mb-3">
            <h2 id="cg-quiz-modal-title" class="text-lg font-bold text-amber-900"><?php esc_html_e('Quick quiz', 'capiznon-geo'); ?></h2>
            <button type="button" id="cg-close-quiz" class="text-amber-800 hover:text-amber-600 text-xl leading-none" aria-label="<?php esc_attr_e('Close quiz', 'capiznon-geo'); ?>">&times;</button>
        </div>
        <?php get_template_part('template-parts/component', 'recommender-quiz'); ?>
    </div>
    <template id="cg-quiz-modal-template"></template>
</div>
