<?php
/**
 * Template Name: About Page
 *
 * @package Capiznon_Geo
 */

get_header();
?>

<style>
    :root {
        --about-cream: #F8F2E6;
        --about-warm: #E8DFD0;
        --about-brown: #78350f;
        --about-dark: #451a03;
    }

    .about-page {
        background-color: var(--about-cream);
    }

    /* Hero with parallax-like depth */
    .about-hero {
        position: relative;
        min-height: 70vh;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
    }

    .about-hero-image {
        position: absolute;
        inset: 0;
        background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/about.jpg'); ?>');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        transform: scale(1.02);
    }

    @media (max-width: 768px) {
        .about-hero-image {
            background-attachment: scroll;
        }
        .about-hero {
            min-height: 50vh;
        }
    }

    .about-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to top,
            rgba(69, 26, 3, 0.85) 0%,
            rgba(69, 26, 3, 0.4) 40%,
            rgba(0, 0, 0, 0.1) 100%
        );
    }

    .about-hero-content {
        position: relative;
        z-index: 10;
        padding-bottom: 4rem;
    }

    .about-title {
        font-family: 'Sigmar One', cursive;
        font-size: clamp(3rem, 10vw, 7rem);
        color: white;
        text-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
        letter-spacing: -0.02em;
        line-height: 1;
        margin: 0;
    }

    .about-subtitle {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 400;
        margin-top: 1rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    /* Content styling */
    .about-content-wrapper {
        position: relative;
        margin-top: -3rem;
        z-index: 20;
    }

    .about-card {
        background: white;
        border-radius: 2rem;
        box-shadow: 
            0 25px 50px -12px rgba(0, 0, 0, 0.15),
            0 0 0 1px rgba(120, 53, 15, 0.05);
        padding: 3rem 2rem;
        position: relative;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .about-card {
            padding: 4rem 5rem;
        }
    }

    .about-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #f59e0b, #ea580c, #dc2626);
    }

    /* Decorative accent */
    .about-card::after {
        content: '';
        position: absolute;
        top: 2rem;
        right: 2rem;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Typography */
    .about-body {
        font-size: 1.125rem;
        line-height: 2;
        color: var(--about-dark);
    }

    @media (min-width: 768px) {
        .about-body {
            font-size: 1.25rem;
            columns: 1;
        }
    }

    .about-body p {
        margin-bottom: 1.5rem;
    }

    .about-body strong,
    .about-body b {
        font-weight: 700;
        color: var(--about-brown);
        display: inline;
    }

    /* Section headers in content */
    .about-body p strong:first-child {
        display: block;
        font-size: 1.1em;
        margin-bottom: 0.25rem;
        color: var(--about-brown);
        letter-spacing: 0.02em;
    }

    /* Pull quote style for emphasis */
    .about-body em {
        font-style: normal;
        color: #b45309;
    }

    /* Decorative divider */
    .about-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin: 3rem 0;
        color: #d97706;
    }

    .about-divider::before,
    .about-divider::after {
        content: '';
        height: 1px;
        width: 60px;
        background: linear-gradient(90deg, transparent, #fbbf24, transparent);
    }

    /* Footer tagline */
    .about-tagline {
        text-align: center;
        padding: 4rem 0;
        color: var(--about-brown);
    }

    .about-tagline-text {
        font-family: 'Sigmar One', cursive;
        font-size: 1.5rem;
        opacity: 0.7;
    }

    /* Smooth scroll indicator */
    .scroll-indicator {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        opacity: 0.7;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
        40% { transform: translateX(-50%) translateY(-10px); }
        60% { transform: translateX(-50%) translateY(-5px); }
    }
</style>

<main id="main" class="flex-1 about-page">
    
    <?php while (have_posts()) : the_post(); ?>
    
    <!-- Immersive Hero -->
    <section class="about-hero">
        <div class="about-hero-image"></div>
        <div class="about-hero-overlay"></div>
        
        <div class="about-hero-content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <h1 class="about-title"><?php the_title(); ?></h1>
        </div>

        <div class="scroll-indicator">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>

    <!-- Content Card -->
    <section class="about-content-wrapper max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="about-card max-w-4xl">
            <article class="about-body">
                <?php the_content(); ?>
            </article>
            
            <div class="about-divider">
                <span>✦</span>
            </div>
        </div>
    </section>

    <!-- Tagline -->
    <div class="about-tagline max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="about-tagline-text">Go eat something delicious.</p>
    </div>

    <?php endwhile; ?>

</main>

<?php
get_footer();
