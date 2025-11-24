<?php

public function register_block_assets() {
    $plugin_url = plugin_dir_url( __DIR__ );
    $plugin_path = plugin_dir_path( __DIR__ );

    // Scripts éditeur (compilés depuis src/)
    $asset_file = include($plugin_path . 'build/bloc-marche-public.asset.php');
    wp_register_script(
            'wpcollectivites-bloc-marche-public-editor',
            $plugin_url . 'build/bloc-marche-public.js',
            $asset_file['dependencies'],
            $asset_file['version']
    );

    $asset_file = include($plugin_path . 'build/bloc-actes-officiels.asset.php');
    wp_register_script(
            'wpcollectivites-bloc-actes-officiels-editor',
            $plugin_url . 'build/bloc-actes-officiels.js',
            $asset_file['dependencies'],
            $asset_file['version']
    );

    $asset_file = include($plugin_path . 'build/bloc-trombinoscope.asset.php');
    wp_register_script(
            'wpcollectivites-bloc-trombinoscope-editor',
            $plugin_url . 'build/bloc-trombinoscope.js',
            $asset_file['dependencies'],
            $asset_file['version']
    );

    // Script frontend pour le bloc marché public (ne change pas)
    wp_register_script(
            'wpcollectivites-bloc-marche-public-front',
            $plugin_url . 'assets/js/bloc_marche_public.js',
            ['jquery'],
            filemtime($plugin_path . 'assets/js/bloc_marche_public.js'),
            true
    );

    // Styles (ne changent pas)
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
