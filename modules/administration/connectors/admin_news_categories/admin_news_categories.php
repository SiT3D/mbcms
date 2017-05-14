<?php

namespace trud\conn\admin;

use MBCMS\routes;
use trud\admin\templates\admin_news_categories\edit_form;
use trud\admin\templates\admin_news_categories_list;

class admin_news_categories extends connector
{
    public function init_files()
    {

        return [
            new edit_form(),
            new admin_news_categories_list(),
            parent::init_files(),
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
            self::$admin->add_content(new admin_news_categories_list());
        }
    }
}