<?php

namespace MBCMS\Forms;

class size extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_STYLE);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico width_and_height KEY_S', $this, 'view');
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

        $opt = new OPT\title('Ширина');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('min_width'), 'min_width');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('width'), 'width');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('max_width'), 'max_width');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Высота');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('min_height'), 'min_height');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('height'), 'height');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('max_height'), 'max_height');
        $this->ADDM($opt, 'modules');
        
         
        $opt = new OPT\title('Внутренние отступы');
        $this->ADDM($opt, 'modules');
        
        $opt = new OPT\main_option($this->get_style('padding_top'), 'padding_top');
        $this->ADDM($opt, 'modules');
        
        $opt = new OPT\main_option($this->get_style('padding_bottom'), 'padding_bottom');
        $this->ADDM($opt, 'modules');
         
        $opt = new OPT\main_option($this->get_style('padding_left'), 'padding_left');
        $this->ADDM($opt, 'modules');
         
        $opt = new OPT\main_option($this->get_style('padding_right'), 'padding_right');
        $this->ADDM($opt, 'modules');
    }

}
