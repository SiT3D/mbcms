<?php

namespace Kontrolio\Rules\Core;

use Kontrolio\Rules\AbstractRule;
use MBCMS\DB;

class QueryHave extends AbstractRule
{

    private $__query = null;

    function __construct(DB $query)
    {
        $this->__query = $query;
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
        if ($this->__query->is_mono()->limit(1)->get())
        {
            return false;
        }

        return true;
    }
}