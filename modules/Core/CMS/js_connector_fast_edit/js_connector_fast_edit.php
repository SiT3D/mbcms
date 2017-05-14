<?php

namespace MBCMS;

/**
 * Подключение JS кода, например коннект опций для аута
 */
class js_connector_fast_edit extends \Module
{

    /**
     * 
     * @param  $module
     * @param  $function_text
     */
    public function __construct($module, $function_text)
    {
        parent::__construct();
        $this->register_module = $module;
        $this->function_text   = $function_text;
    }

    public function init()
    {
        parent::init();

        $this->add_tag(self::MODULE_TAG_TECH);
        $this->className = get_class($this->register_module);
        $this->className = str_replace('\\', '\\\\', $this->className);


        if (!routes::is_admin())
        {
            $this->not_render();
        }
    }

}
