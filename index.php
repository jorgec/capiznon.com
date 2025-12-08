<?php
/**
 * Main Index Template
 *
 * @package Capiznon_Geo
 */

get_header();
?>

<main id="main" class="flex-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        
        <?php if (have_posts()) : ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <?php while (have_posts()) : the_post(); ?>
                
                <article <?php post_class('bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="block aspect-video overflow-hidden">
                        <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover hover:scale-105 transition-transform duration-300']); ?>
                    </a>
                    <?php endif; ?>

                    <div class="p-6">
                        <header class="mb-4">
                            <h2 class="text-2xl font-bold text-gray-900 hover:text-primary-600 transition-colors">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                <span>•</span>
                                <span><?php the_author(); ?></span>
                            </div>
                        </header>

                        <div class="text-gray-600 mb-4">
                            <?php the_excerpt(); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="inline-flex items-center text-primary-600 font-medium hover:text-primary-700">
                            <?php esc_html_e('Read More', 'capiznon-geo'); ?> →
                        </a>
                    </div>
                </article>

                <?php endwhile; ?>
            </div>

            <!-- Sidebar -->
            <aside class="space-y-6">
                <?php get_sidebar(); ?>
            </aside>
        </div>

        <!-- Pagination -->
        <nav class="mt-8">
            <?php the_posts_pagination(); ?>
        </nav>

        <?php else : ?>
        
        <div class="text-center py-16">
            <h2 class="text-xl font-semibold text-gray-900 mb-2"><?php esc_html_e('Nothing found', 'capiznon-geo'); ?></h2>
            <p class="text-gray-500"><?php esc_html_e('Try searching for something else.', 'capiznon-geo'); ?></p>
        </div>

        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
