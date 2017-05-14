<?php

namespace MBCMS;

class template_element extends \Module
{

    public function init()
    {
        parent::init();

        $this->add_attr('idTemplate', 'idtemplate', null, 'btns');
        $this->add_attr('idTemplate', 'idtemplate', true);
        $this->add_attr('name', 'module_class', true);
        $this->add_attr('title', 'template_title', true);
    }

}
