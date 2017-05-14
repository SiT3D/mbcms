<?php

namespace MBCMS\Forms\DBV;

class where extends \MBCMS\Forms\main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(\MBCMS\Forms\main_form::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico database_ico', $this);
        }
    }

    public function init()
    {
        parent::init();


        $opt = new \MBCMS\Forms\OPT\title('Where [AND] [t_users.id] [=] [zna4]');
        $this->ADDM($opt, 'modules');

        $this->__columns();
    }

    private function __columns()
    {
        $tables = $this->get_settingData('all_tables');

        foreach ($tables as $table)
        {
            $clumns = \MBCMS\DB::q("SHOW COLUMNS FROM $table ")->get();

            foreach ($clumns as $col)
            {
                $this->__options_by_column($col, $table);
            }
        }
    }

    /**
     * 
     * 
     * @param $col
     * @param $table
     */
    private function __options_by_column($col, $table)
    {
        $key = $table . '.' . $col->Field;

        $alias = $this->get_settingData($key . '_alias');

        if ($alias != 'destroy' && $alias)
        {
            $opt = new \MBCMS\Forms\OPT\title('');
            $this->ADDM($opt, 'modules');

            $opt              = new \MBCMS\Forms\OPT\main_option($this->get_settingData($key . '__before_operator'), $key . '__before_operator');
            $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
            $opt->hide_metric = true;
            $this->ADDM($opt, 'modules');

            $opt              = new \MBCMS\Forms\OPT\main_option($key, $key . '_name');
            $opt->hide_metric = true;
            $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
            $opt->readonly    = true;
            $this->ADDM($opt, 'modules');


            $opt              = new \MBCMS\Forms\OPT\main_option($this->get_settingData($key . '__operator'), $key . '__operator');
            $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
            $opt->hide_metric = true;
            $this->ADDM($opt, 'modules');

            $opt              = new \MBCMS\Forms\OPT\main_option($this->get_settingData($key . '__value'), $key . '__value');
            $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
            $opt->hide_metric = true;
            $this->ADDM($opt, 'modules');

            $opt              = new \MBCMS\Forms\OPT\main_option($this->get_settingData($key . '__after_operator'), $key . '__after_operator');
            $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
            $opt->hide_metric = true;
            $this->ADDM($opt, 'modules');
        }
    }

}
