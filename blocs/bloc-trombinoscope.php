<?php

$className = 'bloc-trombinoscope';

if (!empty($attributes['className'])) {
    $className .= ' ' . $attributes['className'];
}

// Récupérer le membre_id depuis les attributes
$membre_id = isset($attributes['membre_id']) ? $attributes['membre_id'] : 0;

if(!$membre_id && is_admin()){
    echo '<p>Veuillez sélectionner un membre de l\'équipe municipale à afficher</p>';
    return;
}

// Récupérer le membre
$membre = get_post($membre_id);
if(!$membre) {
    return;
}

$couleurFond = wpc_get_option('trombinoscope_couleur_fond');

?>

<div class="<?php echo esc_attr($className); ?>"<?php if($couleurFond): ?> style="background-color: <?= $couleurFond ?>"<?php endif; ?>>
    <div class="bloc-trombinoscope--photo">
        <?php $photo = get_the_post_thumbnail($membre->ID, 'medium');
        if($photo):
            echo $photo;
        else: ?>
            <span class="dashicons dashicons-businessperson"></span>
        <?php endif; ?>
    </div>
    <div class="bloc-trombinoscope--contenu">
        <h3><?= $membre->post_title; ?></h3>
        <?php
        $fonction = get_field('fonction', $membre->ID);
        if($fonction): ?>
            <p class="bloc-trombinoscope--fonction">
                <strong><?php echo esc_html($fonction); ?></strong>
            </p>
        <?php endif;

        $infos_supp = get_field('informations_supplementaires', $membre->ID);
        if($infos_supp): ?>
            <p class="bloc-trombinoscope--infos">
                <?php echo wp_kses_post($infos_supp); ?>
            </p>
        <?php endif; ?>
    </div>
</div>