<?php
function wpcollectivites_requete_infos_plugin(){
    $requete = get_transient('infos_plugin_wp_collectivites');

    if($requete === false) {
        $requete = wp_remote_get(
            'https://www.lacouleurduzebre.com/plugins/wp-collectivites.json',
            [
                'timeout' => 10,
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]
        );

        //En cas d'erreur, on arrête ici
        if (is_wp_error($requete) || wp_remote_retrieve_response_code($requete) !== 200 || empty(wp_remote_retrieve_body($requete))) {
            return false;
        }

        set_transient('infos_plugin_wp_collectivites', $requete, DAY_IN_SECONDS);
    }

    return json_decode(wp_remote_retrieve_body($requete));
}

function wpcollectivites_infos_plugin($res, $action, $args){
    //Ne rien faire si on n'est pas en train d'afficher les infos du plugin ou si on affiche les infos d'un autre plugin
    if($action !== 'plugin_information' || $args->slug !== plugin_basename(dirname(__DIR__))){
        return $res;
    }

    $infos = wpcollectivites_requete_infos_plugin();

    if(!$infos){
        return $res;
    }

    $res = new stdClass();
    $res->name = $infos->name;
    $res->slug = $infos->slug;
    $res->author = $infos->author;
    $res->version = $infos->version;
    $res->download_link = $infos->download_url;
    $res->trunk = $infos->download_url;
    $res->last_updated = $infos->last_updated;
    //Onglets
    $res->sections = [
        'description' => $infos->description,
        'changelog' => $infos->changelog
    ];

    return $res;
}
add_filter('plugins_api', 'wpcollectivites_infos_plugin', 20, 3);

function wpcollectivites_maj_plugin($transient){
    if(empty($transient->checked)){
        return $transient;
    }

    $infos = wpcollectivites_requete_infos_plugin();

    if(!$infos){
        return $transient;
    }

    $infosVersionActuelle = get_plugin_data(__DIR__.'/../wp-collectivites.php');
    if(version_compare($infosVersionActuelle['Version'], $infos->version, '<')){
        $res = new stdClass();
        $res->slug = $infos->slug;
        $res->plugin = $infos->slug.'/'.$infos->slug.'.php';
        $res->new_version = $infos->version;
        $res->package = $infos->download_url;
        $transient->response[$res->plugin] = $res;
    }

    return $transient;
}
add_filter('site_transient_update_plugins', 'wpcollectivites_maj_plugin');

function wpcollectivites_purge_transient_infos_plugins($upgrader, $options){
    if ($options['action'] === 'update' && $options[ 'type' ] === 'plugin'){
        delete_transient('infos_plugin_wp_collectivites');
    }
}
add_action('upgrader_process_complete', 'wpcollectivites_purge_transient_infos_plugins', 10, 2);
