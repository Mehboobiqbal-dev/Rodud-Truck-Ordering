<?php

namespace App\Notifications\Messages;

class TwilioMessage
{
    public $content;

    public function __construct($content = '')
    {
        $this->content = $content;
    }

    public function content($content)
    {
        $this->content = $content;

        return $this;
    }
}
