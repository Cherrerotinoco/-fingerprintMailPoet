<?php 

/*
Plugin Name: Huella Digital de suscriptores
Description: Crea una huella digital para cumplir la vigente normativa establecida por el actual RGPD.
Version: 1 
Author: Christian Herrero
*/


add_action( 'wp_head', 'suscriber_confirmation_page' );
function suscriber_confirmation_page() {

    // Usamos parse_url() para obetener todos los parámetros de la URL
    $url_components = parse_url($_SERVER['REQUEST_URI']); 
    
    // Mediante parse_str() dividimos el string obtenido y guardamos en un array
    parse_str($url_components['query'], $params); 

    // Utilizamos los parámetros que necesitemos
    if ( $params['mailpoet_page'] === 'subscriptions' && $params['action'] === 'confirm' ) {
        // Si existe la IP de respuesta
        if ($_SERVER['REMOTE_ADDR']) {
            // Procedemos a recoger la info de la DB
            global $wpdb;

            $ip = $_SERVER['REMOTE_ADDR'];
            
            $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $user = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."mailpoet_subscribers WHERE '".$ip."' = confirmed_ip ORDER BY id DESC LIMIT 1 ");

            $message = '
                <p>Hola, el usuario <strong>'. $user->email .'</strong> se ha suscrito a la Newsletter</p>
                <p>Datos enviados el <strong>'. $user->confirmed_at .'</strong> desde la dirección <strong>IP '. $ip .'</strong> a través de la URL:<br/> '. $current_url .'</p>';
        
            wp_mail( 
                'tu-email@example.com', 
                'Un nuevo usuario se ha suscrito a la Newsletter', 
                $message,
                array('Content-Type: text/html; charset=UTF-8')
            );
        }
    } 
}
?>
