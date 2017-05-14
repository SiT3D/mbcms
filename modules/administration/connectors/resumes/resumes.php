<?php

namespace trud\conn\admin;

use trud\admin\templates\resumes_list;
use trud\admin\templates\resumes_list\edit_form;

class resumes extends \trud\conn\admin\connector
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new edit_form,
            new resumes_list(),
        ];
    }

    public function init()
    {
        parent::init();

        if (!self::$admin)
        {
            return;
        }

        if (\MBCMS\routes::get_url_param(0))
        {
            self::$admin->add_content(new edit_form());
        }
        else
        {
            self::$admin->add_content(new resumes_list());
        }
    }

}
