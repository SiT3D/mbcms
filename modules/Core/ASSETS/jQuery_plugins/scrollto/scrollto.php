<?php

namespace Plugins;

class scrollto extends \Module
{
    public function __construct()
    {
        parent::__construct();
        $this->ignore_logic();
    }
}
