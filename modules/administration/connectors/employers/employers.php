<?php

namespace trud\conn\admin;

use MBCMS\routes;
use trud\admin\templates\employers_list;
use trud\admin\templates\employers_list\edit_form;

class employers extends connector
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new edit_form(),
            new employers_list(),
        ];
    }

    public function init()
    {
        parent::init();
        
        if (!self::$admin)
        {
            return;
        }
        
        if (routes::get_url_param(0))
        {
            self::$admin->add_content(new edit_form());
        }
        else
        {
            self::$admin->add_content(new employers_list);
        }
        
    }
}
