<?php


namespace trud\conn\admin;

use trud\admin\templates\admin_xml_parser as template;

class admin_xml_parser extends connector
{
    public function init()
    {
        parent::init();

        self::$admin->add_content(new template);
    }
}