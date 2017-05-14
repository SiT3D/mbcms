<?php

namespace Kontrolio\Rules\Core;

use Kontrolio\Rules\AbstractRule;
use MBCMS\DB;

class Password extends AbstractRule
{

    private $__pass1;
    private $__pass2;

    function __construct($password, $confirm_password)
    {
        $this->__pass1 = $password;
        $this->__pass2 = $confirm_password;
    }


    /**
     * Validates input.
     *
     * @param mixed $query
     *
     * @return bool
     */
    public function isValid($input = null)
    {
        if ($this->__pass1 == $this->__pass2 && trim($this->__pass1))
        {
            return true;
        }

        return false;
    }
}