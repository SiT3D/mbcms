<?php

namespace Assets;

class bootstrap extends \Module
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new jQuery(),
        ];
    }
}

