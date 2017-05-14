<?php

namespace MBCMS;

class dinamic_js_css_loader extends \Module implements \adminAjax
{

    public $connect_class_name;
    public $connect_idTemplate;
    private $ajax;
    private $find_sort_class;

    public function init_files()
    {
        $ret = [
            parent::init_files(),
        ];

        if ($this->connect_class_name && \GClass::autoLoad($this->connect_class_name))
        {
            $ret[] = new $this->connect_class_name;
        }

        return $ret;
    }

    public function ajax()
    {
        $this->ajax               = true;
        $this->connect_class_name = \GetPost::get('connect_class_name');
        $this->connect_idTemplate = \GetPost::get('connect_idTemplate');

        if ($this->connect_class_name && \GClass::autoLoad($this->connect_class_name))
        {
            $this->ADDM(new $this->connect_class_name, self::NO_POSITION);
        }

        $m = false;

        if ($this->connect_idTemplate)
        {
            $data                  = \Module::get_module_cms_data_by_id($this->connect_idTemplate);
            $this->find_sort_class = $data['name'];
            $className             = isset($data['name']) ? $data['name'] : '';
            if (\GClass::autoLoad($className))
            {
                $m          = new $className;
                $m->set_main_module();
                $m->ADDM($this, 'modules');
                $m->CMSData = $data;
                $m->not_render();
            }
        }

        if (!$m)
        {
            $this->set_main_module();
            $this->not_render();
        }
    }

    public function init()
    {
        if (!$this->ajax)
        {
            parent::init();
        }
    }

    public function after_init()
    {
        if ($this->ajax)
        {
            parent::after_init();
            $allModules = \Modules::get_all_modules();
            foreach ($allModules as $module)
            {
                if ($module->get_my_parent_id() !== null)
                {
                    $module->not_files();
                }
            }
        }
    }

    function preview()
    {
        if ($this->ajax)
        {
            $files = \Modules::get_all_files();

            foreach ($files['css'] as &$css)
            {
                $css['metapath'] = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $css['metapath']);
            }

            echo json_encode($files);
        }
        $this->not_render();
    }

}
