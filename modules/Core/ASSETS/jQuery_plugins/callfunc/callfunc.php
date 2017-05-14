<?php

namespace Plugins;

class callfunc extends \Module
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new plugins(),
        ];
    }
}
