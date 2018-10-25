<?php

/*
Copyright (c) 2012 Sandy Mcfadden
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

Disclaimer:
	Use at your own risk. No warranty expressed or implied is provided.
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 	See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


Requires : Wordpress 3.x or newer ,PHP 5 +
*/

// Setup the parent custom post type
function post_type_clutch_stories() {

//    var_dump(current_user_can( 'author' ));
//    die();

    $labels   = array(
        'name' => __('Stories'),
        'singular_name' => __('Story'),
        'add_new_item' => __('Add New Story'),
        'edit_item' => __('Edit Story'));
    $supports = array('title','editor','thumbnail');
    $args     = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'has_archive' => true,
        'hierarchical' => true,
        'supports' => $supports,
        'taxonomies' => array( 'category' ),
        'rewrite' => array( 'slug' => 'stories' ));

    register_post_type('clutch_story', $args);
}

add_action('init', 'post_type_clutch_stories');


// Thanks to http://janina.tumblr.com/post/3588081423/post-parent-different-type for this

add_action('admin_menu', function() { remove_meta_box('pageparentdiv', 'clutch_episode', 'normal');});
add_action('add_meta_boxes', function() {
    add_meta_box('clutch_episode-parent', 'Story', 'clutch_episode_attributes_meta_box', 'clutch_episode', 'side', 'high');});

function clutch_episode_attributes_meta_box($post) {
    $post_type_object = get_post_type_object($post->post_type);
    if ( $post_type_object->hierarchical ) {
        $parent = $post->post_parent;
        if ($parent == 0 && isset($_GET['clutch_story']))
            $parent = $_GET['clutch_story'];
        $pages = wp_dropdown_pages(array(
            'post_type' => 'clutch_story',
            'selected' => $parent,
            'name' => 'parent_id',
            'show_option_none' => __('(Select One)'),
            'sort_column'=> 'menu_order, post_title',
            'echo' => 0));
        if ( ! empty($pages) ) {
            echo $pages;
        }
    }
}

// Setup the children custom post type
function post_type_clutch_episode() {
    $labels   = array(
        'name' => __('Episodes'),
        'singular_name' => __('Episode'),
        'add_new_item' => __('Add New Episode'),
        'edit_item' => __('Edit Episode'),
        'parent_item_colon' => __('Story'));
    $supports = array('title','editor', 'excerpt');
    $args     = array(
        'labels' => $labels,
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'supports' => $supports,
        'rewrite' => array( 'slug' => 'episode' ));

    register_post_type('clutch_episode', $args);
    //UPDATE wp_posts SET comment_status = 'open' WHERE post_type = 'clutch_episode';
}

add_action('init', 'post_type_clutch_episode');


// Remove the children menu item as it will be managed under the parent item.
function remove_clutch_episode_menu() {
    remove_menu_page('edit.php?post_type=clutch_episode');
}
add_action('admin_menu', 'remove_clutch_episode_menu');



// Add meta box to display children items in parent
add_action("admin_init", "add_clutch_stories_meta_boxes");

function add_clutch_stories_meta_boxes(){
    add_meta_box("clutch_episode-meta", "Episode", "clutch_episode_meta", "clutch_story", "normal", "high");
}


function clutch_episode_meta() {
    global $post;
    if (get_post_status($post->ID) == 'publish')
        echo '<p><a href="post-new.php?post_type=clutch_episode&clutch_story='. $post->ID .'">&raquo; Add New Episode</a>'."\n";
    $my_wp_query = new WP_Query();
    $all_wp_children = $my_wp_query->query(array('post_type' => 'clutch_episode', 'post_parent' => $post->ID, "posts_per_page" => '-1'));
    $children = get_page_children($post->ID, $all_wp_children);
    if (count($children)) {
        echo '<br/><br/>';
        echo '<p>Current Episodes:</p>';
        echo '<div style="margin-left: 10px">';
        echo '<ul>'."\n";
        foreach ($children as $child)
            echo '<li><a href="post.php?post='. $child->ID .'&action=edit">'. $child->post_title .'</a> (' . $child->post_status . ')</li>'."\n";
        echo '</ul>'."\n";
        echo '</div>';
    }
}

// Delete all children when the parent is deleted
add_action('delete_post', 'delete_clutch_episode_when_stories_deleted');
function delete_clutch_episode_when_stories_deleted($post_id) {
    $post = get_post($post_id);
    if ($post->post_type == 'clutch_episode') {
        $my_wp_query = new WP_Query();
        $all_wp_children = $my_wp_query->query(array('post_type' => 'clutch_episode'));
        $children = get_page_children($post->ID, $all_wp_children);
        foreach($children as $child) {
            wp_delete_post($child->ID);
        }
    }
}

