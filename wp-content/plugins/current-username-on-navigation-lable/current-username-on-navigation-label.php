<?php
/*
Plugin Name: Current Username on Navigation Label
Plugin URI: http://realblackz.com/plugins/current-username-on-nav-label
Description: This plugin allows you to display current username [Display Name] on wordpress menu navigation label or title attribute. Just use [current-username] shortcode. You can also use other shortcode on your navigation label and tittle attributes. 
Version: 1.0.0
Author: Al-Mamun Talukder
Author URI: http://itsmereal.com
*/

add_filter('wp_nav_menu', 'do_menu_shortcodes'); 
function do_menu_shortcodes( $menu ){ 
        return do_shortcode( $menu ); 
} 

add_shortcode( 'current-username' , 'username_on_menu' );
function username_on_menu(){
    $user = wp_get_current_user();
    return $user->display_name;
}