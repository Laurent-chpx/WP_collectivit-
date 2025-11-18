<?php

$className = 'bloc-actes-officiels';

if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

//Catégorie sélectionnée dans le bloc
$idTypeActe = get_field('type_actes');
if(!$idTypeActe){
    if(is_admin()){
        echo '<p>Veuillez sélectionner un type d\'acte</p>';
    }
    return;
}else{
    $term = get_term($idTypeActe, 'type-acte');
    if(!$term){
        return;
    }
    $nom_categorie = $term->name;
}

$args = array(
    'post_type' => 'acte-officiel',
    'posts_per_page' => -1,
    'meta_query' => [
        'relation' => 'AND',
        'date_publication' => [
            'key' => 'date_publication',
            'compare' => 'EXISTS'
        ],
        'date_fin_publication' => [
            'relation' => 'OR',
            [
                'key' => 'date_fin_publication',
                'compare' => '>=',
                'value' => date('Ymd')
            ],
            [
                'key' => 'date_fin_publication',
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
    'orderby' => [
        'date_publication' => 'DESC'
    ]
);

$query = new WP_Query($args);

$annees = [];

foreach($query->get_posts() as $acte){
    $date_publication = get_field('date_publication', $acte->ID);
    $annee_publication = date_create_from_format('d/m/Y', $date_publication)->format('Y');

    if(!isset($annees[$annee_publication])){
        $annees[$annee_publication] = [];
    }

    $annees[$annee_publication][] = [
        'id' => $acte->ID,
        'titre' => $acte->post_title,
        'date_publication' => $date_publication,
        'publie_par' => get_field('publie_par', $acte->ID),
        'fichier' => get_field('fichier', $acte->ID),
    ];
}

//Trier les années ordre décroissant
krsort($annees);

?>

<div class="<?php echo esc_attr($className); ?>" data-categorie-id="<?php echo esc_attr($idTypeActe); ?>" >
    <?php $i = 0; foreach ($annees as $annee => $actes): ?>
        <div class="actes-annee-section" data-annee="<?php echo esc_attr($annee); ?>">
            <h3 class="actes-annee-title"><?php echo $nom_categorie .' '. $annee ?>  </h3>
            <table class="actes-annee-table">
                <tbody class="actes-tbody" data-annee="<?php echo esc_attr($annee); ?>">
                <?php foreach($actes as $acte): ?>
                    <tr class="actes-annee-row">
                        <td class="actes-annee-column">
                            <a class="actes-annees-titre" href="<?= $acte['fichier'] ? esc_url($acte['fichier']['url']) : '#'; ?>" target="_blank" ><?php echo esc_html($acte['titre']) ?>,</a>
                            <?php if($acte['publie_par']): ?>
                                <span class="actes-annee-auteur">publié par <?= $acte['publie_par']->post_title ?>, <?php the_field('fonction', $acte['publie_par']->ID); ?>,</span>
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
