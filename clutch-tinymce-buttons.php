<?php


add_filter( 'mce_buttons', 'jivedig_remove_tiny_mce_buttons_from_editor');
function jivedig_remove_tiny_mce_buttons_from_editor( $buttons ) {
    $remove_buttons = array(
        'bold',
        'italic',
        'formatselect',
        'strikethrough',
        'bullist',
        'numlist',
        'blockquote',
        'hr', // horizontal line
        'alignleft',
        'aligncenter',
        'alignright',
        'link',
        'unlink',
        'wp_more', // read more link
        'spellchecker',
        'dfw', // distraction free writing mode
        'wp_adv', // kitchen sink toggle (if removed, kitchen sink will always display)
        'paragraph'
    );
    foreach ( $buttons as $button_key => $button_value ) {
        if ( in_array( $button_value, $remove_buttons ) ) {
            unset( $buttons[ $button_key ] );
        }
    }
    return $buttons;
}

add_filter( 'mce_buttons_2', 'jivedig_remove_tiny_mce_buttons_from_kitchen_sink');
function jivedig_remove_tiny_mce_buttons_from_kitchen_sink( $buttons ) {
    $remove_buttons = array(
        'formatselect', // format dropdown menu for <p>, headings, etc
        'underline',
        'alignjustify',
        'strikethrough',
        'forecolor', // text color
        'pastetext', // paste as text
        'removeformat', // clear formatting
        'charmap', // special characters
        'outdent',
        'indent',
        'undo',
        'redo',
        'hr',
        'wp_help', // keyboard shortcuts
    );
    foreach ( $buttons as $button_key => $button_value ) {
        if ( in_array( $button_value, $remove_buttons ) ) {
            unset( $buttons[ $button_key ] );
        }
    }
    return $buttons;
}



add_action( 'after_setup_theme', 'mytheme_theme_setup' );

if ( ! function_exists( 'mytheme_theme_setup' ) ) {
    function mytheme_theme_setup() {

        add_action( 'init', 'mytheme_buttons' );

    }
}


/********* TinyMCE Buttons ***********/
if ( ! function_exists( 'mytheme_buttons' ) ) {
    function mytheme_buttons() {
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }

        if ( get_user_option( 'rich_editing' ) !== 'true' ) {
            return;
        }

        add_filter( 'mce_external_plugins', 'mytheme_add_buttons' );
        add_filter( 'mce_buttons', 'mytheme_register_buttons' );
    }
}

if ( ! function_exists( 'mytheme_add_buttons' ) ) {
    function mytheme_add_buttons( $plugin_array ) {
        $plugin_array['clutch_video_button'] =  plugin_dir_url(__FILE__) . 'clutch-tinymce-video-button.js';
        $plugin_array['clutch_upsell_button'] =  plugin_dir_url(__FILE__) . 'clutch-tinymce-upsell-buttons.js';
        $plugin_array['clutch_img_upsell_button'] =  plugin_dir_url(__FILE__) . 'clutch-tinymce-img-upsell-buttons.js';
        return $plugin_array;
    }
}

if ( ! function_exists( 'mytheme_register_buttons' ) ) {
    function mytheme_register_buttons( $buttons ) {
        array_push( $buttons, 'clutch_video_button' );
        array_push( $buttons, 'clutch_upsell_button' );
        array_push( $buttons, 'clutch_img_upsell_button' );
        return $buttons;
    }
}

add_action ( 'after_wp_tiny_mce', 'mytheme_tinymce_extra_vars' );

if ( !function_exists( 'mytheme_tinymce_extra_vars' ) ) {
    function mytheme_tinymce_extra_vars() { ?>
        <script type="text/javascript">
          var tinyMCE_object = <?php echo json_encode(
                  array(
                      'button_name' => esc_html__('Add video', 'mythemeslug'),
                      'button_title' => esc_html__('Select or Upload Video', 'mythemeslug'),
                      'image_title' => esc_html__('Image', 'mythemeslug'),
                      'image_button_title' => esc_html__('Upload image', 'mythemeslug'),
                      'UPSELL_VALUE' => UPSELL_VALUE,
                      'UPSELL_TITLE' => UPSELL_TITLE,
                  )
              );
              ?>;
        </script><?php
    }
}


add_filter( 'wp_video_extensions',
    function( $exts ) {
        $exts[] = 'mov';
        return $exts;
    }
);