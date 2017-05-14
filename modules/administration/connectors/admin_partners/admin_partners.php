<?php

namespace trud\conn\admin;


use MBCMS\routes;
use trud\admin\templates\admin_partners\edit_form;
use trud\admin\templates\admin_partners_list;

class admin_partners extends connector
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new edit_form(),
            new admin_partners_list(),
        ];
    }

    public function init()
    {
        parent::init();


        if (routes::get_url_param(0))
        {
            self::$admin->add_content(new edit_form());
        }
        else
        {
            self::$admin->add_content(new admin_partners_list());
        }
    }
}