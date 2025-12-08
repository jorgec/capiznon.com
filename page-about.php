<?php
/**
 * Template Name: About Page
 *
 * @package Capiznon_Geo
 */

get_header();
?>

<style>
    .about-page-bg {
        background-color: #F8F2E6;
    }
    .about-content strong,
    .about-content b {
        font-weight: 800;
        color: #78350f;
    }
    .about-content p {
        font-weight: 500;
        line-height: 1.8;
        color: #451a03;
    }
    .about-hero {
        background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/about.jpg'); ?>');
        background-size: cover;
        background-position: center;
    }
</style>

<main id="main" class="flex-1 min-h-screen about-page-bg">
    
    <!-- Hero Section with Background Image -->
    <div class="about-hero relative h-72 md:h-96 lg:h-[28rem]">
        <div class="absolute inset-0 bg-gradient-to-r from-black/50 via-black/30 to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-end pb-12">
            <?php while (have_posts()) : the_post(); ?>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white drop-shadow-lg tracking-tight" style="font-family: 'Sigmar One', cursive;">
                <?php the_title(); ?>
            </h1>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="max-w-3xl">
            <?php while (have_posts()) : the_post(); ?>
            <article class="about-content text-lg md:text-xl leading-relaxed">
                <?php the_content(); ?>
            </article>
            <?php endwhile; ?>
        </div>
    </div>

</main>

<?php
get_footer();
