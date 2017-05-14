<?php

namespace trud\conn\admin;

use trud\admin\templates\admin_synonyms;

class synonyms extends connector
{
    public function init()
    {
        parent::init();

        self::$admin->add_content(new admin_synonyms());
    }
}