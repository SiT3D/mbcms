<?php

namespace MBCMS;

class block extends \Module
{

    /**
     *
     * @var type div;li;ul; etc or empty
     */
    public $__cms_block_type = 'div';

    /**
     *
     * @var string css class {.content}
     */
    public $__user_cms_class;
    public $__cms_class;
    public $__user_cms_out_title;
    public $__text, $__cms_id;
    public $__user_cms_block_type;
    public $__user_cms_dop_attrs = '';
    public $__user_cms_dop_css_classes;
    public $__user_cms_src       = '';

    /**
     * block constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->take_alias('Блок');
        $this->__cms_closing_type = true;
    }

    public function init_files()
    {
        return [
            new mbcms_assets(),
            parent::init_files()
        ];
    }

    /**
     * @param string $text
     * @param string $tag
     * @param string $class
     * @return block
     */
    public static function factory($text = '', $tag = '', $class = '')
    {
        $ret = new block();
        $ret->__text = $text;
        $ret->__cms_block_type = $tag;
        $ret->__user_cms_class = $class;
        return $ret;
    }

    public static function factoryLink($text, $href)
    {
        $ret = new block();
        $ret->__text = $text;
        $ret->__href_block = $href;
        $ret->add_attr('__href_block', 'href');
        $ret->__cms_block_type = 'a';
        return $ret;
    }

    public function init()
    {
        parent::init();

        if (trim($this->__cms_block_type) == '')
        {
            $this->__cms_block_type = 'div';
        }

        $this->add_attr('__user_cms_out_title', '__user_cms_out_title', true);
        $this->add_attr('__user_cms_dop_css_classes', '__user_cms_dop_css_classes', true);

        $this->__cms_closing_type = $this->isClosingTag();
    }

    private function isClosingTag()
    {
        $opening = ['input', 'br', 'hr', 'img'];

        foreach ($opening as $tag)
        {
            if (trim($this->__cms_block_type) === $tag)
            {
                return false;
            }
        }

        return true;
    }

    public function preview()
    {
        parent::preview();

        $this->add_attr('__user_cms_src', 'src');

        $this->add_attr('__cms_connect_type', 'connect_type', true);
        $this->add_attr('__cms_id', 'id');
        $this->__cms_block_type = $this->__user_cms_block_type ? $this->__user_cms_block_type : $this->__cms_block_type;

        if (is_string($this->__user_cms_dop_attrs) && $this->__user_cms_dop_attrs != '')
        {
            $this->__user_cms_dop_attrs = json_decode($this->__user_cms_dop_attrs);
            foreach ($this->__user_cms_dop_attrs as $__attr => $__attr_value)
            {
                if ($__attr_value)
                {
                    $__property_name        = '__user_cms_' . $__attr;
                    $this->$__property_name = $__attr_value;
                    $this->add_attr($__property_name, $__attr);
                }
            }
        }

        $this->__cms_class = $this->__user_cms_class . ' ' . $this->__user_cms_dop_css_classes;
        $this->add_attr('__cms_class', 'class');
        $this->add_attr('__user_cms_class', 'css_class', true);

        foreach ($this->__cms_module_tags as $tag)
        {
            $this->$tag = '+';
            $this->add_attr($tag, $tag, true);
        }
    }

    function add_mbcms_class($class = '')
    {
        $this->__cms_class .= ' ' . $class;
    }

    function remove_mbcms_class($class = '')
    {
        $this->__cms_class      = str_replace($class, '', $this->__cms_class);
        $this->__user_cms_class = str_replace($class, '', $this->__user_cms_class);
    }

}
