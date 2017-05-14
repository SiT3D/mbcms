<?php


namespace trud\conn\admin;


class admin_settings extends connector
{
    public function init()
    {
        parent::init();

        self::$admin->add_content(new \global_settings());
    }
}