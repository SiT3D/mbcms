<?php

namespace MBCMS;

class autocreater extends out implements \adminAjax
{

    private $__outputs = [];
    public $HTML       = '';
    public $URL        = '';

    public function __construct()
    {
        parent::__construct();
    }

    function init()
    {
        $this->fast_edit($this, [
            new \MBCMS\Forms\autocreater_options($this),
            new \MBCMS\Forms\deleter($this),
        ]);

        parent::init();

        $this->__generate_template();
    }

    private function __generate_template()
    {
        $parent = $this->get_my_parent();


        if (isset($parent->CMSData['idTemplate']) && $parent->CMSData['idTemplate'])
        {
            $this->__get_tag();
        }
    }

    ///////////////////////////////////////////////////////////////////////
    // действия

    private function __get_tag()
    {
        $parent = $this->get_my_parent();

        if (!$this->HTML)
        {
            return;
        }

        $document = \phpQuery::newDocument($this->HTML);
        $result   = $document->find('*');

        $this->__outputs = [];

        foreach ($result as $tag)
        {
            if ($tag->parentNode->tagName == 'body') // тут внешние без родителя
            {
                $this->__create_new_output($tag);
            }
        }

        $d       = self::get_module_cms_data_by_id($parent->CMSData['idTemplate']);
        $outputs = $d['outputs'];

        foreach ($this->__outputs as $__out)
        {
            $index           = $__out['data']['__cms_output_index'];
            $outputs[$index] = $__out;
        }

        self::save_outputs_by_id($parent->CMSData['idTemplate'], $outputs);

        self::remove_output($parent->CMSData['idTemplate'], $this->__cms_output_index);
    }

    private function __create_new_output($tag)
    {
        if (get_class($tag) == 'DOMElement')
        {
            $output                               = [];
            $output['data']                       = $this->__get_module_settings($tag);
            $output['name']                       = utf8_encode($this->__get_output_class());
            $output['position']                   = 'modules';
            $index                                = \Module::__generate_random_id();
            $output['data']['__cms_output_index'] = $index;

            $this->__outputs[$index] = $output;

            $this->__create_childrens($tag, $index);
        }
    }

    private function __create_childrens($tag, $index)
    {
        if (isset($tag->childNodes))
        {
            foreach ($tag->childNodes as $child)
            {
                $child->__user_cms_parent_output_index = $index;
                $this->__create_new_output($child);
            }
        }
    }

    private function __get_module_settings($tag)
    {
        $data = [];

        $data['__text'] = $this->__get_tag_text($tag);

        if (isset($tag->__user_cms_parent_output_index))
        {
            $data['__user_cms_parent_output_index'] = $tag->__user_cms_parent_output_index;
        }

        if (isset($tag->attributes))
        {
            foreach ($tag->attributes as $attr)
            {
                $data['__user_cms_dop_attrs'][$attr->name] = $attr->value;
            }

            $data['__user_cms_dop_attrs'] = isset($data['__user_cms_dop_attrs']) ? json_encode($data['__user_cms_dop_attrs']) : '';
        }

        $data['__user_cms_class']      = isset($data['__user_cms_dop_attrs']['class']) ? $data['__user_cms_dop_attrs']['class'] : 'block-' . $tag->tagName;
        $data['__user_cms_block_type'] = $tag->tagName;

        return $data;
    }

    private function __get_tag_text($tag)
    {
        $text = '';

        if (isset($tag->childNodes))
        {
            foreach ($tag->childNodes as $texto)
            {
                if (get_class($texto) == 'DOMText')
                {
                    $text .= $texto->wholeText;
                }
            }
        }

        return $text;
    }

    private function __get_output_class()
    {
        return get_class(new \MBCMS\out);
    }

//    private function __create_css()
//    {
//        $d         = self::get_module_cms_data_by_id($this->idTemplate);
//        $className = $d['name'];
//
//        if (\GClass::autoLoad($className))
//        {
//            $html  = file_get_contents($this->__user_cms_page_url);
//            $match = [];
//            preg_match('~(http://.*)/|(http://.*)$~Usi', $this->__user_cms_page_url, $match); // https
//            $domen = isset($match[1]) ? $match[1] : '';
//            $domen = isset($match[2]) && !$domen ? $match[2] : $domen;
//
//            $result = [];
//            preg_match_all('~href=\"([^"]*\.css)~Usi', $html, $result);
//            $css    = '';
//
//            if (isset($result[1]))
//            {
//                foreach ($result[1] as $path)
//                {
//                    $css .= @file_get_contents($domen . $path); // нужно подставлять домен если указан локальный путь /css/cs.css
//                }
//            }
//
//            file_put_contents(\GClass::$classInfo['folder'] . '/css/dop.css', $css);
//        }
//    }

}
