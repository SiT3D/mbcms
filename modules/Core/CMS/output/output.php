<?php

namespace MBCMS;

use GetPost;
use Module;

class output extends Module implements \adminAjax
{

    function get()
    {
        $m             = new output();
        $m->idTemplate = GetPost::get('idTemplate');
        $m->set_main_module(1, 1);
    }

    function save()
    {
        $idTemplate = GetPost::get('idTemplate');
        $modules    = GetPost::get('modules');

        Module::save_outputs_by_id($idTemplate, $modules);
    }

    function update()
    {
        $idTemplate = GetPost::get('idTemplate');
        $out_index  = GetPost::get('out_index');
        $data       = GetPost::get('data');

        Module::update_output_by_id($idTemplate, $out_index, $data);
    }

    /**
     *
     * @param $idTemplate
     * @param $out_index
     * @param $new_class
     */
    function update_output_class($idTemplate, $out_index, $new_class)
    {
        $idTemplate = GetPost::get('idTemplate', $idTemplate);
        $out_index  = GetPost::get('out_index', $out_index);
        $new_class  = GetPost::get('new_class', $new_class);

        
        $d = self::get_module_cms_data_by_id($idTemplate);

        if (isset($d['outputs'][$out_index]) && \GClass::autoLoad($new_class))
        {
            $d['outputs'][$out_index]['name'] = $new_class;
        }
        
        self::save_outputs_by_id($idTemplate, $d['outputs']);
    }

    function update_array()
    {
        $idTemplate = GetPost::get('idTemplate');
        $data       = GetPost::get('data');

        Module::update_output_by_id_array($idTemplate, $data);
    }

    function add_array()
    {
        $idTemplate = GetPost::get('idTemplate');
        $outputs    = GetPost::get('outputs');
        Module::add_output_array($idTemplate, $outputs);
        $this->set_main_module();
        $this->not_render();
    }

    /**
     * Удаляет out из указанного шаблона
     */
    function remove()
    {
        $idTemplate = GetPost::get('idTemplate');
        $out_index  = GetPost::get('out_index');
        Module::remove_output($idTemplate, $out_index);
    }

    function add()
    {
        $idTemplate = GetPost::get('idTemplate');
        $name       = GetPost::get('out_class');
        $data       = GetPost::get('data', []);
        Module::add_output($idTemplate, $name, $data);
        $this->set_main_module(10, 10);
        $this->not_render();
    }

    function add_more()
    {
        $idTemplate = GetPost::get('idTemplate');
        $name       = GetPost::get('out_class');
        $data       = GetPost::get('data', []);
        $count      = GetPost::get('count', 0);
        Module::add_output($idTemplate, $name, $data, $count);
        $this->set_main_module();
        $this->not_render();
    }

    function resort()
    {
        $idTemplate = GetPost::get('idTemplate');
        $indexis    = GetPost::get('indexis');
        Module::resort_outputs($idTemplate, $indexis);

        $this->not_render();
        $this->set_main_module(1, 1);
    }


}
