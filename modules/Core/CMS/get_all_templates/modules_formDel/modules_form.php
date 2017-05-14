<?php

namespace MBCMS;

class modules_form extends \Module
{

    public $modules = array();

    function init()
    {
        $ie = new ie();
        $ie->arr = $this->modules;

        $this->ADDM($ie, 'mmodules');
    }

}
