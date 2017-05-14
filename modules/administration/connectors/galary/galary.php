<?php

namespace trud\conn\admin;

use MBCMS\image_galary;

class galary extends connector
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new image_galary(),
        ];
    }

    public function init()
    {
        parent::init();

        if (!self::$admin)
        {
            return;
        }

        self::$admin->add_content(new image_galary());
    }

}
