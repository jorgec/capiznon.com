<?php
/**
 * Footer Template
 * Bohemian Beach Vibe with Art Nouveau touches
 *
 * @package Capiznon_Geo
 */
?>

    <!-- Footer -->
    <footer id="colophon" class="bg-gradient-to-b from-amber-800 to-orange-900 text-white mt-auto relative overflow-hidden">
        
        <!-- Art Nouveau Decorative Wave -->
        <div class="absolute top-0 left-0 right-0 h-16 -translate-y-full">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="w-full h-full">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C57.1,118.92,156.63,69.08,321.39,56.44Z" class="fill-amber-800"></path>
            </svg>
        </div>
        
        <!-- Bohemian decorative elements -->
        <div class="absolute top-20 left-10 w-32 h-32 border-2 border-white/5 rounded-full"></div>
        <div class="absolute bottom-20 right-10 w-24 h-24 border-2 border-white/5 rotate-45"></div>
        <div class="absolute top-1/2 left-1/4 w-4 h-4 bg-rose-400/20 rounded-full"></div>
        <div class="absolute top-1/3 right-1/3 w-3 h-3 bg-amber-400/20 rotate-45"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                
                <!-- Brand -->
                <div class="md:col-span-2">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3 mb-6 group">
                        <!-- Bohemian Beach Logo -->
                        <div class="relative w-12 h-12">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-400 to-orange-400 rounded-xl rotate-3 group-hover:rotate-6 transition-transform"></div>
                            <div class="absolute inset-0.5 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform">
                                <span class="text-2xl text-white">🏝️</span>
                            </div>
                        </div>
                        <span class="text-2xl font-bold"><?php bloginfo('name'); ?></span>
                    </a>
                    <p class="text-amber-200 max-w-md leading-relaxed mb-6">
                        <?php echo esc_html(get_bloginfo('description')); ?>
                    </p>
                    
                    <!-- Social Links -->
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-amber-600 rounded-xl flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-gradient-to-br hover:from-purple-500 hover:to-pink-500 rounded-xl flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-6 flex items-center gap-2">
                        <span class="w-8 h-1 bg-gradient-to-r from-amber-400 to-orange-400 rounded-full"></span>
                        <?php esc_html_e('Discover', 'capiznon-geo'); ?>
                    </h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'space-y-3',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                        'link_before'    => '<span class="text-amber-200 hover:text-white hover:pl-2 transition-all block">',
                        'link_after'     => '</span>',
                    ]);
                    ?>
                </div>

                <!-- Contact / Widget Area -->
                <div>
                    <h4 class="text-lg font-bold mb-6 flex items-center gap-2">
                        <span class="w-8 h-1 bg-gradient-to-r from-amber-400 to-rose-400 rounded-full"></span>
                        <?php esc_html_e('Connect', 'capiznon-geo'); ?>
                    </h4>
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <?php dynamic_sidebar('footer-1'); ?>
                    <?php else : ?>
                        <div class="space-y-4 text-amber-200">
                            <p class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">🏝️</span>
                                Roxas City, Capiz, Philippines
                            </p>
                            <p class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">📧</span>
                                hello@capiznongeo.com
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/10 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-amber-200">
                    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. 
                    <?php esc_html_e('Made with', 'capiznon-geo'); ?> 
                    <span class="text-rose-400">♥</span> 
                    <?php esc_html_e('in Capiz', 'capiznon-geo'); ?>
                </p>
                <p class="text-sm text-amber-200 flex items-center gap-2">
                    <?php esc_html_e('Powered by', 'capiznon-geo'); ?> 
                    <a href="https://wordpress.org" class="hover:text-white transition-colors" target="_blank" rel="noopener">WordPress</a>
                    <span class="w-1 h-1 bg-amber-200 rounded-full"></span>
                    <a href="https://leafletjs.com" class="hover:text-white transition-colors" target="_blank" rel="noopener">Leaflet</a>
                </p>
            </div>
        </div>
    </footer>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
