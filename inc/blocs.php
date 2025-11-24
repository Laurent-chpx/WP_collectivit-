<?php

class WPCollectivites_Blocs {
    public function __construct() {
        add_action('init', [$this, 'register_blocks']);
        add_filter('block_categories_all', [$this, 'add_block_category'], 10, 2);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
    }

    //Fonction pour la catégorie WP Collectivité
    public function add_block_category($categories, $post) {
        $nouveau_classement = [];
        foreach ($categories as $categorie) {
            if($categorie['slug'] == 'embed'){
                $nouveau_classement[] = [
                    'slug' => 'wp-collectivites',
                    'title' => 'WP Collectivités',
                ];
            }
            $nouveau_classement[] = $categorie;
        }
        return $nouveau_classement;
    }

    //Remplacement de acf_register_block
    public function register_blocks() {
        $this->register_block_assets();

        //Bloc marché public
        register_block_type('wpcollectivites/marche-public',[
            'title' => 'Marché public',
            'description'=> "Affiche les informations d'un marché public",
            'category' => 'wp-collectivites',
            'icon' => 'megaphone',
            'keywords' => ['marché', 'public', 'appel', 'offre'],
            'supports' => [
                'align' => false,
                'customClassName' => true
            ],
            'attributes' =>[
                'intitule' => [
                    'type' => 'string',
                    'default' => '',
                ],
                'date_publication' => [
                    'type' => 'string',
                    'default' => '',
                ],
                'date_cloture' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'type_marche' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'lien' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'profil_acheteur' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'avis_complet' => [
                    'type' => 'string',
                    'default' => ''
                ]
            ],
            'render_callback' => [$this, 'render_marche_public_block'],
            'editor_script' => 'wpcollectivites-bloc-marche-public-editor',
            'script' => 'wpcollectivites-bloc-marche-public-front',
            'style' => 'wpcollectivites-bloc-marche-public-style'
        ]);


        //Bloc Actes officiels
        register_block_type('wpcollectivites/actes-officiels', [
            'title' => 'Actes officiels',
            'description' => 'Affiche une liste d\'actes officiels par catégorie',
            'category' => 'wp-collectivites',
            'icon' => 'pdf',
            'keywords' => ['actes', 'officiels', 'documents', 'arrêtés'],
            'supports' => [
                'align' => false,
                'customClassName' => true
            ],
            'attributes' => [
                'type_actes' => [
                    'type' => 'number',
                    'default' => 0
                ]
            ],
            'render_callback' => [$this, 'render_actes_officiels_block'],
            'editor_script' => 'wpcollectivites-bloc-actes-officiels-editor'
        ]);

        // Bloc Trombinoscope
        register_block_type('wpcollectivites/trombinoscope', [
            'title' => 'Trombinoscope',
            'description' => 'Affiche la fiche d\'un membre de l\'équipe municipale',
            'category' => 'wp-collectivites',
            'icon' => 'businessperson',
            'keywords' => ['trombinoscope', 'membre', 'équipe', 'municipale', 'élu'],
            'supports' => [
                'align' => false,
                'customClassName' => true
            ],
            'attributes' => [
                'membre_id' => [
                    'type' => 'number',
                    'default' => 0
                ]
            ],
            'render_callback' => [$this, 'render_trombinoscope_block'],
            'editor_script' => 'wpcollectivites-bloc-trombinoscope-editor',
            'style' => 'wpcollectivites-bloc-trombinoscope-style'
        ]);


    }

    public function register_block_assets() {
        $plugin_url = plugin_dir_url( __DIR__ );
        $plugin_path = plugin_dir_path( __DIR__ );

        // Scripts éditeur
        wp_register_script(
                'wpcollectivites-bloc-marche-public-editor',
                $plugin_url . 'assets/js/bloc-marche-public-editor.js',
                ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components'],
                filemtime($plugin_path . 'assets/js/bloc-marche-public-editor.js'),
                false
        );

        wp_register_script(
                'wpcollectivites-bloc-actes-officiels-editor',
                $plugin_url . 'assets/js/bloc-actes-officiels-editor.js',
                ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-api-fetch', 'wp-i18n'],
                filemtime($plugin_path . 'assets/js/bloc-actes-officiels-editor.js'),
                false
        );

        wp_register_script(
                'wpcollectivites-bloc-trombinoscope-editor',
                $plugin_url . 'assets/js/bloc-trombinoscope-editor.js',
                ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-api-fetch', 'wp-i18n'],
                filemtime($plugin_path . 'assets/js/bloc-trombinoscope-editor.js'),
                false
        );

        // Script frontend pour le bloc marché public
        wp_register_script(
                'wpcollectivites-bloc-marche-public-front',
                $plugin_url . 'assets/js/bloc_marche_public.js',
                ['jquery'],
                filemtime($plugin_path . 'assets/js/bloc_marche_public.js'),
                true
        );

        // Styles
        wp_register_style(
                'wpcollectivites-bloc-marche-public-style',
                $plugin_url . 'assets/css/bloc-marche-public.css',
                [],
                file_exists($plugin_path . 'assets/css/bloc-marche-public.css') ? filemtime($plugin_path . 'assets/css/bloc-marche-public.css') : '1.0.0'
        );

        wp_register_style(
                'wpcollectivites-bloc-trombinoscope-style',
                $plugin_url . 'assets/css/bloc-trombinoscope.css',
                [],
                file_exists($plugin_path . 'assets/css/bloc-trombinoscope.css') ? filemtime($plugin_path . 'assets/css/bloc-trombinoscope.css') : '1.0.0'
        );
    }

