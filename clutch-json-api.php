<?php


function json_api_import_clutch_episode_wp_post($this_obj, $wp_post) {

    if ($wp_post->post_type === "clutch_episode")
    {
        $this_obj->set_value('post_content', strip_tags($wp_post->post_content));
        $this_obj->set_value('post_excerpt', strip_tags($wp_post->post_excerpt));

        $parser = new post_parser($wp_post->post_content);
        $parser->parse();
        $parser->resultMessages;
        $this_obj->set_value('messages', $parser->resultMessages);
        $this_obj->set_value('parser_warnings', $parser->warnings);
    }
}

add_action( 'json_api_import_wp_post', 'json_api_import_clutch_episode_wp_post', 10, 2 );