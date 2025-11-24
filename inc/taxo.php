<?php

function wpcollectivites_taxonomies(){
    register_taxonomy( 'type-acte', array(
        0 => 'acte-officiel',
    ), array(
        'labels' => array(
            'name' => 'Types d\'actes',
            'singular_name' => 'Type d\'acte',
            'menu_name' => 'Types d\'actes',
            'all_items' => 'Tous les Types d\'actes',
            'edit_item' => 'Modifier le type d\'acte',
            'view_item' => 'Voir le type d\'acte',
            'update_item' => 'Mettre à jour le type d\'acte',
            'add_new_item' => 'Ajouter un type d\'acte',
            'new_item_name' => 'Nom du nouveau type d\'acte',
            'search_items' => 'Rechercher dans les types d\'actes',
            'popular_items' => 'Types d\'actes populaires',
            'separate_items_with_commas' => 'Séparer les types d\'actes avec une virgule',
            'add_or_remove_items' => 'Ajouter ou retirer des types d\'actes',
            'choose_from_most_used' => 'Choisir parmi les types d\'actes les plus utilisés',
            'not_found' => 'Aucun type d\'actes trouvé',
            'no_terms' => 'Aucun type d\'actes',
            'items_list_navigation' => 'Navigation dans la liste des types d\'actes',
            'items_list' => 'Liste des types d\'actes',
            'back_to_items' => '← Aller à « types d\'actes »',
            'item_link' => 'Lien du type d\'acte',
            'item_link_description' => 'Un lien vers un type d\'acte',
        ),
        'public' => true,
        'publicly_queryable' => true,
        'hierarchical' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'rest_base' => 'type-acte',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'type-acte',
            'with_front' => false,
        ),
    ) );
}
add_action( 'init', 'wpcollectivites_taxonomies');