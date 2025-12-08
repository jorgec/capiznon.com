<?php
/**
 * Page Template - Bohemian Beach Vibe
 *
 * @package Capiznon_Geo
 */

get_header();

while (have_posts()) : the_post();
?>

<main id="main" class="flex-1 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 min-h-screen">
    
    <!-- Header with Art Nouveau flair -->
    <div class="relative h-64 md:h-80 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-400 via-orange-400 to-rose-400">
            <div class="absolute inset-0 bg-white/10"></div>
            <!-- Decorative circles -->
            <div class="absolute top-10 right-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 left-10 w-48 h-48 bg-yellow-300/20 rounded-full blur-2xl"></div>
        </div>
        
        <!-- Art Nouveau decorative wave -->
        <svg class="absolute bottom-0 left-0 right-0 text-orange-50" viewBox="0 0 1440 120" fill="currentColor">
            <path d="M0,64 C240,96 480,32 720,64 C960,96 1200,32 1440,64 L1440,120 L0,120 Z"/>
        </svg>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-center pb-12">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 drop-shadow-md text-center">
                <?php the_title(); ?>
            </h1>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-lg border border-amber-100 p-8 md:p-12 relative overflow-hidden">
            <!-- Decorative corner -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-100 to-orange-100 rounded-bl-full opacity-50 pointer-events-none"></div>
            
            <div class="prose prose-lg prose-amber max-w-none relative z-10">
                <?php the_content(); ?>
            </div>
        </article>
    </div>

</main>

<?php
endwhile;
get_footer();
?>
