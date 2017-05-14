<?php

namespace MBCMS\Forms;

class autocreater_options extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(main_form::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico templates KEY_A', $this);
        }
    }

    function init()
    {
        parent::init();

        $opt = new \MBCMS\Forms\OPT\title('HTML');
        $this->ADDM($opt, 'modules');

        $opt              = new OPT\main_option($this->get_setting('HTML'), 'HTML');
        $opt->hide_metric = true;
        $opt->type        = 'textarea';
        $this->ADDM($opt, 'modules');

        $opt = new \MBCMS\Forms\OPT\title('URL');
        $this->ADDM($opt, 'modules');

        $opt              = new OPT\main_option($this->get_setting('URL'), 'URL');
        $opt->hide_metric = true;
        $opt->type        = 'text';
        $this->ADDM($opt, 'modules');
    }

}
