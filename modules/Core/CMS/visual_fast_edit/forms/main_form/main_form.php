<?php

namespace MBCMS\Forms;

use GetPost;

class main_form extends \Module implements \adminAjax
{

    const FORM_TYPE_STYLE    = 1;
    const FORM_TYPE_SETTINGS = 2;

    public $action            = '';
    public $method_update     = null;
    public $form_method       = 'GET';
    public $dop_css           = '';
    protected $__styles       = null;
    protected $__settings     = null;
    protected $__settingsData = null;

    public function init_files()
    {
        parent::init_files();

        return [
            parent::init_files(),
            new OPT\standart_hiddens(),
            new OPT\title,
            new OPT\main_option(null, null),
        ];
    }

    /**
     * 
     * @param $form_type
     */
    public function __construct($form_type = null)
    {
        parent::__construct();

        if ($form_type == self::FORM_TYPE_STYLE)
        {
            $this->action = get_class($this) . '->' . 'update_styles';
        }
        else if ($form_type == self::FORM_TYPE_SETTINGS)
        {
            $this->action = get_class($this) . '->' . 'update_settings';
        }

        $this->__load_styles();
    }

    public function init()
    {
        parent::init();


        $this->ADDM(new OPT\standart_hiddens(), 'modules');
    }

    public function update_styles()
    {
        $idTemplate    = GetPost::get('idTemplate');
        $current_class = GetPost::get('current_class');
        $parent_class  = GetPost::get('parent_class');
        $data          = GetPost::get('data');

        foreach ($data as &$__value)
        {
            $__value = OPT\main_option::array_to_string($__value);
        }

        $template = new \MBCMS\template();
        $template->update_styles($idTemplate, $current_class, $parent_class, $data);
    }

    public function update_settings()
    {
        $idTemplate = GetPost::get('idTemplate');
        $index      = GetPost::get('__cms_output_index');
        $data       = GetPost::get('data', []);


        foreach ($data as &$__value)
        {
            $__value = OPT\main_option::array_to_string($__value, false);
        }

        $template = new \MBCMS\template();

        if (isset($data['__user_cms_class']))
        {
            $data['__user_cms_class'] = isset($data['__user_cms_class']) ? str_replace(['.', ',', ':', ' '], '', $data['__user_cms_class']) : '';
        }

        if ($index)
        {
            $template->update_output_by_id($idTemplate, $index, $data);
        }
        else
        {
            $template->update_settings_by_id($idTemplate, $data);
        }
    }

    public function view()
    {
        $this->set_main_module();
    }

    protected function __load_styles()
    {
        if ($this->__styles)
        {
            return $this->__styles;
        }

        if (\GClass::autoLoad('MBCMS\template'))
        {
            $index = GetPost::uget('__cms_output_index');


            $template = new \MBCMS\template;
            $settings = $template->load_styles(true);

            $this->__settings = isset($settings['CMSData']) ? $settings['CMSData'] : [];

            if ($index && isset($this->__settings['outputs'][$index]))
            {
                $this->__settingsData = $this->__settings['outputs'][$index]['data'];
                $this->__settings     = isset($this->__settings['outputs'][$index]) ? $this->__settings['outputs'][$index] : [];
            }
            else if ($index && !isset($this->__settings['outputs'][$index]))
            {
                $this->__settingsData = [];
                $this->__settings     = [];
            }
            else
            {
                $this->__settingsData = isset($this->__settings['settingsData']) ? $this->__settings['settingsData'] : [];
            }

            return $this->__styles = isset($settings['styles']['options']) ? $settings['styles']['options'] : [];
        }

        return [];
    }

    /**
     * опции вместе с мета данными типа idTemplate, name, position включая settingsData|data для output (пользовательские настройки)
     * 
     * @param $key
     * @return type
     */
    protected function get_setting($key = null)
    {
        if (!$key)
        {
            return $this->__settings;
        }

        return isset($this->__settings[$key]) ? $this->__settings[$key] : null;
    }

    /**
     * Получение пользовательских настроек отдельно от меты
     * 
     * @param $key
     * @return type
     */
    protected function get_settingData($key = null)
    {
        if (!$key)
        {
            return $this->__settingsData;
        }

        return isset($this->__settingsData[$key]) ? $this->__settingsData[$key] : null;
    }

    /**
     * массив стилей данного модуля
     * 
     * @param $key
     * @return type
     */
    protected function get_style($key = null)
    {
        if (!$key)
        {
            return $this->__styles;
        }

        return isset($this->__styles[$key]) ? $this->__styles[$key] : null;
    }

}
