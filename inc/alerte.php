<?php
class WPCollectivites_Alerte {

    private $activation;
    private $zoneAffichage;
    private $typeAffichage;
    private $debutAffichage;
    private $finAffichage;
    private $texte;
    private $couleurFond;
    private $couleurTexte;

    public function __construct() {

        $options = get_option('wpcollectivites_options', []); // CORRECTION: était 'wpcollectivite_options'

        $this->activation = isset($options['message_alerte_activation']) ? $options['message_alerte_activation'] : false;
        $this->zoneAffichage = isset($options['message_alerte_zone_affichage']) ? $options['message_alerte_zone_affichage'] : 'partout';
        $this->typeAffichage = isset($options['message_alerte_type_affichage']) ? $options['message_alerte_type_affichage'] : 'popup';
        $this->debutAffichage = isset($options['message_alerte_debut_periode_daffichage']) ? $options['message_alerte_debut_periode_daffichage'] : '';
        $this->finAffichage = isset($options['message_alerte_fin_periode_affichage']) ? $options['message_alerte_fin_periode_affichage'] : ''; // CORRECTION: était 'message_alerte_fin_periode_daffichage'
        $this->texte = isset($options['message_alerte_texte']) ? $options['message_alerte_texte'] : '';
        $this->couleurFond = isset($options['message_alerte_couleur_fond']) ? $options['message_alerte_couleur_fond'] : '#000000';
        $this->couleurTexte = isset($options['message_alerte_couleur_texte']) ? $options['message_alerte_couleur_texte'] : '#FFFFFF';

        add_action('wp_footer', [$this, 'affichage']);
    }

    public function affichage(){
        $ajd = (new DateTime())->format('Y-m-d');
        
        // Conversion des dates au format Y-m-d si nécessaire
        $date_debut = '';
        if ($this->debutAffichage) {
            $date_obj = DateTime::createFromFormat('Y-m-d', $this->debutAffichage);
            if ($date_obj) {
                $date_debut = $date_obj->format('Y-m-d');
            }
        }
        
        $date_fin = '';
        if ($this->finAffichage) {
            $date_obj = DateTime::createFromFormat('Y-m-d', $this->finAffichage);
            if ($date_obj) {
                $date_fin = $date_obj->format('Y-m-d');
            }
        }
        
        $date_debut_ok = !$date_debut || $date_debut <= $ajd;
        $date_fin_ok = !$date_fin || $date_fin >= $ajd;

        if(
            $this->activation &&
            $date_debut_ok &&
            $date_fin_ok &&
            ($this->zoneAffichage == 'partout' || is_front_page())
        ){
            //JS
            wp_enqueue_script('wpcollectivites-jscookie', plugin_dir_url(__DIR__).'assets/js/jscookie.js', [], '3.0.5', true);
            wp_enqueue_script('wpcollectivites-message-alerte-js', plugin_dir_url(__DIR__).'assets/js/message_alerte.js', ['jquery', 'wpcollectivites-jscookie'], '1.0.0', true);
            //CSS
            wp_enqueue_style('wpcollectivites-message-alerte-css', plugin_dir_url(__DIR__).'assets/css/message_alerte.css');

            if($this->typeAffichage == 'popup'){
                $this->popup();
            }else{
                $this->bandeau();
            }
        }
    }

    public function bandeau(){
        ?>
            <div id="wpcollectivites-bandeau" style="background-color: <?= $this->couleurFond; ?>; color: <?= $this->couleurTexte ?>">
                <p class="wpcollectivites-bandeau--texte">
                    <?= $this->texte; ?>
                </p>
            </div>
        <?php
    }

    public function popup(){
        ?>
            <div id="wpcollectivites-popup">
                <div class="wpcollectivites-popup--conteneur" style="background-color: <?= $this->couleurFond; ?>; color: <?= $this->couleurTexte ?>">
                    <button class="wpcollectivites-popup--fermeture"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M504.6 148.5C515.9 134.9 514.1 114.7 500.5 103.4C486.9 92.1 466.7 93.9 455.4 107.5L320 270L184.6 107.5C173.3 93.9 153.1 92.1 139.5 103.4C125.9 114.7 124.1 134.9 135.4 148.5L278.3 320L135.4 491.5C124.1 505.1 125.9 525.3 139.5 536.6C153.1 547.9 173.3 546.1 184.6 532.5L320 370L455.4 532.5C466.7 546.1 486.9 547.9 500.5 536.6C514.1 525.3 515.9 505.1 504.6 491.5L361.7 320L504.6 148.5z" fill="<?= $this->couleurTexte ?>"/></svg></button>
                    <p class="wpcollectivites-popup--texte">
                        <?= $this->texte; ?>
                    </p>
                </div>
            </div>
        <?php
    }
}

new WPCollectivites_Alerte();