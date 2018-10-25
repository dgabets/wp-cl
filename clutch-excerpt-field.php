<?php


function clutch_mandatory_fields($data) {
//change your_post_type to post, page, or your custom post type slug

    if ( 'clutch_episode' == $data['post_type'] ) {

        $content = $data['post_content'];

        if (empty($content)) { // If excerpt field is empty

            // Check if the data is not drafed and trashed
            if ( ( $data['post_status'] != 'draft' ) && ( $data['post_status'] != 'trash' ) ){

                $data['post_status'] = 'draft';

                add_filter('redirect_post_location', 'clutch_content_error_message_redirect', '99');

            }
        }
//        var_dump('<html>' . $content . '</html>');
//        $dom = new DOMDocument;
//        $dom->loadHTML($content);
//        $messages = $dom->getElementsByTagName('p');
//        var_dump($messages);

        $excerpt = $data['post_excerpt'];

        if (empty($excerpt)) { // If excerpt field is empty

            // Check if the data is not drafed and trashed
            if ( ( $data['post_status'] != 'draft' ) && ( $data['post_status'] != 'trash' ) ){

                $data['post_status'] = 'draft';

                add_filter('redirect_post_location', 'excerpt_error_message_redirect', '99');

            }
        }
    }

    return $data;
}

add_filter('wp_insert_post_data', 'clutch_mandatory_fields');

function excerpt_error_message_redirect($location) {

    $location = str_replace('&message=6', '', $location);

    return add_query_arg('excerpt_required', 1, $location);

}

function clutch_content_error_message_redirect($location) {

    $location = str_replace('&message=6', '', $location);

    return add_query_arg('content_required', 1, $location);
}

function excerpt_admin_notice() {

    if (isset($_GET['content_required'])) {

        $message = 'Content field is empty!';

    } else if (isset($_GET['excerpt_required'])) {

        $message = 'Excerpt field is empty!';

    } else {

        return;

    }

    echo '<div id="notice" class="error"><p>' . $message . '</p></div>';

}

add_action('admin_notices', 'excerpt_admin_notice');


/**
 * Removes the regular excerpt box. We're not getting rid
 * of it, we're just moving it above the wysiwyg editor
 *
 * @return null
 */
function oz_remove_normal_excerpt() {
    remove_meta_box( 'postexcerpt' , 'clutch_episode' , 'normal' );
}
add_action( 'admin_menu' , 'oz_remove_normal_excerpt' );

/**
 * Add the excerpt meta box back in with a custom screen location
 *
 * @param  string $post_type
 * @return null
 */
function oz_add_excerpt_meta_box( $post_type ) {
    if ( in_array( $post_type, array( 'clutch_episode' ) ) ) {
        add_meta_box(
            'oz_postexcerpt',
            __( 'Excerpt', 'thetab-theme' ),
            'post_excerpt_meta_box',
            $post_type,
            'after_title',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'oz_add_excerpt_meta_box' );


/**
 * You can't actually add meta boxes after the title by default in WP so
 * we're being cheeky. We've registered our own meta box position
 * `after_title` onto which we've registered our new meta boxes and
 * are now calling them in the `edit_form_after_title` hook which is run
 * after the post tile box is displayed.
 *
 * @return null
 */
function oz_run_after_title_meta_boxes() {
    global $post, $wp_meta_boxes;
    # Output the `below_title` meta boxes:
    do_meta_boxes( get_current_screen(), 'after_title', $post );
}
add_action( 'edit_form_after_title', 'oz_run_after_title_meta_boxes' );


// rename excerpt label
function wpartisan_excerpt_label( $translation, $original ) {

    if ( false !== strpos( $original, 'Excerpts are optional hand-crafted summaries of your' ) ) {
        return __( 'Enter a summary of this episode that will excite readers' );
    }
    return $translation;
}
add_filter( 'gettext', 'wpartisan_excerpt_label', 10, 2 );
