<?php

namespace MBCMS\Forms\DBV;

class connect_settings extends \MBCMS\Forms\main_form
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


        $opt = new \MBCMS\Forms\OPT\title('Тестовый запрос');
        $this->ADDM($opt, 'modules');

        $opt              = new \MBCMS\Forms\OPT\main_option($this->get_settingData('SQL_test'), 'SQL_test');
        $opt->hide_metric = true;
        $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_AREA;
        $this->ADDM($opt, 'modules');


        $opt = new \MBCMS\Forms\OPT\title('Класс модуля');
        $this->ADDM($opt, 'modules');

        $opt              = new \MBCMS\Forms\OPT\main_option($this->get_settingData('connect_module_name'), 'connect_module_name');
        $opt->hide_metric = true;
        $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
        $this->ADDM($opt, 'modules');
    }

    public function ajax_get_autocomplete()
    {
        $tables = \MBCMS\DB::q("SHOW TABLES")->get();

        $result = [];

        foreach ($tables as $table)
        {
            $result[$table->Tables_in_rabota1] = $table->Tables_in_rabota1;
        }

        $tables = $result;

        foreach ($tables as $table)
        {
            $clumns = \MBCMS\DB::q("SHOW COLUMNS FROM $table ")->get();

            foreach ($clumns as $col)
            {
                $result[$col->Field] = $col->Field;
            }
        }

        self::add_response('auto', $result);
        self::response();
    }

}
