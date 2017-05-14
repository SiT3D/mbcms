<?php

namespace MBCMS\Forms;

use MBCMS\Forms\OPT\main_option;
use MBCMS\Forms\OPT\title;

class cloner extends main_form implements \adminAjax
{

    public function __construct($parent = null)
    {
        parent::__construct(null);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico templates KEY_C', $this);
        }
    }

    public function init()
    {
        parent::init();

        $opt = new title('Клонирование');
        $this->ADDM($opt, 'modules');
        
        $opt              = new main_option('', 0);
        $opt->hide_metric = true;
        $opt->dop_classes_value = 'cloner_counter';
        $opt->value_width = main_option::MW_LARGE;
        $this->ADDM($opt, 'modules');

    }

}
