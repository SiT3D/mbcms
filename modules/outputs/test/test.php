<?php

namespace cms;

use Assets\jQuery;
use MBCMS\DBOld_config;
use MBCMS\out;

class test extends out
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new jQuery(),
        ];
    }

    public function init()
    {
        parent::init();

    }
}