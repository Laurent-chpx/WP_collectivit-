<?php

$className = 'bloc-trombinoscope';

if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

$membre = get_field('membre_a_afficher');
if(!$membre && is_admin()){
    echo '<p>Veuillez sélectionner un membre de l\'équipe municipale à afficher</p>';
    return;
}

$couleurFond = get_field('trombinoscope_couleur_fond', 'options');

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
        <?php if(get_field('fonction', $membre->ID)): ?>
            <p class="bloc-trombinoscope--fonction">
                <strong><?php the_field('fonction', $membre->ID); ?></strong>
            </p>
        <?php endif;
        if(get_field('informations_supplementaires', $membre->ID)): ?>
            <p class="bloc-trombinoscope--infos">
                <?php the_field('informations_supplementaires', $membre->ID); ?>
            </p>
        <?php endif; ?>
    </div>
</div>
