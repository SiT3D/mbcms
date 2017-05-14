<?php

namespace MBCMS\Forms\OPT;

class standart_hiddens extends \Module
{

    public function __construct($idTemplate = null, $current_class = null, $parent_class = null, $index = null, $pidTemplate = null)
    {
        parent::__construct();

        $this->idTemplate    = \GetPost::get('idTemplate', $idTemplate);
        $this->current_class = \GetPost::get('current_class', $current_class);
        $this->parent_class  = \GetPost::get('parent_class', $parent_class);
        $this->index         = \GetPost::get('__cms_output_index', $index);
        $this->pidTemplate   = \GetPost::get('pidTemplate', $pidTemplate);
    }

}
