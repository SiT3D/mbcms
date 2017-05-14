<?php

namespace MBCMS;

use Assets\jQuery;
use event\p404 as p404_event;

class p404 extends \Module
{

    private static $__init = false;
    public $__standart = true;
    private $__error_code = 404;


    public static function error404($code = 404)
    {
        if (!routes::is_static_status())
        {
            $p404 = new \MBCMS\p404();
            $p404->setErrorCode($code);
            if ($p404->__standart)
            {
                $p404->set_main_module();
            }

            return $p404;
        }

        return block::factory('');
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new jQuery(),
        ];
    }

    public function setErrorCode($code)
    {
        $this->__error_code = $code;
        return $this;
    }

    public function init()
    {
        parent::init();


        if (!self::$__init)
        {
            self::$__init = true;

            $evt = (new p404_event())->call();

            if (!$this->__cms_module_position == self::NO_POSITION )
            {
                routes::redirect('', $this->__error_code);
            }


            if ($evt->trg)
            {
                $this->__standart = false;
                $new = new $evt->trg;
                $this->ADDM($new, 'modules');
                $this->not_files();
            }
        }
    }


}
