<?php

namespace Kontrolio\Rules\Core;

use Kontrolio\Rules\AbstractRule;
use MBCMS\DB;

class NotEmptyArray extends AbstractRule
{

    /**
     * Validates input.
     *
     * @param mixed $query
     *
     * @return bool
     */
    public function isValid($input = null)
    {
        if (is_array($input) && count($input))
        {
            return true;
        }

        return false;
    }
}