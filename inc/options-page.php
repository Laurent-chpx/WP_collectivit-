<?php

class WPCollectivites_Options{
    private $options;
    private $page_slug = "wp-collectivites-options";

    function __construct(){
        add_action('admin_menu', [$this, 'add_options_page']);
        add_action('admin_init', [ $this, 'register_settings' ]);
        add_action('admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ]);
    }

    //Ajout de la page options dans le menu wordpress
    function add_options_page(){
        add_menu_page(
            'WP Collectivites',
            'WP Collectivites',
            'manage_options',
            $this->page_slug,
            [$this, 'render_options_page'],
            'dashicons-admin-multisite',
            75
        );
    }

    //Enregistrement des paramètres
    function register_settings(){
        register_setting(
            'wpcollectivites_options_group', //Groupe d'option
            'wpcollectivites_options', //Nom dans la bdd
            [$this, 'sanitize_options'] //fonction de validation
        );

        //Section message d'alerte
        add_settings_section(
            'wpcollectivites_alerte_section', //ID de la section
            "Message alerte", //Titre
            [$this, 'render_alerte_section_info'], //Callback pour le texte d'intro
            $this->page_slug
        );

        //Section Trombinoscope
        add_settings_section(
            'wpcollectivites_trombino_section',
            "Trombinoscope",
            [$this, 'render_trombino_section_info'],
            $this->page_slug
        );

        $this->add_settings_fields();
    }

    function add_settings_fields(){

        //Champs pour le message d'alerte
        $alerte_fields = [
            [
                'id' => 'message_alerte_activation',
                'title' => "Message d'alerte activé",
                'callback' => 'render_checkbox_field',
                'section' => 'wpcollectivites_alerte_section',
            ],
            [
                'id' => 'message_alerte_zone_affichage',
                'title' => "Zoe d'affichage du message",
                'callback' => 'render_radio_field',
                'section' => 'wpcollectivites_alerte_section',
                'args' => [
                    'options' => [
                        'accueil' => 'Page d\'accueil',
                        'partout' => 'Sur tout le site'
                    ]
                ]
            ],
            [
                'id' => 'message_alerte_type_affichage',
                'title' => "Type d'affichage",
                'callback' => 'render_radio_field',
                'section' => 'wpcollectivites_alerte_section',
                'args' => [
                    'options' => [
                        'popup' => 'Pop-up',
                        'bandeau' => 'Bandeau'
                    ]
                ]
            ],
            [
                'id'=>'message_alerte_debut_periode_daffichage',
                'title' => "Début de la période d'affichage",
                'callback' => 'render_date_field',
                'section' => 'wpcollectivites_alerte_section',
            ],
            [
                'id' => 'message_alerte_fin_periode_daffichage',
                'title' => "Fin de la période d'affichage",
                'callback' => 'render_date_field',
                'section' => 'wpcollectivites_alerte_section',
            ],
            [
                'id' => 'message_alerte_texte',
                'title' => "Texte de message d'alerte",
                'callback' => 'render_textarea_field',
                'section' => 'wpcollectivites_alerte_section',
            ],
            [
                'id' => 'message_alerte_couleur_fond',
                'title' => "Couleur de fond",
                'callback' => 'render_color_field',
                'section' => 'wpcollectivites_alerte_section',
                'args' => ['default' => '#000000'],
            ],
            [
                'id' => 'message_alerte_couleur_texte',
                'title' => "Couleur du texte",
                'callback' => 'render_color_field',
                'section' => 'wpcollectivites_alerte_section',
                'args' => ['default' => '#ffffff'],
            ]
        ];

        //Champs pour le trombinoscope
        $trombine_fields = [
            [
                'id' => 'trombinoscope_couleur_fond',
                'title' => "Couleur de fond",
                'callback' => 'render_color_field',
                'section' => 'wpcollectivites_trombino_section',
                'args' => ['default' => '#ffffff'],
            ]
        ];

        $all_fields = array_merge($alerte_fields, $trombine_fields);
        foreach($all_fields as $field){
            add_settings_field(
                $field['id'],
                $field['title'],
                [$this, $field['callback']],
                $this->page_slug,
                $field['section'],
                array_merge(
                    ['field_id' => $field['id']],
                    isset($field['args']) ? $field['args'] : []
                )
            );
        }
    }

    //Rendu HTML de la page
    public function render_options_page(){
        //Récupération des options sauvegardées
        $this->options = get_option('wpcollectivites_options', []);
        ?>
        <div class="wrap">
            <h1>WP Collectivités</h1>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('wpcollectivites_options_group');
                ?>
                <div class="wpc-tabs">
                    <h2 class="nav-tab-wrapper">
                        <a href="#alerte" class="nav-tab nav-tab-active" data-tab="alerte">Message d'alerte</a>
                        <a href="#trombinoscope" class="nav-tab" data-tab="trombinoscope">Trombinoscope</a>
                    </h2>

                    <div class="tab-content" id="tab-alerte">
                        <table class="form-table">
                            <?php do_settings_fields($this->page_slug, 'wpcollectivites_alerte_section'); ?>
                        </table>
                    </div>

                    <div class="tab-content" id="tab-trombinoscope">
                        <table class="form-table">
                            <?php do_settings_fields($this->page_slug, 'wpcollectivites_trombino_section'); ?>
                        </table>
                    </div>
                </div>

                <?php submit_button('Enregistrer les modifications'); ?>
            </form>
        </div>
        <?php

    }

    //Fonction de rendu pour les types de champs
    public function render_checkbox_field($args){
        $field_id = $args['field_id'];
        $value = isset($this->options[$field_id]) ? $this->options[$field_id] : 0;
        ?>
        <label class="switch">
           <input type="checkbox"
                  id="<?php echo esc_attr($field_id); ?>"
                  name="wpcollectivites_options[<?php echo esc_attr($field_id); ?>]"
                  value="1"
                  <?php checked($value, 1); ?> />
           <span class="slider round"></span>
        </label>
        <?php
    }

    public function render_radio_field($args){
        $field_id = $args['field_id'];
        $options = $args['options'];
        $value = isset($this->options[$field_id]) ? $this->options[$field_id] : key($options);

        foreach ($options as $key => $label) {
            ?>
            <label style="display:block; margin-bottom: 5px">
                <input type="radio"
                       name="wpcollectivites_options[<?php echo esc_attr($field_id); ?>]"
                       value="<?php echo esc_attr($key); ?>"
                       <?php checked($value, $key); ?>
                />
                <?php echo esc_html($label); ?>
            </label>
            <?php
        }
    }

    public function render_date_field($args){
        $field_id = $args['field_id'];
        $value = isset($this->options[$field_id]) ? $this->options[$field_id] : '';
        ?>
        <input type="date"
               id="<?php echo esc_attr($field_id); ?>"
               name="wpcollectivites_options[<?php echo esc_attr($field_id); ?>]"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text" />
        <?php
    }

    public function render_textarea_field($args){
        $field_id = $args['field_id'];
        $value = isset($this->options[$field_id]) ? $this->options[$field_id] : '';
        ?>
        <textarea id="<?php echo esc_attr($field_id); ?>"
                  name="wpcollectivites_options[<?php echo esc_attr($field_id); ?>]"
                  rows="3"
                  cols="50"
                  class="large-text"><?php echo esc_textarea($value)?></textarea>
        <?php
    }

    public function render_color_field($args){
        $field_id = $args['field_id'];
        $default = isset($args['default']) ? $args['default'] : '#000000';
        $value = isset($this->options[$field_id]) ? $this->options[$field_id] : $default;
        ?>
        <input type="color"
               id="<?php echo esc_attr($field_id); ?>"
               name="wpcollectivites_options[<?php echo esc_attr($field_id); ?>]"
               value="<?php echo esc_attr($value); ?>"/>
        <?php
    }

    //Validation et nettoyage des données
    public function sanitize_options($input){
        $sanitized = [];

        if(isset($input['message_alerte_activation'])){
            $sanitized['message_alerte_activation'] = 1;
        }else{
            $sanitized['message_alerte_activation'] = 0;
        }

        if (isset($input['message_alerte_zone_affichage'])) {
            $valid_zones = ['accueil', 'partout'];
            if (in_array($input['message_alerte_zone_affichage'], $valid_zones)) {
                $sanitized['message_alerte_zone_affichage'] = $input['message_alerte_zone_affichage'];
            }
        }

        if (isset($input['message_alerte_type_affichage'])) {
            $valid_types = ['popup', 'bandeau'];
            if (in_array($input['message_alerte_type_affichage'], $valid_types)) {
                $sanitized['message_alerte_type_affichage'] = $input['message_alerte_type_affichage'];
            }
        }

        if (isset($input['message_alerte_debut_periode_daffichage'])) {
            $sanitized['message_alerte_debut_periode_daffichage'] = sanitize_text_field($input['message_alerte_debut_periode_daffichage']);
        }

        if (isset($input['message_alerte_fin_periode_affichage'])) {
            $sanitized['message_alerte_fin_periode_affichage'] = sanitize_text_field($input['message_alerte_fin_periode_affichage']);
        }

        if (isset($input['message_alerte_texte'])) {
            $sanitized['message_alerte_texte'] = wp_kses_post($input['message_alerte_texte']);
        }

        if (isset($input['message_alerte_couleur_fond'])) {
            $sanitized['message_alerte_couleur_fond'] = sanitize_hex_color($input['message_alerte_couleur_fond']);
        }

        if (isset($input['message_alerte_couleur_texte'])) {
            $sanitized['message_alerte_couleur_texte'] = sanitize_hex_color($input['message_alerte_couleur_texte']);
        }

        if (isset($input['trombinoscope_couleur_fond'])) {
            $sanitized['trombinoscope_couleur_fond'] = sanitize_hex_color($input['trombinoscope_couleur_fond']);
        }

        return $sanitized;
    }

    //Style et script pour améliiorer l'interface admin
    public function enqueue_admin_assets($hook) {
        // Seulement sur notre page d'options
        if ($hook !== 'toplevel_page_' . $this->page_slug) {
            return;
        }

        // Ajout du color picker WordPress
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Styles personnalisés pour améliorer l'apparence
        wp_add_inline_style('wp-admin', '
            .wpc-tabs .tab-content { 
                display: none; 
                padding-top: 20px;
            }
            .wpc-tabs .tab-content.active { 
                display: block; 
            }
            
            /* Style pour le switch checkbox comme ACF */
            .switch {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 34px;
            }
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
            }
            .slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .4s;
            }
            input:checked + .slider {
                background-color: #2271b1;
            }
            input:checked + .slider:before {
                transform: translateX(26px);
            }
            .slider.round {
                border-radius: 34px;
            }
            .slider.round:before {
                border-radius: 50%;
            }
        ');

        // Script pour gérer les onglets
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // Afficher le premier onglet au chargement
                $("#tab-alerte").addClass("active");
                
                $(".nav-tab").on("click", function(e) {
                    e.preventDefault();
                    $(".nav-tab").removeClass("nav-tab-active");
                    $(this).addClass("nav-tab-active");
                    
                    var tab = $(this).data("tab");
                    $(".tab-content").removeClass("active");
                    $("#tab-" + tab).addClass("active");
                });
            });
        ');
    }

    // Textes d'introduction pour les sections
    public function render_alerte_section_info() {
        echo '<p>Configurez les messages d\'alerte qui apparaîtront sur votre site.</p>';
    }

    public function render_trombino_section_info() {
        echo '<p>Paramètres d\'affichage du trombinoscope de l\'équipe municipale.</p>';
    }
}

new WPCollectivites_Options();

//Fonction pour remplacer get_field d'ACF
function wpc_get_option($option_name) {
    $options= get_option('wpcollectivites_options', []);
    return isset($options[$option_name]) ? $options[$option_name] : false;
}