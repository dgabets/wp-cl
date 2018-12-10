<?php

Class post_parser
{
    public $content;
    public $rawMessages;
    public $resultMessages;

    public $warnings;

    public function __construct($html)
    {
        $this->content = $html;
    }

    public function parse()
    {
        $this->getRawMessages();
        $this->getResultMessages();
    }

    private function getRawMessages()
    {
        $rows = explode("\n", $this->content);

        $current_author = '';

        $raw_messages = array();

        foreach ($rows as $row)
        {
            $row = trim($row);
            if (empty($row))
                continue;

            $new_author = $this->getRowAuthorName($row);

            if ($new_author)
            {
                $row = str_replace($new_author, '', $row);
                $current_author = trim($new_author, '[]');
            }

            $row = trim($row);

            if (empty($row))
                continue;

            if (strpos($row, UPSELL_VALUE) !== false)
            {
                $up_rows = explode(UPSELL_VALUE, $row);
                $upsell_added = false;
                foreach ($up_rows as $u_row)
                {
                    $u_row = trim($u_row);

                    if (!empty($u_row))
                    {
                        $raw_messages[] = array(
                            'author' => $current_author,
                            'message' => $row
                        );
                    }
                    if (!$upsell_added)
                    {
                        $raw_messages[] = array(
                            UPSELL_TITLE => true
                        );
                        $upsell_added = true;
                    }
                }
            }
            else
            {
                $raw_messages[] = array(
                    'author' => $current_author,
                    'message' => $row
                );
            }
        }

        $this->rawMessages = $raw_messages;
    }

    private function getRowAuthorName($row)
    {
        $clean_text = trim(strip_tags($row));

        $author_preg = '~\[[@\w\s]+\]~';

        preg_match_all($author_preg, $clean_text, $m);

        if (count($m) > 1)
        {
            $this->warnings[] = "Have many authors in message \"$row\"";
            //save notice
        }

        preg_match($author_preg, $clean_text, $m);

        if (count($m) === 1 && $m[0] !== UPSELL_VALUE)
            return $m[0];

        $this->warnings[] = "Author not found in message \"$row\"";

        // save notice
        return false;
    }

    private function getResultMessages()
    {
        $result = array();
        foreach ($this->rawMessages as $raw)
        {
            if (isset($raw[UPSELL_TITLE]) && $raw[UPSELL_TITLE])
            {
                $result[] = array(
                    'type' => UPSELL_TITLE
                );
                continue;
            }

            $added = 0;
            $author = $raw['author'];
            $message = $raw['message'];

            $pattern = get_shortcode_regex();

            if (   preg_match_all( '/'. $pattern .'/s', $message, $matches )
                && array_key_exists( 2, $matches )
            )
            {
                foreach ($matches[2] as $preg_index => $shortcode)
                {
                    $args = array();
                    $args_content = $matches[3][$preg_index];
                    if (preg_match_all( '~(\w+)="(\S*)"~su', $args_content, $m_args ))
                    {
                        foreach ($m_args[1] as $index => $arg_name)
                            $args[$arg_name] = $m_args[2][$index];
                    }

                    if (empty($args))
                        continue;

                    if ($shortcode === 'video')
                    {
                        $support_video = array('mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv', 'mov', 'src');
                        $video_src = false;
                        foreach ($args as $arg => $value)
                        {
                            if (in_array($arg, $support_video))
                            {
                                $video_src = $value;
                                break;
                            }
                        }

                        if ($video_src)
                        {
                            $video_result = array(
                                'author' => $author,
                                'type' => 'video',
                                'src' => $video_src,
                            );

                            if (!empty($args['poster']))
                                $video_result['poster'] = $args['poster'];

                            $result[] = $video_result;
                        }
                        else
                        {
                            $this->warnings[] = 'Video src not found "' . $message . '"';
                        }
                    }
                }
            }

            $message = strip_shortcodes($message);

            if (!empty($message))
            {
                $dom = new DOMDocument;
                $dom->loadHTML($message);
                $images = $dom->getElementsByTagName('img');
                foreach ($images as $image) {
                    $added++;
                    $result[] = array(
                        'author' => $author,
                        'type' => 'image',
                        'src' => $image->getAttribute('src'),
                        'alt' => $image->getAttribute('alt'),
                    );
                }
            }

//            $videos = $dom->getElementsByTagName('video');
//            foreach ($videos as $video) {
//                $added++;
//                $result[] = array(
//                    'author' => $author,
//                    'type' => 'video',
//                    'src' => $video->getAttribute('src'),
//                );
//            }
//
//            $videos = $dom->getElementsByTagName('audio');
//            foreach ($videos as $video) {
//                $added++;
//                $result[] = array(
//                    'author' => $author,
//                    'type' => 'audio',
//                    'src' => $video->getAttribute('src'),
//                );
//            }

            $message_without_tags = trim(strip_tags($message));

            if (!empty($message_without_tags)){
                $added++;
                $result[] = array(
                    'author' => $author,
                    'type' => 'text',
                    'message' => $message_without_tags,
                );
            }

            if ($added > 1) {
                $this->warnings[] = 'Many messages in row "' . $message . '"';
            }

//            if (!$added) {
//                $this->warnings[] = 'Empty messages in row "' . $message . '"';
//            }
        }

        $this->resultMessages = $result;
    }
}