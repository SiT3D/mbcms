<?php

namespace MBCMS\image_galary;

class one_image extends \Module
{

    public $name;
    public $src;
    public $alt;
    public $id;
    public $tags     = [];
    public $tags_ids = [];

    public function __construct()
    {
        parent::__construct();
        $this->ignore_logic();
    }
    
}
