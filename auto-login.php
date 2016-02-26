<?php
/*
Plugin Name: WP Auto Login
Plugin URI: http://www.famethemes.com/
Description:  Auto login as administrator without password.
Author: shrimp2t
Author URI:  http://www.famethemes.com/
Version: 1.0.0
Text Domain: auto-login
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


function ft_auto_login(){

    $action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : false;
    if ( $action == 'logout' ) {
        wp_logout();
        ob_start();
        ob_clean();
        ob_start();
        wp_redirect( home_url('/') );
        die();
    }

    $page_now = isset ( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : false;
    if ( ! is_user_logged_in() && ( is_admin() || $page_now == 'wp-login.php' )) {
        $args = array(
            'role'    => 'administrator',
            'orderby' => 'registered',
            'order'   => 'desc',
        );
        $users = get_users( $args );
        if ( count( $users ) ) {
            $user = $users[0]->data;
            wp_set_current_user( $user->ID, $user->user_login );
            wp_set_auth_cookie($user->ID, true );
            do_action( 'wp_login', $user->user_login );

            if ( $page_now == 'wp-login.php' ) {
                $redirect_to = admin_url();
                if (isset ($_REQUEST['redirect_to']) && $_REQUEST['redirect_to'] != '') {
                    $redirect_to = $_REQUEST['redirect_to'];
                }
            } else {
                $redirect_to = $_SERVER['REQUEST_URI'];
            }

            ob_start();
            ob_clean();
            ob_start();
            wp_redirect( $redirect_to );
            die();
        }
    }
}
add_action( 'init', 'ft_auto_login' );

function ft_logout_url(){
    $args = array(
        'action'=>'logout'
    );
    $logout_url = add_query_arg( $args, home_url('/') );
    return $logout_url;
}
 add_filter( 'logout_url', 'ft_logout_url', 99 );
