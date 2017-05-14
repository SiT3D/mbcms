<?php

namespace MBCMS;

/**
 * Визуализатор запросов
 */
class DBV extends block
{

    public $connect_module_name = '';

    public function init()
    {
        if (routes::is_admin())
        {
            $this->fast_edit($this, [
                new Forms\output($this),
                new Forms\display($this),
                new Forms\text($this),
                new Forms\text_styles($this),
                new Forms\size($this),
                new Forms\position($this),
                new Forms\deleter($this),
                new Forms\DBV\connect_settings($this),
                new Forms\DBV\tables($this),
                new Forms\DBV\columns_select($this),
//                new Forms\DBV\where($this),
//                new Forms\DBV\final_step($this),
            ]);
        }

        parent::init();

        $this->__sql();



//        $this->__connect_modules();
    }

    public function static_nature()
    {
        if (routes::is_static_status())
        {
            unset($this->modules);
            return $this->__static_nature($this);
        }
        else
        {
            return true;
        }
    }

    private function __connect_modules()
    {
        if (!isset($this->connect_module_name) || !isset($this->result_query))
        {
            return;
        }

        if (!\GClass::autoLoad($this->connect_module_name))
        {
            return;
        }

        $this->result_query = $this->result_query && $this->result_query !== 'destroy' ? $this->result_query : null;

        if ($this->result_query)
        {
            foreach (\MBCMS\DB::q($this->result_query)->get() as $data)
            {
                $m = new $this->connect_module_name();
                $m->clone_settings($data);
                $this->ADDM($m, 'modules');
            }
        }
    }

    private function __sql()
    {
        if (isset($this->SQL_test) && $this->SQL_test && $this->SQL_test !== 'destroy' && $this->connect_module_name)
        {
            foreach (DB::q($this->SQL_test)->get() as $data)
            {
                $m = new $this->connect_module_name();
                $m->clone_settings($data);
                $this->ADDM($m, 'modules');
            }
        }
    }

}