    public function enqueue_editor_assets() {
        // Passer des données PHP vers JavaScript pour l'éditeur
        wp_localize_script(
            'wpcollectivites-bloc-actes-officiels-editor',
            'wpcActesOfficiels',
            [
                'types' => $this->get_types_actes_for_js(),
                'nonce' => wp_create_nonce('wp_rest')
            ]
        );

        wp_localize_script(
            'wpcollectivites-bloc-trombinoscope-editor',
            'wpcTrombinoscope',
            [
                'membres' => $this->get_membres_for_js(),
                'couleur_fond' => wpc_get_option('trombinoscope_couleur_fond'),
                'nonce' => wp_create_nonce('wp_rest')
            ]
        );
    }
    //rendu front du bloc marché public
    public function render_marche_public_block($attributes, $content, $block) {
        // Extraction des attributs avec valeurs par défaut
        $intitule = isset($attributes['intitule']) ? $attributes['intitule'] : '';
        $date_publication = isset($attributes['date_publication']) ? $attributes['date_publication'] : '';
        $date_cloture = isset($attributes['date_cloture']) ? $attributes['date_cloture'] : '';
        $type_marche = isset($attributes['type_marche']) ? $attributes['type_marche'] : '';
        $lien = isset($attributes['lien']) ? $attributes['lien'] : '';
        $profil_acheteur = isset($attributes['profil_acheteur']) ? $attributes['profil_acheteur'] : '';
        $avis_complet = isset($attributes['avis_complet']) ? $attributes['avis_complet'] : '';


        $className = 'bloc-marche-public';
        if (!empty($attributes['className'])) {
            $className .= ' ' . $attributes['className'];
        }


        ob_start();
        ?>
        <div class="<?php echo esc_attr($className); ?>">
            <?php if ($intitule): ?>
                <h3><?php echo esc_html($intitule); ?></h3>
            <?php endif; ?>

            <?php if ($date_publication): ?>
                <p><strong>Date de publication : </strong><?php echo esc_html($date_publication); ?></p>
            <?php endif; ?>

            <?php if ($date_cloture): ?>
                <?php
                $timestamp_cloture = strtotime($date_cloture);
                $date_formatee = date('d/m/Y à H:i', $timestamp_cloture);
                ?>
                <p>
                    <strong>Date de clôture : </strong><?php echo esc_html($date_formatee); ?>
                    <?php if ($timestamp_cloture < time()): ?>
                        <span style="color: red;">Marché clôturé</span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <?php if ($type_marche): ?>
                <p><strong>Type de marché : </strong><?php echo esc_html($type_marche); ?></p>
            <?php endif; ?>

            <div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex">
                <div class="wp-block-button">
                    <a class="bloc-marche-public--btnLireAvisComplet wp-block-button__link wp-element-button">En savoir plus</a>
                </div>
            </div>

            <div class="bloc-marche-public--avisComplet" style="display: none">
                <?php if ($lien): ?>
                    <p>
                        <strong>Lien vers la plateforme : </strong>
                        <a href="<?php echo esc_url($lien); ?>" target="_blank"><?php echo esc_html($lien); ?></a>
                    </p>
                <?php endif; ?>

                <?php if ($profil_acheteur): ?>
                    <p><strong>Profil acheteur : </strong><?php echo esc_html($profil_acheteur); ?></p>
                <?php endif; ?>

                <?php if ($avis_complet): ?>
                    <?php echo wp_kses_post($avis_complet); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    //rendu front block Actes officiels
    public function render_actes_officiels_block($attributes, $content, $block) {
        $className = 'bloc-actes-officiels';
        if (!empty($attributes['className'])) {
            $className .= ' ' . $attributes['className'];
        }

        // Récupération du type d'acte sélectionné
        $id_type_acte = isset($attributes['type_actes']) ? intval($attributes['type_actes']) : 0;

        if (!$id_type_acte) {
            if (is_admin()) {
                return '<p>Veuillez sélectionner un type d\'acte</p>';
            }
            return '';
        }

        // Récupération de la catégorie
        $term = get_term($id_type_acte, 'type-acte');
        if (!$term) {
            return '';
        }
        $nom_categorie = $term->name;

        // Requête pour récupérer les actes
        $args = [
            'post_type' => 'acte-officiel',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                'date_publication' => [
                    'key' => '_wpc_date_publication', // Notez le préfixe _wpc
                    'compare' => 'EXISTS'
                ],
                'date_fin_publication' => [
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
                    'terms' => $id_type_acte,
                ]
            ],
            'orderby' => [
                'date_publication' => 'DESC'
            ]
        ];

        $query = new WP_Query($args);
        $annees = [];

        foreach ($query->posts as $acte) {
            $date_publication = wpc_get_acte_date_publication($acte->ID);
            $date_obj = DateTime::createFromFormat('d/m/Y', $date_publication);

            if ($date_obj) {
                $annee_publication = $date_obj->format('Y');

                if (!isset($annees[$annee_publication])) {
                    $annees[$annee_publication] = [];
                }

                $annees[$annee_publication][] = [
                    'id' => $acte->ID,
                    'titre' => $acte->post_title,
                    'date_publication' => $date_publication,
                    'publie_par' => wpc_get_acte_publie_par($acte->ID),
                    'fichier' => wpc_get_acte_fichier($acte->ID),
                ];
            }
        }
        krsort($annees);
        ob_start();
        ?>
        <div class="<?php echo esc_attr($className); ?>" data-categorie-id="<?php echo esc_attr($id_type_acte); ?>">
            <?php foreach ($annees as $annee => $actes): ?>
                <div class="actes-annee-section" data-annee="<?php echo esc_attr($annee); ?>">
                    <h3 class="actes-annee-title"><?php echo esc_html($nom_categorie . ' ' . $annee); ?></h3>
                    <table class="actes-annee-table">
                        <tbody class="actes-tbody" data-annee="<?php echo esc_attr($annee); ?>">
                        <?php foreach ($actes as $acte): ?>
                            <tr class="actes-annee-row">
                                <td class="actes-annee-column">
                                    <a class="actes-annees-titre"
                                       href="<?php echo $acte['fichier'] ? esc_url($acte['fichier']['url']) : '#'; ?>"
                                       target="_blank"><?php echo esc_html($acte['titre']); ?>,</a>

                                    <?php if ($acte['publie_par']): ?>
                                        <span class="actes-annee-auteur">
                                            publié par <?php echo esc_html($acte['publie_par']->post_title); ?>,
                                            <?php echo esc_html(wpc_get_membre_fonction($acte['publie_par']->ID)); ?>,
                                        </span>
                                    <?php endif; ?>

                                    <span class="actes-annee-date"> le <?php echo esc_html($acte['date_publication']); ?>.</span>
                                </td>
                                <td class="actes-annee-column">
                                    <?php if ($acte['fichier']): ?>
                                        <a href="<?php echo esc_url($acte['fichier']['url']); ?>" target="_blank">
                                            <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'assets/img/pdf.svg'); ?>
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
        <?php

