<?php
/*
Plugin Name: wp-clutch
Version: 1.0
Author: wp-clutch
*/

ini_set( 'upload_max_size' , '64M' );
ini_set( 'post_max_size', '64M');

define('UPSELL_TITLE', 'upsell_trigger');
define('UPSELL_VALUE', '[upsell_trigger]');

$dir = dirname(__FILE__);


include_once "$dir/clutch-author-menu.php";
include_once "$dir/clutch-excerpt-field.php";
include_once "$dir/clutch-json-api.php";
include_once "$dir/clutch-posts-types.php";
include_once "$dir/clutch-tinymce-buttons.php";
include_once "$dir/post_parser.class.php";



// Include custom single template file for parent post type
// Thanks to http://www.unfocus.com/2010/08/10/including-page-templates-from-a-wordpress-plugin/

//function locate_plugin_template($template_names, $load = false, $require_once = true )
//{
//    if ( !is_array($template_names) )
//        return '';
//
//    $located = '';
//
//    $this_plugin_dir = WP_PLUGIN_DIR.'/'.str_replace( basename( __FILE__), "", plugin_basename(__FILE__) );
//
//    foreach ( $template_names as $template_name ) {
//        if ( !$template_name )
//            continue;
//        if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
//            $located = STYLESHEETPATH . '/' . $template_name;
//            break;
//        } else if ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
//            $located = TEMPLATEPATH . '/' . $template_name;
//            break;
//        } else if ( file_exists( $this_plugin_dir .  $template_name) ) {
//            $located =  $this_plugin_dir . $template_name;
//            break;
//        }
//    }
//
//    if ( $load && '' != $located )
//        load_template( $located, $require_once );
//
//    return $located;
//}


//add_filter( 'single_template', 'get_custom_single_template' );
//function get_custom_single_template($template)
//{
//    global $wp_query;
//    $object = $wp_query->get_queried_object();
//
//    if ( 'clutch_story' == $object->post_type ) {
//        $templates = array('single-' . $object->post_type . '.php', 'single.php');
//        $template = locate_plugin_template($templates);
//    }
//    // return apply_filters('single_template', $template);
//    return $template;
//}


