<?php

namespace MBCMS;

class tblock extends \MBCMS\block
{

    public $__cms_static_true = false;

    public function __construct()
    {
        parent::__construct();
        $this->take_alias('<span style="color: red;">Блок стилей</span>');
    }

    public function init()
    {
        $this->fast_edit($this, [
                new Forms\template($this),
                new Forms\size($this),
                new Forms\position($this),
                new Forms\display($this),
                new Forms\border($this),
                new Forms\fixed($this),
                new Forms\flex($this),
                new Forms\deleter($this),
            ]
        );
        parent::init();

        $this->set_identification_attrs($this);
        $this->__user_cms_class = $this->__user_cms_class == '' ? 'this' : $this->__user_cms_class;

        $this->__in_self($this);
    }

    /**
     * Проверяет на вложеность самого себя в себя, для предотвращения вечного цикла
     *
     * @param $trg
     * @return boolean
     */
    private function __in_self($trg)
    {
        $parent = $trg->get_my_parent();

        if ($parent)
        {
            if (isset($this->CMSData['idTemplate']) && isset($parent->CMSData['idTemplate']) && $this->CMSData['idTemplate'] == $parent->CMSData['idTemplate'])
            {
                $parent = $this->get_my_parent();
                if (isset($parent->CMSData['idTemplate']))
                {
                    $pidTemplate = $parent->CMSData['idTemplate'];
                    $pd = $this->get_module_cms_data_by_id($pidTemplate);
                    $childs = isset($pd['childrens']) ? $pd['childrens'] : [];

                    foreach ($childs as $__idTemplate => $children)
                    {
                        if ($this->CMSData['idTemplate'] == $__idTemplate)
                        {
                            \Module::remove_template($pidTemplate, $__idTemplate);
                        }
                    }
                }
                else
                {
                    die('__inself');
                }
            }
            else
            {
                return $this->__in_self($parent);
            }
        }

        return false;
    }

    public function after_init()
    {
        parent::after_init();

        $childs = $this->find_my_childrens();

        $new_childrens = [];
        $new_parents = [];

        foreach ($childs as $child)
        {
            if (isset($child->__user_cms_parent_output_index))
            {
                $new_childrens[$child->__user_cms_parent_output_index][] = $child;
            }

            if (isset($child->__cms_output_index))
            {
                $new_parents[$child->__cms_output_index] = $child;
            }
        }


        foreach ($new_childrens as $index => $childs)
        {
            if (isset($new_parents[$index]))
            {
                $parent = $new_parents[$index];
                foreach ($childs as $child)
                {
                    if ($child->__cms_module_position != 'not_module_position')
                    {
                        $parent->ADDM($child, 'modules');
                    }
                }
            }
        }

        $this->__in_self($this);
    }

    public function preview()
    {
        parent::preview();

        $this->__class = $this->__cms_class . ' ' . str_replace('\\', '__', get_class($this));
        $this->add_attr('__class', 'class');
        $this->__class__ = preg_replace('~.*\\\\(.*)~Usi', '$1', get_class($this));
        $this->add_attr('__class__', 'idtemplate');
        $this->add_attr('__produc_hidden', '__produc_hidden', true);


        if (ModuleCreater::__is_static($this, true))
        {
            $this->__static_attr = 'true';
            $this->add_attr('__static_attr', '__static_view', true);
        }
    }

}
