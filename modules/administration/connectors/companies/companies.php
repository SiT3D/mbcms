<?php

namespace trud\conn\admin;

use MBCMS\routes;
use trud\admin\templates\companies_list;
use trud\admin\templates\companies_list\edit_form;

class companies extends connector
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new edit_form(),
            new companies_list(),
        ];
    }

    /**
     *
     */
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
            self::$admin->add_content(new companies_list());
        }
    }

}
