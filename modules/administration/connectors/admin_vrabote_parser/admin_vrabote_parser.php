<?php

namespace trud\conn\admin;


use MBCMS\form\input;

class admin_vrabote_parser extends connector
{

    public function init()
    {
        parent::init();

        self::$admin->add_content(new \trud\admin\templates\admin_vrabote_parser());
    }
}