<?php 

/*
Plugin Name: Subscriber fingerprint
Description: Create a fingerprint to comply with the current regulations established by the current RGPD.
Version: 1 
Author: Christian Herrero
*/


add_action( 'wp_head', 'suscriber_confirmation_page' );
function suscriber_confirmation_page() {

    // Use parse_url() to get all URL params
    $url_components = parse_url($_SERVER['REQUEST_URI']); 
    
    // Use parse_str to divide the obtained string and convert to an array  
    parse_str($url_components['query'], $params); 

    // Get the params as you need
    if ( $params['mailpoet_page'] === 'subscriptions' && $params['action'] === 'confirm' ) {
        
        // If IP resolve  
        if ($_SERVER['REMOTE_ADDR']) {
            
            // Get user variables by IP from MailPoet table
            global $wpdb;
            $ip = $_SERVER['REMOTE_ADDR'];
            $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            // Get all user data
            $user = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."mailpoet_subscribers WHERE '".$ip."' = confirmed_ip ORDER BY id DESC LIMIT 1 ");
            
            // Write the finger print
            $message = '
                <p>Hola, el usuario <strong>'. $user->email .'</strong> se ha suscrito a la Newsletter</p>
                <p>Datos enviados el <strong>'. $user->confirmed_at .'</strong> desde la dirección <strong>IP '. $ip .'</strong> a través de la URL:<br/> '. $current_url .'</p>';
        
            wp_mail( 
                'your-email@example.com', 
                'New user has suscribed to MailPoet', 
                $message,
                array('Content-Type: text/html; charset=UTF-8')
            );
        }
    } 
}
?>
