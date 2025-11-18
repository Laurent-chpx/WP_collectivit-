<?php

$className = 'bloc-marche-public';

if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
?>

<div class="<?php echo esc_attr($className); ?>">
    <?php if(get_field('intitule')): ?>
        <h3><?php the_field('intitule') ?></h3>
    <?php endif; ?>

    <?php if(get_field('date_publication')): ?>
        <p><strong>Date de publication : </strong><?php the_field('date_publication') ?></p>
    <?php endif; ?>

    <?php if(get_field('date_cloture')): ?>
        <p><strong>Date de clôture : </strong><?= date('d/m/Y à H:i', get_field('date_cloture')); ?><?php if(get_field('date_cloture') < time()): ?> <span style="color: red;">Marché clôturé</span><?php endif; ?></p>
    <?php endif; ?>

    <?php if(get_field('type_marche')): ?>
        <p><strong>Type de marché : </strong><?php the_field('type_marche') ?></p>
    <?php endif; ?>

    <div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex">
        <div class="wp-block-button">
            <a class="bloc-marche-public--btnLireAvisComplet wp-block-button__link wp-element-button">En savoir plus</a>
        </div>
    </div>

    <div class="bloc-marche-public--avisComplet" style="display: none">
        <?php if(get_field('lien')): ?>
            <p><strong>Lien vers la plateforme : </strong><a href="<?= get_field('lien') ?>" target="_blank"><?= get_field('lien') ?></a></p>
        <?php endif; ?>

        <?php if(get_field('profil_acheteur')): ?>
            <p><strong>Profil acheteur : </strong><?php the_field('profil_acheteur') ?></p>
        <?php endif; ?>

        <?php if(get_field('avis_complet')): ?>
            <?php the_field('avis_complet'); ?>
        <?php endif; ?>
    </div>
</div>
