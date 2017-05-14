<?php

namespace MBCMS\Forms\OPT;

class checkbox extends \Module
{

    public $checked = false;
    public $name = false;

    public function __construct($value, $key)
    {
        parent::__construct();

        if ($value)
        {
            $this->checked = true;
        }
        
        $this->name = $key;
    }

}
