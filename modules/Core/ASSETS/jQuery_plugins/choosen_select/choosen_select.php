<?php

namespace Plugins;

use Assets\jQuery;

class choosen_select extends \Module
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new jQuery(),
        ];
    }

}

