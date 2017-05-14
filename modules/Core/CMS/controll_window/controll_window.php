<?php

namespace MBCMS;

class controll_window extends \Module implements \adminAjax
{

    public function ajax()
    {
        $this->AJAX = true;
        $this->set_main_module(1, 1);
    }

    function init()
    {
        parent::init();
        if (!isset($this->AJAX))
            return;

        $idTemplate = \GetPost::get('idTemplate');

        if (!$idTemplate)
        {
            $adr        = \GetPost::get('adr');
            $nhostadr   = \MBCMS\routes::remove_host($adr);
            $validAdr   = \MBCMS\routes::remove_admin_adr($nhostadr);
            $idTemplate = \MBCMS\routes::get_template_id($validAdr);
        }

        \Modules::get_template($idTemplate);

        $allModules = \Modules::get_all_modules();

        foreach ($allModules as $module)
        {
            if ($module->get_my_parent_id() === null)
            {
                $this->ADDM($module, 'modules');
            }
        }
    }

    public function preview()
    {
        parent::preview();

        $allModules = \Modules::get_all_modules();

        foreach ($allModules as $module)
        {
            $module->connect_type = isset($module->__cms_connect_type) ? $module->__cms_connect_type : '';
            $module->add_attr('connect_type', 'connect_type', true);
            $module->add_attr('echo_module_class', 'fast_edit_class', true);
            $module->add_attr('__user_cms_class', 'css_class', true);

            if (isset($module->__cms_connect_type) && $module->__cms_connect_type === \Module::__cms_connect_type_TEMPLATE)
            {
                $module->idtemplate = isset($module->CMSData['idTemplate']) ? $module->CMSData['idTemplate'] : '';
                $module->add_attr('idtemplate', 'idtemplate', true);

                $module->module_class = isset($module->CMSData['name']) ? $module->CMSData['name'] : '';
                $module->add_attr('module_class', 'module_class', true);

                $module->template_title = isset($module->CMSData['title']) ? $module->CMSData['title'] : '';
                $module->add_attr('template_title', 'template_title', true);
            }
        }
    }

}
