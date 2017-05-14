<?php

namespace MBCMS\Forms;

class border extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_STYLE);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico border_ico KEY_B', $this, 'view');
        }
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new OPT\title,
            new OPT\main_option(null, null),
        ];
    }

    public function init()
    {
        parent::init();

        $metrix = ['', 'solid', 'none', 'dashed', 'dotted', 'double'];

        $opt = new OPT\title('Граница верх / низ');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('border_top_width'), 'border_top_width');
        $this->ADDM($opt, 'modules');

        $opt             = new OPT\main_option($this->get_style('border_top_style'), 'border_top_style');
        $opt->hide_value = true;
        $opt->metrix     = $metrix;
        $this->ADDM($opt, 'modules');

        $opt              = new OPT\main_option($this->get_style('border_top_color'), 'border_top_color');
        $opt->hide_metric = true;
        $opt->type        = 'text';
        $opt->setColorPIcker();
        $this->ADDM($opt, 'modules');

        $opt       = new OPT\title();
        $opt->mini = true;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('border_bottom_width'), 'border_bottom_width');
        $this->ADDM($opt, 'modules');

        $opt             = new OPT\main_option($this->get_style('border_bottom_style'), 'border_bottom_style');
        $opt->hide_value = true;
        $opt->metrix     = $metrix;
        $this->ADDM($opt, 'modules');

        $opt              = new OPT\main_option($this->get_style('border_bottom_color'), 'border_bottom_color');
        $opt->setColorPIcker();
        $opt->hide_metric = true;
        $opt->type        = 'text';
        $this->ADDM($opt, 'modules');


        $opt = new OPT\title('Граница Лево / Право');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('border_left_width'), 'border_left_width');
        $this->ADDM($opt, 'modules');

        $opt             = new OPT\main_option($this->get_style('border_left_style'), 'border_left_style');
        $opt->hide_value = true;
        $opt->metrix     = $metrix;
        $this->ADDM($opt, 'modules');

        $opt              = new OPT\main_option($this->get_style('border_left_color'), 'border_left_color');
        $opt->setColorPIcker();
        $opt->hide_metric = true;
        $opt->type        = 'text';
        $this->ADDM($opt, 'modules');

        $opt       = new OPT\title();
        $opt->mini = true;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('border_right_width'), 'border_right_width');
        $this->ADDM($opt, 'modules');

        $opt             = new OPT\main_option($this->get_style('border_right_style'), 'border_right_style');
        $opt->hide_value = true;
        $opt->metrix     = $metrix;
        $this->ADDM($opt, 'modules');

        $opt              = new OPT\main_option($this->get_style('border_right_color'), 'border_right_color');
        $opt->setColorPIcker();
        $opt->hide_metric = true;
        $opt->type        = 'text';
        $this->ADDM($opt, 'modules');


        $opt = new OPT\title('Радиусы углов верх Лево / Право ');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('border_top_left_radius'), 'border_top_left_radius');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('border_top_right_radius'), 'border_top_right_radius');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Радиусы углов низ Лево / Право ');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('border_bottom_left_radius'), 'border_bottom_left_radius');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('border_bottom_right_radius'), 'border_bottom_right_radius');
        $this->ADDM($opt, 'modules');
    }

}
