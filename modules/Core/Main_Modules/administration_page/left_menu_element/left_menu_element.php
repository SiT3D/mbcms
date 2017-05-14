<?php

namespace MBCMS\administration_page;

class left_menu_element extends \Module
{

    public $active = false;

    /**
     * 
     * @param $items [['href', 'title', 'active:bool'], []]
     * @return \MBCMS\administration_page\left_menu_element
     */
    public static function factory($items, $active = false)
    {
        $ret          = new left_menu_element();
        $ret->__items = $items;
        $ret->active  = $active ? '' : 'none';
        return $ret;
    }

    public function init()
    {
        parent::init();

        $url = urldecode($_SERVER['REQUEST_URI']);

        foreach ($this->__items as &$item)
        {
            if (preg_match("~$item[0]~", $url))
            {
                $item[2] = true;
                $this->active = true;
                break;
            }
        }
    }

}
