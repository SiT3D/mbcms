<?php

namespace MBCMS\Forms;

class deleter extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(null);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico del KEY_DEL', $this, 'view');
        }
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new main_form()
        ];
    }
    
    public function init()
    {
        $this->ADDM(new main_form(), 'modules');
    }

}
