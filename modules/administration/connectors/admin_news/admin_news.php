<?php

namespace trud\conn\admin;

use MBCMS\routes;
use trud\admin\templates\admin_news as template;
use trud\admin\templates\news_list\edit_form;
use trud\admin\templates\news_list;

class admin_news extends connector
{
    public function init_files()
    {

        return[
        parent::init_files(),
            new edit_form(),
            new news_list(),
        ];
    }

    public function init()
    {
        parent::init();

        if (routes::get_url_param(0))
        {
            self::$admin->add_content(new news_list\edit_form());
        }
        else
        {
            self::$admin->add_content(new news_list());
        }
    }
}