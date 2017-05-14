<?php

namespace MBCMS\Forms\DBV;

class tables extends \MBCMS\Forms\main_form implements \adminAjax
{

    public function __construct($parent = null)
    {
        parent::__construct(\MBCMS\Forms\main_form::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico database_ico', $this);
        }

        $this->dop_css = 'tables_form_';
    }

    public function init()
    {
        parent::init();


        $opt = new \MBCMS\Forms\OPT\title('Список всех таблиц');
        $this->ADDM($opt, 'modules');

        $tables     = \MBCMS\DB::q("SHOW TABLES")->get();
        $all_tables = ['none'];

        foreach ($tables as $table)
        {
            $all_tables[] = isset($table->Tables_in_rabota1) ? $table->Tables_in_rabota1 : null;
        }

        $opt                  = new \MBCMS\Forms\OPT\main_option($this->get_settingData('all_tables'), 'all_tables');
        $opt->hide_value      = true;
        $opt->metrix          = $all_tables;
        $opt->dop_classes_metric .= ' tables_picker';
        $opt->multiple_metrix = true;
        $this->ADDM($opt, 'modules');
    }

    public function ajax_get_rows()
    {
        $tables   = \GetPost::get('table');
        $all_rows = [];

        if ($tables)
        {
            foreach ($tables as $table)
            {
                $rows = \MBCMS\DB::q("SHOW COLUMNS FROM $table ")->get();

                foreach ($rows as $rows)
                {
                    foreach ($rows as $key2 => $__row)
                    {
                        $all_rows[$key2][] = $__row;
                    }
                }
            }
        }

        self::add_response('rows', $all_rows);
        self::response();
    }

}
