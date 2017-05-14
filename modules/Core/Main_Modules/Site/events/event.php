<?php

namespace event;

defined('HOME_PATH') or die('No direct script access.');

/**
 * Основной класс - событие для наследования!
 */
class event
{

    protected static $__listners = [];

    public static function factory(event $event)
    {
        return $event;
    }

    /**
     * @return $this
     */
    public function call()
    {
        $class_name                    = get_class($this);
        self::$__listners[$class_name] = isset(self::$__listners[$class_name]) ? self::$__listners[$class_name] : [];

        foreach (self::$__listners[$class_name] as $function)
        {
            if (is_callable($function))
            {
                call_user_func_array($function, [$this]);
            }
        }
        
        return $this;
    }

    /**
     *
     * @param $callback = function($event) $event = $this of event object
     * @return $this
     */
    public function listen($callback)
    {
        $class_name                    = get_class($this);
        self::$__listners[$class_name] = isset(self::$__listners[$class_name]) ? self::$__listners[$class_name] : [];
        array_push(self::$__listners[$class_name], $callback);
        return $this;
    }

}
