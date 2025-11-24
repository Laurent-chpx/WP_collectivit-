<?php
/**
 * Bloc Trombinoscope - Affiche les membres de l'équipe
 */

if (!defined('ABSPATH')) {
    exit;
}

$attributes = $attributes ?? [];
$nombre_membres = isset($attributes['nombreMembres']) ? intval($attributes['nombreMembres']) : -1;
$afficher_fonction = isset($attributes['afficherFonction']) ? $attributes['afficherFonction'] : true;
$afficher_telephone = isset($attributes['afficherTelephone']) ? $attributes['afficherTelephone'] : true;
$afficher_email = isset($attributes['afficherEmail']) ? $attributes['afficherEmail'] : true;
$afficher_complement = isset($attributes['afficherComplement']) ? $attributes['afficherComplement'] : false;

// Requête pour récupérer les membres
$args = array(
        'post_type' => 'membre',
        'posts_per_page' => $nombre_membres,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_status' => 'publish'
);

$membres = new WP_Query($args);

if (!$membres->have_posts()) {
    echo '<p class="no-membres">Aucun membre à afficher pour le moment.</p>';
    return;
}
?>

    <div class="bloc-trombinoscope">
        <div class="trombinoscope-grid">
            <?php while($membres->have_posts()): $membres->the_post();

                // Récupération des champs via get_post_meta au lieu de get_field
                $fonction = get_post_meta(get_the_ID(), '_wpc_fonction', true);
                $telephone = get_post_meta(get_the_ID(), '_wpc_telephone', true);
                $email = get_post_meta(get_the_ID(), '_wpc_email', true);
                $complement_info = get_post_meta(get_the_ID(), '_wpc_complement_info', true);
                $photo_id = get_post_meta(get_the_ID(), '_wpc_photo', true);

                // Récupération de la photo
                $photo_url = '';
                if ($photo_id) {
                    $photo_url = wp_get_attachment_image_url($photo_id, 'medium');
                }

                // Photo par défaut si aucune photo
                if (!$photo_url) {
                    $photo_url = plugins_url('assets/img/avatar-default.png', dirname(__FILE__));
                }
                ?>

                <article class="membre-card">
                    <div class="membre-photo">
                        <img src="<?php echo esc_url($photo_url); ?>"
                             alt="<?php echo esc_attr(get_the_title()); ?>">
                    </div>

                    <div class="membre-info">
                        <h3 class="membre-nom"><?php echo esc_html(get_the_title()); ?></h3>

                        <?php if ($afficher_fonction && $fonction): ?>
                            <p class="membre-fonction"><?php echo esc_html($fonction); ?></p>
                        <?php endif; ?>

                        <?php if ($afficher_telephone && $telephone): ?>
                            <p class="membre-telephone">
                                📞 <a href="tel:<?php echo esc_attr(str_replace(' ', '', $telephone)); ?>">
                                    <?php echo esc_html($telephone); ?>
                                </a>
                            </p>
                        <?php endif; ?>

                        <?php if ($afficher_email && $email): ?>
                            <p class="membre-email">
                                ✉️ <a href="mailto:<?php echo esc_attr($email); ?>">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </p>
                        <?php endif; ?>

                        <?php if ($afficher_complement && $complement_info): ?>
                            <div class="membre-complement">
                                <?php echo wp_kses_post($complement_info); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

            <?php endwhile; ?>
        </div>
    </div>

<?php wp_reset_postdata(); ?>