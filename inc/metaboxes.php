<?php

class WPCollectivite_Metaboxes {
    public function __construct() {
        add_action( 'add_meta_boxes', [$this, 'add_metaboxes' ]);
        add_action( 'save_post', [$this, 'save_metabox_data']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_metabox_assets']);
    }

    public function add_metaboxes() {
        //Metabox pour les membres de l'équipe municipale
        add_meta_box(
            'wpc_membre_equipe_infos',
            'Informations du membre',
            [$this, 'render_membre_equipe_metabox'],
            'membre-equipe-muni',
            'normal',
            'high'
        );

        //Metabox pour les actes officiels
        add_meta_box(
            'wpc_acte_officiel_infos',
            'Informations de l\'acte officiel',
            [$this, 'render_acte_officiel_metabox'],
            'acte-officiel',
            'normal',
            'high'
        );
    }

    //Rendu de la metabox equipe (remplace le group acf)
    public function render_membre_equipe_metabox($post) {
        wp_nonce_field('wpc_save_membre_equipe', 'wpc_membre_equipe_nonce');

        //Récupération des valeurs existante
        $fonction = get_post_meta($post->ID, '_wpc_fonction', true);
        $infos_supp = get_post_meta($post->ID, '_wpc_informations_supplementaires', true);
        ?>
        <div class="wpc-metabox-container">
            <div class="wpc-field-group">
                <label for="wpc_fonction">
                    <strong>Fonction</strong>
                </label>
                <input type="text"
                       name="wpc_fonction"
                       id="wpc_fonction"
                       value="<?php echo $fonction; ?>"
                       class="widefat"
                       placeholder="Ex: Maire, Adjoint au maire, Conseiller municipal..."
                />
            </div>
            <div class="wpc-field-group" style="margin-top: 20px">
                <label for="wpc_informations_supplementaires">
                    <strong>Informations supplémentaires</strong>
                </label>
                <textarea id="wpc_informations_supplementaires"
                          name="wpc_informations_supplementaires"
                          rows="5"
                          class="widefat"
                          ><?php echo esc_textarea($infos_supp); ?></textarea>
            </div>
        </div>
    <?php
    }

    public function render_acte_officiel_metabox($post) {
        wp_nonce_field('wpc_save_acte_officiel', 'wpc_acte_officiel_nonce');

        //récupération des valeurs
        $date_publication = get_post_meta($post->ID, '_wpc_date_publication', true);
        $date_fin_publication = get_post_meta($post->ID, '_wpc_date_fin_publication', true);
        $publie_par = get_post_meta($post->ID, '_wpc_publie_par', true);
        $fichier_id = get_post_meta($post->ID, '_wpc_fichier_id', true);
        ?>
        <div class="wpc-metabox-container">
            <div class="wpc-field-group">
                <label for="wpc_date_publication">
                    <strong>Date de publication</strong>
                </label>
                <input type="date"
                       id="wpc_date_publication"
                       name="wpc_date_publication"
                       value="<?php echo $date_publication; ?>"
                       class="regular-text" />
            </div>
            <div class="wpc-field-group" style="margin-top: 20px">
                <label for="wpc_date_fin_publication">
                    <strong>Date de fin publication</strong>
                </label>
                <input type="date"
                       id="wpc_date_fin_publication"
                       name="wpc_date_fin_publication"
                       value="<?php echo $date_fin_publication; ?>"
                       class="regular-text" />
                <p class="description">Laisser vide si l'acte doit rester publié indéfiniment</p>
            </div>

            <div class="wpc-field-group" style="margin-top: 20px">
                <label for="wpc_publie_par">
                    <strong>Publié par</strong>
                </label>
                <select id="wpc_publie_par" name="wpc_publie_par" class="widefat">
                    <option value="">-- Sélectionner un membre --</option>
                    <?php
                    $membres = get_posts([
                        'post_type' => 'membre-equipe-muni',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC',
                    ]);

                    foreach ($membres as $membre) {
                        $selected = ($publie_par == $membre->ID) ? 'selected=selected' : '';
                        echo sprintf(
                            '<option value="%s" %s>%s</option>',
                            $membre->ID,
                            $selected,
                            esc_html($membre->post_title)
                        );
                    }
                    ?>
                </select>
                <p class="description">Membre de l'équipe municipale ayant publié cet acte</p>
            </div>

            <div class="wpc-field-group" style="margin-top: 20px">
                <label for="wpc_fichier">
                    <strong>Fichier PDF</strong>
                </label>
                <div class="wpc-file-upload">
                    <?php if($fichier_id) :
                        $fichier_url = wp_get_attachment_url($fichier_id);
                        $fichier_title = get_the_title($fichier_id);
                        ?>
                    <div class="wpc-file-preview">
                        <span class="dashicons dashicons-pdf" style="color: #CC4B4C; font-size: 30px;"></span>
                        <span class="filename"><?php echo esc_html($fichier_title); ?></span>
                        <a href="<?php echo esc_url($fichier_url); ?>" target="_blank" class="button">Voir</a>
                        <button type="button" class="button wpc-remove-file">Supprimer</button>
                    </div>
                    <?php endif; ?>

                    <div class="wpc-file-selector" <?php echo $fichier_id ? 'style="display: none"' : '' ?>>
                        <button type="button" class="button wpc-upload-file">
                            <span class="dashicons dashicons-upload"></span> Sélectionner un fichier
                        </button>
                    </div>
                    <input type="hidden" id="wpc_fichier_id" value="<?php echo $fichier_id; ?>"
                </div>
            </div>
        </div>
        <?php
    }

    public function save_metabox_data($post_id) {
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        $post_type = get_post_type($post_id);

        if($post_type === 'membre-equipe-muni') {
            if(!isset($_POST['wpc_acte_officiel_nonce']) ||!wp_verify_nonce($_POST['wpc_membre_equipe_nonce'], 'wpc_save_membre_equipe')){
                return;
            }

            if(isset($_POST['wpc_fonction'])){
                update_post_meta($post_id, '_wpc_fonction', sanitize_text_field($_POST['wpc_fonction']));
            }

            if(isset($_POST['wpc_informations_supplementaires'])){
                update_post_meta($post_id, '_wpc_informations_supplementaires', wp_kses_post($_POST['wpc_informations_supplementaires']));
            }
        }
        elseif ($post_type === 'acte-officiel') {
            if (!isset($_POST['wpc_acte_officiel_nonce']) ||
                !wp_verify_nonce($_POST['wpc_acte_officiel_nonce'], 'wpc_save_acte_officiel')) {
                return;
            }

            if (isset($_POST['wpc_date_publication'])) {
                update_post_meta($post_id, '_wpc_date_publication', sanitize_text_field($_POST['wpc_date_publication']));
            }

            if (isset($_POST['wpc_date_fin_publication'])) {
                update_post_meta($post_id, '_wpc_date_fin_publication', sanitize_text_field($_POST['wpc_date_fin_publication']));
            }

            if (isset($_POST['wpc_publie_par'])) {
                update_post_meta($post_id, '_wpc_publie_par', absint($_POST['wpc_publie_par']));
            }

            if (isset($_POST['wpc_fichier_id'])) {
                update_post_meta($post_id, '_wpc_fichier_id', absint($_POST['wpc_fichier_id']));
            }
        }
    }

    //script et styles pour les metaboxes
    public function enqueue_metabox_assets($hook) {
        //uniquement sur les pages cpt
        $screen = get_current_screen();
        if(!in_array($screen->post_type, ['membre_equipe-uni', 'acte-officiel'])) {
            return;
        }
        if($screen->post_type === 'acte-officiel') {
            wp_enqueue_media();

            //Js pour l'upload de fichier
            wp_add_inline_script('jquery', '
                jQuery(document).ready(function($) {
                    var mediaUploader;
                    
                    // Gestion du bouton upload
                    $(document).on("click", ".wpc-upload-file", function(e) {
                        e.preventDefault();
                        
                        // Si le media uploader existe déjà, on l\'ouvre
                        if (mediaUploader) {
                            mediaUploader.open();
                            return;
                        }
                        
                        // Création du media uploader
                        mediaUploader = wp.media({
                            title: "Sélectionner un fichier PDF",
                            button: {
                                text: "Utiliser ce fichier"
                            },
                            library: {
                                type: ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"]
                            },
                            multiple: false
                        });
                        
                        // Quand un fichier est sélectionné
                        mediaUploader.on("select", function() {
                            var attachment = mediaUploader.state().get("selection").first().toJSON();
                            $("#wpc_fichier_id").val(attachment.id);
                            
                            // Mise à jour de l\'affichage
                            $(".wpc-file-selector").hide();
                            
                            // Si preview existe, on le met à jour, sinon on le crée
                            var preview = $(".wpc-file-preview");
                            if (preview.length === 0) {
                                preview = $(\'<div class="wpc-file-preview"></div>\');
                                $(".wpc-file-upload").prepend(preview);
                            }
                            
                            preview.html(
                                \'<span class="dashicons dashicons-pdf" style="color: #CC4B4C; font-size: 30px;"></span>\' +
                                \'<span class="filename">\' + attachment.title + \'</span>\' +
                                \'<a href="\' + attachment.url + \'" target="_blank" class="button">Voir</a>\' +
                                \'<button type="button" class="button wpc-remove-file">Supprimer</button>\'
                            );
                        });
                        
                        mediaUploader.open();
                    });
                    
                    // Gestion du bouton supprimer
                    $(document).on("click", ".wpc-remove-file", function(e) {
                        e.preventDefault();
                        $("#wpc_fichier_id").val("");
                        $(".wpc-file-preview").remove();
                        $(".wpc-file-selector").show();
                    });
                });
            ');
        }
        wp_add_inline_style('wp-admin', '
            .wpc-metabox-container {
                padding: 10px 0;
            }
            .wpc-field-group {
                margin-bottom: 15px;
            }
            .wpc-field-group label {
                display: block;
                margin-bottom: 5px;
            }
            .wpc-field-group .description {
                color: #666;
                font-style: italic;
                margin-top: 5px;
            }
            .wpc-file-preview {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px;
                background: #f5f5f5;
                border: 1px solid #ddd;
                margin-bottom: 10px;
            }
            .wpc-file-preview .filename {
                flex-grow: 1;
            }
        ');
    }
}

new WPCollectivite_Metaboxes();

//Fonction pour récupérer les données (remplace get_field() acf)
function wpc_get_membre_fonction($post_id = null){
    if(!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_wpc_fonction', true);
}

function wpc_get_membre_infos_supp($post_id = null){
    if(!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_wpc_informations_supplementaires', true);
}

function wpc_get_acte_date_publication($post_id = null){
    if(!$post_id) {
        $post_id = get_the_ID();
    }
    $date = get_post_meta($post_id, '_wpc_date_publication', true);
    if(!$date) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $date);
        return $date_obj ? $date_obj->format('Y-m-d') : $date;
    }
    return '';
}

function wpc_get_acte_date_fin_publication($post_id = null){
    if(!$post_id) {
        $post_id = get_the_ID();
    }
    $date = get_post_meta($post_id, '_wpc_date_fin_publication', true);
    if(!$date) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $date);
        return $date_obj ? $date_obj->format('Y-m-d') : $date;
    }
    return '';
}

function wpc_get_acte_publie_par($post_id = null){
    if(!$post_id) {
        $post_id = get_the_ID();
    }
    $membre_id = get_post_meta($post_id, '_wpc_publie_par', true);
    return $membre_id ? get_post($membre_id) : null;
}

function wpc_get_acte_fichier($post_id = null){
    if(!$post_id) {
        $post_id = get_the_ID();
    }
    $fichier_id = get_post_meta($post_id, '_wpc_fichier_id', true);
    if(!$fichier_id) {
        return [
            'id' => $fichier_id,
            'url' => wp_get_attachment_url($fichier_id),
            'title'=> get_the_title($fichier_id)
        ];
    }
    return null;
}