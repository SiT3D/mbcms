<?php

namespace MBCMS\Forms\OPT;

class title extends \Module
{

    public $title;
    public $mini = false;

    public function __construct($title = '')
    {
        parent::__construct();

        $this->title = $title;
        $this->mini = $title ? false : true;
    }

}
