<?php

namespace MBCMS\Forms\DBV;

class final_step extends \MBCMS\Forms\main_form
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

        $opt = new \MBCMS\Forms\OPT\title('Final');
        $this->ADDM($opt, 'modules');

        $this->__columns();
    }
    
    // сборка должна быть в самом DBV
    // еще сделать подстановку переменных из шаблона, тоесть шаблон опрееляется первым!

    private function __columns()
    {
        $tables = $this->get_settingData('all_tables');

        $result_query = '';

        $select_array = [];
        $tables_array = [];
        $where_array  = [];


        foreach ($tables as $table)
        {
            $columns = \MBCMS\DB::q("SHOW COLUMNS FROM $table ")->get();

            $tables_array[] = $table;

            foreach ($columns as $col)
            {
                if ($val = $this->__get_select_result($col, $table))
                {
                    $select_array[] = $val;
                }
            }

            foreach ($columns as $col)
            {
                if ($val = $this->__get_where_result($col, $table))
                {
                    $where_array[] = $val;
                }
            }
        }

        if ($select_array && $tables_array && $where_array)
        {
            $result_query = 'SELECT ' . implode(',', $select_array) . ' FROM ' . implode(',', $tables_array) . ' WHERE ' . implode(' ', $where_array);
        }

        $opt              = new \MBCMS\Forms\OPT\main_option($result_query, 'result_query');
        $opt->hide_metric = true;
        $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_AREA;
        $opt->readonly    = true;
        $this->ADDM($opt, 'modules');

        $opt = new \MBCMS\Forms\OPT\title('Класс модуля');
        $this->ADDM($opt, 'modules');

        $opt              = new \MBCMS\Forms\OPT\main_option($this->get_settingData('connect_module_name'), 'connect_module_name');
        $opt->hide_metric = true;
        $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
        $this->ADDM($opt, 'modules');

        $this->__connect_modules($result_query);
    }

    private function __get_select_result($col, $table)
    {
        $key   = $table . '.' . $col->Field;
        $alias = $this->get_settingData($key . '_alias');
        $name  = $this->get_settingData($key . '_name');
        if ($alias !== 'destroy' && $alias)
        {
            return $name . ' AS ' . $alias;
        }
    }

    private function __get_where_result($col, $table)
    {
        //_name + _alias + __before_operator + __operator + __value + __after_operator
        //$this->get_settingData($key . '__before_operator')
        // SELECT [_name AS _alias] FROM [table, table] WHERE [__before_operator + __operator + __value + __after_operator ]


        $key = $table . '.' . $col->Field;

        $key = $table . '.' . $col->Field;

        $name     = $this->get_settingData($key . '_name');
        $this->__get_real_value($name);
        $bop      = $this->get_settingData($key . '__before_operator');
        $this->__get_real_value($bop);
        $oparator = $this->get_settingData($key . '__operator');
        $this->__get_real_value($oparator);
        $value    = $this->get_settingData($key . '__value');
        $this->__get_real_value($value);
        $aop      = $this->get_settingData($key . '__after_operator');
        $this->__get_real_value($aop);

        if ($oparator)
        {
            return $bop . $name . $oparator . $value . $aop;
        }
    }

    private function __get_real_value(&$var)
    {
        if ($var && $var != 'destroy')
        {
            $var = ' ' . $var . ' ';
        }
        else
        {
            $var = '';
        }
    }

}
