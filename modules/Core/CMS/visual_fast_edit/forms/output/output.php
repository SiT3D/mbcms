<?php

namespace MBCMS\Forms;

use MBCMS\Forms\OPT\main_option;
use MBCMS\Forms\OPT\title;

class output extends main_form implements \adminAjax
{

    public function __construct($parent = null)
    {
        parent::__construct(main_form::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico templates KEY_A', $this);
        }
    }

    public function init()
    {
        parent::init();

        $opt = new title('OUTPUT');
        $this->ADDM($opt, 'modules');

        $opt              = new main_option($this->get_setting('name'), 'name');
        $opt->hide_metric = true;
        $opt->type        = 'text';
        $opt->value_width = main_option::MW_LARGE;
        $this->ADDM($opt, 'modules');


        $opt = new title('Класс модуля');
        $this->ADDM($opt, 'modules');

        $opt              = new main_option($this->get_settingData('__user_cms_class'), '__user_cms_class');
        $opt->hide_metric = true;
        $opt->type        = 'text';
        $opt->value_width = main_option::MW_LARGE;
        $this->ADDM($opt, 'modules');

        $opt = new title('Название');
        $this->ADDM($opt, 'modules');

        $opt              = new main_option($this->get_settingData('__user_cms_out_title'), '__user_cms_out_title');
        $opt->type        = 'text';
        $opt->hide_metric = true;
        $opt->value_width = main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new title('Дополнительные классы');
        $this->ADDM($opt, 'modules');

        $opt              = new main_option($this->get_settingData('__user_cms_dop_css_classes'), '__user_cms_dop_css_classes');
        $opt->hide_metric = true;
        $opt->type        = 'text';
        $opt->value_width = main_option::MW_LARGEX;
        $this->ADDM($opt, 'modules');

        $opt = new title('Тип блока');
        $this->ADDM($opt, 'modules');

        $blocktype        = $this->get_settingData('__cms_block_type') ? $this->get_settingData('__cms_block_type') : 'div';
        $opt              = new main_option($blocktype, '__cms_block_type');
        $opt->metrix      = ['', 'div', 'span'];
        $opt->type        = 'text';
        $opt->value_width = main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new title('Вложенность в другие outputs');
        $this->ADDM($opt, 'modules');
    }

}
