<?php

namespace Kontrolio\Rules\Core;

use Kontrolio\Rules\AbstractRule;

class IsNumber extends AbstractRule
{
    private $__empty;

    function __construct($empty = false)
    {
        $this->__empty = (boolean) $empty;
    }

    /**
     * Validates input.
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function isValid($input = null)
    {
        if ($this->__empty && !$input)
        {
            return true;
        }

        if (preg_match('~^\-?\d+(\.\d{0,})?$~', $input))
        {
            return true;
        }

        return false;
    }
}