<?php

namespace MBCMS;

use Assets\jQuery;

class mbcms_assets extends \Module
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new jQuery(),
            new routes(),
        ];
    }

    public function init()
    {
        parent::init();

        if (!routes::is_admin())
        {
            $this->json = json_encode([
                'is_static_templates' => configuration::factory()->is_static_templates(),
                'idTemplate' => routes::get_current_idTemplate(),
            ]);
        }
        else
        {
            $this->not_render();
        }

    }
}