        wp_reset_postdata();
        return ob_get_clean();
    }

    //rendu front bloc  trombinoscope
    public function render_trombinoscope_block($attributes, $content, $block) {
        $className = 'bloc-trombinoscope';
        if (!empty($attributes['className'])) {
            $className .= ' ' . $attributes['className'];
        }
        $membre_id = isset($attributes['membre_id']) ? intval($attributes['membre_id']) : 0;

        if (!$membre_id) {
            if (is_admin()) {
                return '<p>Veuillez sélectionner un membre de l\'équipe municipale à afficher</p>';
            }
            return '';
        }

        $membre = get_post($membre_id);
        if (!$membre || $membre->post_type !== 'membre-equipe-muni') {
            return '';
        }

        $couleur_fond = wpc_get_option('trombinoscope_couleur_fond');

        ob_start();
        ?>
        <div class="<?php echo esc_attr($className); ?>"<?php if ($couleur_fond): ?> style="background-color: <?php echo esc_attr($couleur_fond); ?>"<?php endif; ?>>
            <div class="bloc-trombinoscope--photo">
                <?php
                $photo = get_the_post_thumbnail($membre->ID, 'medium');
                if ($photo):
                    echo $photo;
                else:
                    ?>
                    <span class="dashicons dashicons-businessperson"></span>
                <?php endif; ?>
            </div>
            <div class="bloc-trombinoscope--contenu">
                <h3><?php echo esc_html($membre->post_title); ?></h3>

                <?php
                $fonction = wpc_get_membre_fonction($membre->ID);
                if ($fonction):
                    ?>
                    <p class="bloc-trombinoscope--fonction">
                        <strong><?php echo esc_html($fonction); ?></strong>
                    </p>
                <?php
                endif;

                $infos_supp = wpc_get_membre_infos_supp($membre->ID);
                if ($infos_supp):
                    ?>
                    <p class="bloc-trombinoscope--infos">
                        <?php echo wp_kses_post($infos_supp); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    private function get_types_actes_for_js() {
        $terms = get_terms([
            'taxonomy' => 'type-acte',
            'hide_empty' => false
        ]);

        $types = [];
        foreach ($terms as $term) {
            $types[] = [
                'value' => $term->term_id,
                'label' => $term->name
            ];
        }

        return $types;
    }

    private function get_membres_for_js() {
        $membres_posts = get_posts([
            'post_type' => 'membre-equipe-muni',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ]);

        $membres = [];
        foreach ($membres_posts as $membre) {
            $membres[] = [
                'value' => $membre->ID,
                'label' => $membre->post_title
            ];
        }

        return $membres;
    }
}

new WPCollectivites_Blocs();


