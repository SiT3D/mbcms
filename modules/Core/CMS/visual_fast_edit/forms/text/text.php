<?php

namespace MBCMS\Forms;

class text extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico pen KEY_T', $this);
        }
    }

    public function init()
    {
        parent::init();
        
        $opt = new OPT\title('Текст модуля');
        $this->ADDM($opt, 'modules');

        $opt              = new OPT\main_option($this->get_settingData('__text'), '__text');
        $opt->setCKEditor(); // тут нужно сделать чтобы по сыбитию, срабатывало и обновление текстареа со всеми динамическими моментами
        $opt->hide_metric = true;
        $opt->type        = OPT\main_option::TYPE_AREA;
        $this->ADDM($opt, 'modules');
    }


}
