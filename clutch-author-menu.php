<?php


function remove_author_menu() {

    if ( current_user_can( 'author' ) ) {
        remove_menu_page('edit.php'); // Posts
        remove_menu_page('edit-comments.php'); // Media
    }
}
add_action('admin_init', 'remove_author_menu');