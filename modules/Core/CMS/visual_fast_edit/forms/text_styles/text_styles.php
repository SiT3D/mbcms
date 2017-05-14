<?php

namespace MBCMS\Forms;

class text_styles extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_STYLE);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico pen KEY_T KEY_SHIFT', $this);
        }
    }

    public function init()
    {
        parent::init();


        $opt = new OPT\title('Высота строки');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('line_height'), 'line_height');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Цвет');
        $this->ADDM($opt, 'modules');

        $opt              = new OPT\main_option($this->get_style('color'), 'color');
        $opt->hide_metric = true;
        $opt->setColorPIcker();
        $opt->type        = OPT\main_option::TYPE_TEXT;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Размер текста');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('font_size'), 'font_size');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Жирность');
        $this->ADDM($opt, 'modules');

        $opt             = new OPT\main_option($this->get_style('font_weight'), 'font_weight');
        $opt->hide_value = true;
        $opt->metrix     = ['normal', 'bold', 'inherit'];
        $this->ADDM($opt, 'modules');
    }

}
