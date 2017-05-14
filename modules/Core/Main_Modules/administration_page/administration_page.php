<?php

namespace MBCMS;

class administration_page extends \Module
{

    public function add_content(\Module $module)
    {
        $this->ADDM($module, 'content');
    }

}
