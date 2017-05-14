<?php

namespace MBCMS;

class ie extends \Module
{

    public $arr = array();

    function init()
    {
        if (is_array($this->arr))
        {
            $arr = $this->arr;
            foreach ($arr as $key => $ar)
            {
                if (!isset($ar['name']))
                {
                    $nextElement = new ie();
                    $nextElement->name = $key;
                    $nextElement->class = ' cont-fold';
                    $nextElement->arr = $ar;
                }
                else
                {
                    $nextElement = new li();
                    $nextElement->name = $ar['name'];
                    $nextElement->alias = isset($ar['alias']) && !empty($ar['alias']) ? $ar['alias'] . ' - ' : '';
                }

                $this->ADDM($nextElement, 'modules');
            }
        }
    }

}
