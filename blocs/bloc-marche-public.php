<?php
/**
 * Bloc Marché public - Affiche les marchés publics
 */

if (!defined('ABSPATH')) {
    exit;
}

$attributes = $attributes ?? [];
$nombre_marches = isset($attributes['nombreMarches']) ? intval($attributes['nombreMarches']) : 5;
$afficher_liste_deroulante = isset($attributes['afficherListeDeroulante']) ? $attributes['afficherListeDeroulante'] : false;

// Requête pour récupérer les marchés publics
$args = array(
        'post_type' => 'marche_public',
        'posts_per_page' => $nombre_marches,
        'orderby' => 'meta_value',
        'meta_key' => '_wpc_date_publication',
        'order' => 'DESC',
        'meta_query' => array(
                'relation' => 'AND',
                array(
                        'key' => '_wpc_date_publication',
                        'compare' => 'EXISTS'
                ),
                array(
                        'key' => '_wpc_date_publication',
                        'value' => date('Y-m-d'),
                        'compare' => '<=',
                        'type' => 'DATE'
                )
        )
);

$marches = new WP_Query($args);

if (!$marches->have_posts()) {
    echo '<p class="no-marches">Aucun marché public disponible pour le moment.</p>';
    return;
}
?>

    <div class="bloc-marches-publics" data-afficher-liste="<?php echo $afficher_liste_deroulante ? 'true' : 'false'; ?>">
        <?php if ($afficher_liste_deroulante): ?>
            <div class="marches-selection">
                <label for="marches-select">Sélectionner un marché :</label>
                <select id="marches-select" class="marches-select">
                    <option value="">-- Choisir un marché --</option>
                    <?php while($marches->have_posts()): $marches->the_post(); ?>
                        <option value="<?php echo get_the_ID(); ?>">
                            <?php echo esc_html(get_the_title()); ?>
                        </option>
                    <?php endwhile; wp_reset_postdata(); ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="marches-liste">
            <?php
            $marches->rewind_posts();
            while($marches->have_posts()):
                $marches->the_post();

                // Récupération des champs via get_post_meta au lieu de get_field
                $date_publication = get_post_meta(get_the_ID(), '_wpc_date_publication', true);
                $date_limite = get_post_meta(get_the_ID(), '_wpc_date_limite', true);
                $objet = get_post_meta(get_the_ID(), '_wpc_objet', true);
                $montant = get_post_meta(get_the_ID(), '_wpc_montant', true);
                $procedure = get_post_meta(get_the_ID(), '_wpc_procedure', true);
                $avis_complet = get_post_meta(get_the_ID(), '_wpc_avis_complet', true);
                $document_id = get_post_meta(get_the_ID(), '_wpc_document', true);

                // Formatage des dates
                $date_pub_formatee = '';
                if ($date_publication) {
                    $date_obj = DateTime::createFromFormat('Y-m-d', $date_publication);
                    if ($date_obj) {
                        $date_pub_formatee = $date_obj->format('d/m/Y');
                    }
                }

                $date_lim_formatee = '';
                if ($date_limite) {
                    $date_obj = DateTime::createFromFormat('Y-m-d', $date_limite);
                    if ($date_obj) {
                        $date_lim_formatee = $date_obj->format('d/m/Y');
                    }
                }

                // Récupération du fichier
                $document_url = '';
                if ($document_id) {
                    $document_url = wp_get_attachment_url($document_id);
                }
                ?>

                <article class="marche-item"
                         data-marche-id="<?php echo get_the_ID(); ?>"
                         style="<?php echo $afficher_liste_deroulante ? 'display: none;' : ''; ?>">

                    <div class="marche-header">
                        <h3 class="marche-titre"><?php echo esc_html(get_the_title()); ?></h3>
                        <?php if ($date_pub_formatee): ?>
                            <span class="marche-date">Publié le <?php echo esc_html($date_pub_formatee); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="marche-contenu">
                        <?php if ($objet): ?>
                            <div class="marche-field">
                                <strong>Objet :</strong>
                                <p><?php echo esc_html($objet); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($montant): ?>
                            <div class="marche-field">
                                <strong>Montant :</strong>
                                <p><?php echo esc_html($montant); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($procedure): ?>
                            <div class="marche-field">
                                <strong>Procédure :</strong>
                                <p><?php echo esc_html($procedure); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($date_lim_formatee): ?>
                            <div class="marche-field">
                                <strong>Date limite :</strong>
                                <p><?php echo esc_html($date_lim_formatee); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($avis_complet): ?>
                            <div class="marche-avis-toggle">
                                <button class="toggle-avis" data-marche="<?php echo get_the_ID(); ?>">
                                    Voir l'avis complet
                                </button>
                                <div class="marche-avis-complet" style="display: none;">
                                    <?php echo wp_kses_post($avis_complet); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($document_url): ?>
                            <div class="marche-document">
                                <a href="<?php echo esc_url($document_url); ?>"
                                   target="_blank"
                                   class="marche-download">
                                    📄 Télécharger le document
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

            <?php endwhile; ?>
        </div>
    </div>

<?php wp_reset_postdata(); ?>