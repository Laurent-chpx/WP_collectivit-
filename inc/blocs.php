<?php
//Catégorie de bloc "WP Collectivités"
function wpcollectivites_categories_blocs($categories, $post){
    $nouveauClassementCategoriesBlocs = [];
    foreach($categories as $categorie){
        if($categorie['slug'] == 'embed'){
            $nouveauClassementCategoriesBlocs[] = [
                'slug'  => 'wp-collectivites',
                'title' => 'WP Collectivités'
            ];
        }
        $nouveauClassementCategoriesBlocs[] = $categorie;
    }
    return $nouveauClassementCategoriesBlocs;
}
add_filter('block_categories_all', 'wpcollectivites_categories_blocs', 10, 2);

//Blocs Gutenberg
function wpcollectivites_blocs() {
    if(function_exists('acf_register_block')){
        $blocs = [
            //Bloc marché public
            'marche-public' => [
                'name'          => 'marche-public',
                'title'         => "Marché public",
                'icon'          => 'megaphone',
                'category'      => 'wp-collectivites',
                'enqueue_script' => plugin_dir_url(__DIR__) . 'assets/js/bloc_marche_public.js',
                'mode' => 'auto',
                'supports'          => [
                    'align' => false
                ]
            ],
            //Bloc actes officiels
            'actes-officiels' => [
                'name' => 'actes-officiels',
                'title' => "Actes officiels",
                'icon' => 'pdf',
                'category' => 'wp-collectivites',
                'mode' => 'auto',
                'supports' => [
                    'align' => false
                ]
            ],
            //Bloc trombinoscope
            'trombinoscope' => [
                'name' => 'trombinoscope',
                'title' => "Trombinoscope",
                'icon' => 'businessperson',
                'category' => 'wp-collectivites',
                'mode' => 'auto',
                'supports' => [
                    'align' => false
                ]
            ]
        ];

        foreach($blocs as $id => $parametres){
            $blocs[$id]['render_template'] = file_exists(get_stylesheet_directory().'/blocs/bloc-'.$id.'.php') ? get_stylesheet_directory().'/blocs/bloc-'.$id.'.php' : __DIR__.'/../blocs/bloc-'.$id.'.php';

            if(file_exists(__DIR__.'/../assets/css/bloc-'.$id.'.css')){
                $blocs[$id]['enqueue_style'] = plugin_dir_url(__DIR__) . 'assets/css/bloc-'.$id.'.css';
            }

            acf_register_block($blocs[$id]);
        }
    }
}
add_action('acf/init', 'wpcollectivites_blocs');
