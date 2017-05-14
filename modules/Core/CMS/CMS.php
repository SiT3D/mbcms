<?php

namespace MBCMS;

use Assets\ck_editor;
use Assets\jQuery;
use event\standart_security_login_event;
use MBCMS\Forms\main_form;
use Plugins\scrollbar;
use Plugins\scrollto;
use Plugins\visual_fast_edit;

class CMS extends \Module
{

    public $innerModule = null;

    public function init_files()
    {
        return [
            parent::init_files(),
            $this->innerModule,
            new mbcms_assets(),
            new CSS(),
            new controll_window(),
            new get_all_templates(),
            new Iframe(),
            new icons_cms(),
            new dinamic_js_css_loader(),
            new scrollbar(),
            new Option(),
            new tblock(),
            new visual_fast_edit(),
            new template(),
            new output(),
            new block(),
            new out(),
            new jQuery(),
            new scrollto(),
            new ck_editor(),
        ];
    }

}
