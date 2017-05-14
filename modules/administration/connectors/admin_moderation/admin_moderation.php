<?php

namespace trud\conn\admin;

use trud\admin\templates\admin_moderation as template;

class admin_moderation extends  connector
{
    public function init()
    {
        parent::init();

        self::$admin->add_content(new template());
    }
}