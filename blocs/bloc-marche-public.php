<?php

$className = 'bloc-marche-public';

if (!empty($attributes['className'])) {
    $className .= ' ' . $attributes['className'];
}

// Récupérer les valeurs depuis les attributes
$intitule = isset($attributes['intitule']) ? $attributes['intitule'] : '';
$date_publication = isset($attributes['date_publication']) ? $attributes['date_publication'] : '';
$date_cloture = isset($attributes['date_cloture']) ? $attributes['date_cloture'] : '';
$type_marche = isset($attributes['type_marche']) ? $attributes['type_marche'] : '';
$lien = isset($attributes['lien']) ? $attributes['lien'] : '';
$profil_acheteur = isset($attributes['profil_acheteur']) ? $attributes['profil_acheteur'] : '';
$avis_complet = isset($attributes['avis_complet']) ? $attributes['avis_complet'] : '';

?>

<div class="<?php echo esc_attr($className); ?>">
    <?php if($intitule): ?>
        <h3><?php echo wp_kses_post($intitule); ?></h3>
    <?php endif; ?>

    <?php if($date_publication): ?>
        <p><strong>Date de publication : </strong><?php echo esc_html($date_publication); ?></p>
    <?php endif; ?>

    <?php if($date_cloture): ?>
        <p>
            <strong>Date de clôture : </strong>
            <?php
            $date = new DateTime($date_cloture);
            echo esc_html($date->format('d/m/Y à H:i'));
            ?>
            <?php if($date < new DateTime()): ?>
                <span style="color: red;">Marché clôturé</span>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <?php if($type_marche): ?>
        <p><strong>Type de marché : </strong><?php echo esc_html($type_marche); ?></p>
    <?php endif; ?>

    <div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex">
        <div class="wp-block-button">
            <a class="bloc-marche-public--btnLireAvisComplet wp-block-button__link wp-element-button">En savoir plus</a>
        </div>
    </div>

    <div class="bloc-marche-public--avisComplet" style="display: none">
        <?php if($lien): ?>
            <p><strong>Lien vers la plateforme : </strong><a href="<?= esc_url($lien); ?>" target="_blank"><?= esc_url($lien); ?></a></p>
        <?php endif; ?>

        <?php if($profil_acheteur): ?>
            <p><strong>Profil acheteur : </strong><?php echo esc_html($profil_acheteur); ?></p>
        <?php endif; ?>

        <?php if($avis_complet): ?>
            <?php echo wp_kses_post($avis_complet); ?>
        <?php endif; ?>
    </div>
</div>