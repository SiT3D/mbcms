<?php

namespace MBCMS\Forms\DBV;

class columns_select extends \MBCMS\Forms\main_form
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


        $opt = new \MBCMS\Forms\OPT\title('SELECT [поле] AS [alias необяз!]');
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
      $col

      ["Field"]=>
      string(2) "id"
      ["Type"]=>
      string(16) "int(11) unsigned"
      ["Null"]=>
      string(2) "NO"
      ["Key"]=>
      string(3) "PRI"
      ["Default"]=>
      NULL
      ["Extra"]=>
      string(14) "auto_increment"
     * 
     * @param $col
     * @param $table
     */
    private function __options_by_column($col, $table)
    {
        $key = $table . '.' . $col->Field;

        $opt = new \MBCMS\Forms\OPT\title();
        $this->ADDM($opt, 'modules');
        
        $opt              = new \MBCMS\Forms\OPT\main_option($key, $key . '_name');
        $opt->hide_metric = true;
        $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
        $opt->readonly    = true;
        $this->ADDM($opt, 'modules');

        $opt              = new \MBCMS\Forms\OPT\main_option($this->get_settingData($key . '_alias'), $key . '_alias');
        $opt->type        = \MBCMS\Forms\OPT\main_option::TYPE_TEXT;
        $opt->hide_metric = true;
        $this->ADDM($opt, 'modules');
    }

}
