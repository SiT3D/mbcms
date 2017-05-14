<?php

namespace MBCMS\Forms\OPT\image_loader_picker;

class galary extends \Module
{

    /**
     * Массив src изображений
     */
    public $images = [];
    public $width  = '120px';

    public function __construct()
    {
        parent::__construct();
        $this->ignore_logic();
    }

}
