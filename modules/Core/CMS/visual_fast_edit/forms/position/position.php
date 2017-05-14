<?php

namespace MBCMS\Forms;

class position extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_STYLE);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico move2 KEY_Q', $this);
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

        $opt = new OPT\title('Позиция');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('top'), 'top');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('bottom'), 'bottom');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('left'), 'left');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('right'), 'right');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Отступы с наружи');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('margin_top'), 'margin_top');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('margin_bottom'), 'margin_bottom');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('margin_left'), 'margin_left');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('margin_right'), 'margin_right');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Выравнивание текста внутри блока');
        $this->ADDM($opt, 'modules');

        $opt             = new OPT\main_option($this->get_style('text_align'), 'text_align');
        $opt->hide_value = true;
        $opt->metrix     = ['inherit', 'left', 'right', 'center'];
        $this->ADDM($opt, 'modules');


        $opt = new OPT\title('Тип позиции');
        $this->ADDM($opt, 'modules');

        $opt             = new OPT\main_option($this->get_style('position'), 'position');
        $opt->hide_value = true;
        $opt->metrix     = ['relative', 'absolute', 'destroy', 'fixed', 'static'];
        $this->ADDM($opt, 'modules');
    }

}
