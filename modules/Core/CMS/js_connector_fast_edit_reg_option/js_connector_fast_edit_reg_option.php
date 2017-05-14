<?php

namespace MBCMS;

/**
 * Подключение JS кода, например коннект опций для аута
 */
class js_connector_fast_edit_reg_option extends \Module
{

    private static $__names = [];

    /**
     * 
     * @param  $css_class
     * @param  $module
     * @param  $method
     */
    public function __construct($css_class, $module, $method)
    {
        parent::__construct();
        $this->css_class       = $css_class;
        $this->register_module = $module;
        $this->method          = $method;
    }

    public function init()
    {
        parent::init();

        $this->add_tag(self::MODULE_TAG_TECH);
        $this->className = get_class($this->register_module);
        $this->className = str_replace('\\', '\\\\', $this->className);
        
        if (!routes::is_admin() || isset(self::$__names[$this->className]))
        {
            $this->not_render();
        }
        
        self::$__names[$this->className] = true; // выводить только 1 раз от каждого аута.
    }

}
