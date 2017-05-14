<?php

namespace trud\conn\admin;

use trud\admin\templates\admin_work_parser as template;

class admin_work_parser extends \trud\conn\admin\connector
{

    public function init()
    {
        parent::init();

        self::$admin->add_content(new template());
    }

}
