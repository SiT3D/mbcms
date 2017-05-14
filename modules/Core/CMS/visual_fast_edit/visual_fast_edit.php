<?php

namespace Plugins;

use GetPost;
use MBCMS\Forms\animation;
use MBCMS\Forms\cloner;
use MBCMS\Forms\DBV\connect_settings;
use MBCMS\Forms\DBV\tables;
use MBCMS\Forms\main_form;
use MBCMS\Forms\OPT\image_loader_picker;
use MBCMS\Forms\OPT\main_option;
use MBCMS\Forms\OPT\title;
use MBCMS\Forms\size;
use MBCMS\Forms\template;

class visual_fast_edit extends \Module
{

    private $__name = '/css_mod.css';

    public function init_files()
    {
        return [
            parent::init_files(),
            new choosen_select(),
            new jQuery_UI(),
            new colorpicker(),
            new scrollbar(),
            new size(),
            new main_form(),
            new connect_settings(),
            new tables(),
            new image_loader_picker(),
            new cloner(),
            new template(),
            new main_option(null,null),
            new title(),
            new animation(),
        ];
    }


    function create_css_modificator()
    {
        $idTemplate = GetPost::get('idTemplate');
        $content    = GetPost::get('content', '');

        $d         = self::get_module_cms_data_by_id($idTemplate);
        $className = $d['name'];

        if (\GClass::autoLoad($className))
        {
            $folder     = \GClass::$classInfo['folder'];
            $css_folder = $folder . '/css';
            if (!file_exists($css_folder))
                mkdir($css_folder);

            file_put_contents($css_folder . $this->__name, $content);
        }
    }

    function load_css_modificator()
    {
        $idTemplate = GetPost::get('idTemplate');

        $d         = self::get_module_cms_data_by_id($idTemplate);
        $className = $d['name'];

        if (\GClass::autoLoad($className))
        {
            $folder     = \GClass::$classInfo['folder'];
            $css_folder = $folder . '/css';
            if (!file_exists($css_folder))
                mkdir($css_folder);

            if (!file_exists($css_folder . $this->__name))
                file_put_contents($css_folder . $this->__name, '');
            else
                echo file_get_contents($css_folder . $this->__name);
        }

        $this->set_main_module();
        $this->not_render();
    }

}
