<?php

$className = 'bloc-actes-officiels';

if (!empty($attributes['className'])) {
    $className .= ' ' . $attributes['className'];
}

// Récupérer le type_actes depuis les attributes
$idTypeActe = isset($attributes['type_actes']) ? $attributes['type_actes'] : 0;

if(!$idTypeActe){
    if(is_admin()){
        echo '<p>Veuillez sélectionner un type d\'acte</p>';
    }
    return;
}else{
    $term = get_term($idTypeActe, 'type-acte');
    if(!$term || is_wp_error($term)){
        return;
    }
    $nom_categorie = $term->name;
}

$args = array(
        'post_type' => 'acte-officiel',
        'posts_per_page' => -1,
        'meta_query' => [
                'relation' => 'AND',
                '_wpc_date_publication' => [
                        'key' => '_wpc_date_publication',
                        'compare' => 'EXISTS'
                ],
                '_wpc_date_fin_publication' => [
                        'relation' => 'OR',
                        [
                                'key' => '_wpc_date_fin_publication',
                                'compare' => '>=',
                                'value' => date('Y-m-d')
                        ],
                        [
                                'key' => '_wpc_date_fin_publication',
                                'value' => '',
                        ]
                ]
        ],
        'tax_query' => [
                [
                        'taxonomy' => 'type-acte',
                        'field' => 'term_id',
                        'terms' => $idTypeActe,
                ]
        ],
        'orderby' => 'meta_value',
        'meta_key' => '_wpc_date_publication',
        'order' => 'DESC'
);

$query = new WP_Query($args);

$annees = [];

foreach($query->get_posts() as $acte){
    // Récupérer depuis les metaboxes custom (format Y-m-d)
    $date_publication = get_post_meta($acte->ID, '_wpc_date_publication', true);

    if($date_publication) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $date_publication);
        if($date_obj) {
            $annee_publication = $date_obj->format('Y');
            $date_affichage = $date_obj->format('d/m/Y'); // Convertir pour l'affichage

            if(!isset($annees[$annee_publication])){
                $annees[$annee_publication] = [];
            }

            // Récupérer le membre qui a publié
            $publie_par_id = get_post_meta($acte->ID, '_wpc_publie_par', true);
            $publie_par = $publie_par_id ? get_post($publie_par_id) : null;

            // Récupérer le fichier
            $fichier_id = get_post_meta($acte->ID, '_wpc_fichier_id', true);
            $fichier = null;
            if($fichier_id) {
                $fichier = [
                        'url' => wp_get_attachment_url($fichier_id),
                        'title' => get_the_title($fichier_id)
                ];
            }

            $annees[$annee_publication][] = [
                    'id' => $acte->ID,
                    'titre' => $acte->post_title,
                    'date_publication' => $date_affichage,
                    'publie_par' => $publie_par,
                    'fichier' => $fichier,
            ];
        }
    }
}

// Trier les années ordre décroissant
krsort($annees);

?>

    <div class="<?php echo esc_attr($className); ?>" data-categorie-id="<?php echo esc_attr($idTypeActe); ?>" >
        <?php foreach ($annees as $annee => $actes): ?>
            <div class="actes-annee-section" data-annee="<?php echo esc_attr($annee); ?>">
                <h3 class="actes-annee-title"><?php echo esc_html($nom_categorie) .' '. esc_html($annee); ?></h3>
                <table class="actes-annee-table">
                    <tbody class="actes-tbody" data-annee="<?php echo esc_attr($annee); ?>">
                    <?php foreach($actes as $acte): ?>
                        <tr class="actes-annee-row">
                            <td class="actes-annee-column">
                                <a class="actes-annees-titre" href="<?= $acte['fichier'] ? esc_url($acte['fichier']['url']) : '#'; ?>" target="_blank" ><?php echo esc_html($acte['titre']) ?>,</a>
                                <?php if($acte['publie_par']): ?>
                                    <?php
                                    $fonction_membre = get_post_meta($acte['publie_par']->ID, '_wpc_fonction', true);
                                    ?>
                                    <span class="actes-annee-auteur">publié par <?= esc_html($acte['publie_par']->post_title); ?><?php if($fonction_membre): ?>, <?= esc_html($fonction_membre); ?><?php endif; ?>,</span>
                                <?php endif; ?>
                                <span class="actes-annee-date"> le <?php echo esc_html($acte['date_publication']); ?>.</span>
                            </td>
                            <td class="actes-annee-column">
                                <?php if($acte['fichier']): ?>
                                    <a href="<?php echo esc_url($acte['fichier']['url']); ?>" target="_blank">
                                        <?= file_get_contents(plugin_dir_path(__DIR__).'assets/img/pdf.svg'); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

<?php wp_reset_postdata(); ?>