<?php

namespace trud\conn\admin;

use MBCMS\routes;
use trud\admin\templates\vacancies_list;
use trud\admin\templates\vacancies_list\edit_form;

class vacancies extends connector
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new edit_form(),
            new vacancies_list(),
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
            self::$admin->add_content(new vacancies_list());
        }
        
    }
}
