<?php
/**
 * Header Template
 *
 * @package Capiznon_Geo
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <!-- PWA manifest & theme color -->
    <link rel="manifest" href="<?php echo esc_url( get_template_directory_uri() . '/manifest.webmanifest' ); ?>">
    <meta name="theme-color" content="#f59e0b">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Display:wght@300;400;500;600;700&family=Sigmar+One&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class('antialiased bg-base-white text-base-black'); ?>>
<?php wp_body_open(); ?>

<div id="page" class="min-h-screen flex flex-col">
    
    <!-- Skip Link -->
    <a class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded-xl z-50" href="#main">
        <?php esc_html_e('Skip to content', 'capiznon-geo'); ?>
    </a>

    <!-- Header -->
    <header id="masthead" class="bg-gradient-to-r from-amber-50 to-orange-50/95 backdrop-blur-lg border-b border-amber-200 sticky top-0 z-40">
        <!-- Art Nouveau decorative top bar -->
        <div class="h-1 bg-gradient-to-r from-amber-400 via-orange-400 to-rose-400"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo / Site Title -->
                <div class="flex items-center">
                    <?php if (has_custom_logo()) : ?>
                        <div class="flex-shrink-0">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3 group">
                            <!-- Bohemian Beach Logo -->
                            <div class="relative w-11 h-11">
                                <div class="absolute inset-0 bg-gradient-to-br from-amber-400 to-orange-400 rounded-xl rotate-3 group-hover:rotate-6 transition-transform"></div>
                                <div class="absolute inset-0.5 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform">
                                    <span class="text-2xl text-white">🏝️</span>
                                </div>
                                <!-- Decorative element -->
                                <div class="absolute -top-1 -right-1 w-3 h-3 bg-rose-400 rounded-full border-2 border-white"></div>
                            </div>
                            <div>
                                <span class="text-xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent"><?php bloginfo('name'); ?></span>
                                <?php 
                                $description = get_bloginfo('description', 'display');
                                if ($description) : ?>
                                    <span class="hidden sm:block text-xs text-amber-700 font-medium"><?php echo $description; ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Navigation -->
                <nav id="site-navigation" class="hidden md:flex items-center gap-1">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'menu_class'     => 'flex items-center gap-1',
                        'fallback_cb'    => false,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'link_before'    => '<span class="px-4 py-2 text-sm font-semibold text-amber-800 hover:text-amber-600 hover:bg-amber-100 rounded-xl transition-all">',
                        'link_after'     => '</span>',
                    ]);
                    ?>
                    
                    <!-- CTA Button -->
                    <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>" class="ml-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-bold rounded-xl hover:shadow-lg hover:shadow-amber-500/30 transition-all">
                        🌊 <?php esc_html_e('Explore', 'capiznon-geo'); ?>
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-toggle" class="md:hidden p-2.5 rounded-xl text-amber-800 hover:text-amber-600 hover:bg-amber-100 transition-all">
                    <span class="sr-only"><?php esc_html_e('Open menu', 'capiznon-geo'); ?></span>
                    <span class="text-2xl">🏝️</span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-amber-200 bg-gradient-to-b from-amber-50 to-orange-50">
            <div class="px-4 py-4 space-y-1">
                <?php
                wp_nav_menu([
                    'theme_location' => 'mobile',
                    'menu_id'        => 'mobile-menu-nav',
                    'container'      => false,
                    'menu_class'     => 'space-y-1',
                    'fallback_cb'    => function() {
                        wp_nav_menu([
                            'theme_location' => 'primary',
                            'container'      => false,
                            'menu_class'     => 'space-y-1',
                        ]);
                    },
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'link_before'    => '<span class="block px-4 py-3 text-base font-semibold text-amber-800 hover:text-amber-600 hover:bg-amber-100 rounded-xl transition-all">',
                    'link_after'     => '</span>',
                ]);
                ?>
                
                <!-- Mobile CTA -->
                <a href="<?php echo esc_url(get_post_type_archive_link('cg_location')); ?>" class="block mt-3 px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-center font-bold rounded-xl">
                    🌊 <?php esc_html_e('Explore Paradise', 'capiznon-geo'); ?>
                </a>
            </div>
        </div>
    </header>

    <script>
        document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
            document.getElementById('mobile-menu')?.classList.toggle('hidden');
        });
    </script>
