<?php

namespace MBCMS\Forms\OPT;

class hidden extends \Module
{

    public $name  = '';
    public $value = '';

    /**
     * 
     * @param $name
     * @param $value
     */
    public function __construct($name, $value)
    {
        parent::__construct();
        $this->ignore_logic();
        
        $this->name = $name;
        $this->value = $value;
    }

}
