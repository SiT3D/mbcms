<?php

namespace MBCMS\Forms;

class fixed extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico padlock KEY_F', $this);
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

        $opt = new OPT\title('Запретить редактирование этого блока');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_settingData('fixed_padlock'), 'fixed_padlock');
        $opt->hide_value = true;
        $opt->metrix = ['work', 'fixed'];
        $this->ADDM($opt, 'modules');
    }

}
