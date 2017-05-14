<?php

namespace trud\conn\admin;

use MBCMS\routes;
use trud\admin\templates\candidats\edit_form;
use trud\admin\templates\candidats_list;

class candidats extends \trud\conn\admin\connector
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new edit_form,
            new candidats_list(),
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
            self::$admin->add_content(new candidats_list());
        }
    }

}
