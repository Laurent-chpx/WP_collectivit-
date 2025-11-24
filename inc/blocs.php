<?php

class WPCollectivites_Blocs {
    public function __construct() {
        add_action('init', [$this, 'register_block_category']);
        add_action('init', [$this, 'register_blocks']);
        add_action('enqueue_block_assets', [$this, 'register_block_assets']);
    }

    public function register_block_category() {
        if (!function_exists('register_block_type')) {
            return;
        }

        add_filter('block_categories_all', function($categories) {
            return array_merge(
                    $categories,
                    [
                            [
                                    'slug'  => 'wp-collectivites',
                                    'title' => 'WP Collectivités',
                            ],
                    ]
            );
        });
    }

    public function register_blocks() {
        // Marché public
        register_block_type(__DIR__ . '/../blocs/bloc-marche-public.php', [
                'editor_script' => 'wpcollectivites-bloc-marche-public-editor',
                'script' => 'wpcollectivites-bloc-marche-public-front',
                'style' => 'wpcollectivites-bloc-marche-public-style',
                'render_callback' => function($attributes, $content, $block) {
                    ob_start();
                    include __DIR__ . '/../blocs/bloc-marche-public.php';
                    return ob_get_clean();
                }
        ]);

        // Actes officiels
        register_block_type(__DIR__ . '/../blocs/bloc-actes-officiels.php', [
                'editor_script' => 'wpcollectivites-bloc-actes-officiels-editor',
                'render_callback' => function($attributes, $content, $block) {
                    ob_start();
                    include __DIR__ . '/../blocs/bloc-actes-officiels.php';
                    return ob_get_clean();
                }
        ]);

        // Trombinoscope
        register_block_type(__DIR__ . '/../blocs/bloc-trombinoscope.php', [
                'editor_script' => 'wpcollectivites-bloc-trombinoscope-editor',
                'style' => 'wpcollectivites-bloc-trombinoscope-style',
                'render_callback' => function($attributes, $content, $block) {
                    ob_start();
                    // Passer la couleur de fond au JS
                    wp_localize_script(
                            'wpcollectivites-bloc-trombinoscope-editor',
                            'wpcTrombinoscope',
                            ['couleur_fond' => wpc_get_option('trombinoscope_couleur_fond') ?: '#ffffff']
                    );
                    include __DIR__ . '/../blocs/bloc-trombinoscope.php';
                    return ob_get_clean();
                }
        ]);
    }

    public function register_block_assets() {
        $plugin_url = plugin_dir_url(__DIR__);
        $plugin_path = plugin_dir_path(__DIR__);

        // Scripts éditeur (compilés depuis src/)
        if (file_exists($plugin_path . 'build/bloc-marche-public.asset.php')) {
            $asset_file = include($plugin_path . 'build/bloc-marche-public.asset.php');
            wp_register_script(
                    'wpcollectivites-bloc-marche-public-editor',
                    $plugin_url . 'build/bloc-marche-public.js',
                    $asset_file['dependencies'],
                    $asset_file['version']
            );
        }

        if (file_exists($plugin_path . 'build/bloc-actes-officiels.asset.php')) {
            $asset_file = include($plugin_path . 'build/bloc-actes-officiels.asset.php');
            wp_register_script(
                    'wpcollectivites-bloc-actes-officiels-editor',
                    $plugin_url . 'build/bloc-actes-officiels.js',
                    $asset_file['dependencies'],
                    $asset_file['version']
            );
        }

        if (file_exists($plugin_path . 'build/bloc-trombinoscope.asset.php')) {
            $asset_file = include($plugin_path . 'build/bloc-trombinoscope.asset.php');
            wp_register_script(
                    'wpcollectivites-bloc-trombinoscope-editor',
                    $plugin_url . 'build/bloc-trombinoscope.js',
                    $asset_file['dependencies'],
                    $asset_file['version']
            );
        }

        // Script frontend pour le bloc marché public
        wp_register_script(
                'wpcollectivites-bloc-marche-public-front',
                $plugin_url . 'assets/js/bloc_marche_public.js',
                ['jquery'],
                filemtime($plugin_path . 'assets/js/bloc_marche_public.js'),
                true
        );

        // Styles
        wp_register_style(
                'wpcollectivites-bloc-marche-public-style',
                $plugin_url . 'assets/css/bloc-marche-public.css',
                [],
                filemtime($plugin_path . 'assets/css/bloc-marche-public.css')
        );

        wp_register_style(
                'wpcollectivites-bloc-trombinoscope-style',
                $plugin_url . 'assets/css/bloc-trombinoscope.css',
                [],
                filemtime($plugin_path . 'assets/css/bloc-trombinoscope.css')
        );
    }
}

new WPCollectivites_Blocs();