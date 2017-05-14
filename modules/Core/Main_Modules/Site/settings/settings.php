<?php

namespace MBCMS\Site;

class settings extends \Module implements \adminAjax
{

    public function ajax()
    {
        $this->set_main_module(2, 2);
    }

}
