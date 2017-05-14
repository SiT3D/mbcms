<?php

namespace MBCMS\form;

class select extends \Module
{

    public $name;
    public $values;
    public $multiple;
    public $class;
    public $title;

    /**
     *
     * @var bool= true
     */
    public $chosen = true;
    /**
     *
     * @var bool = false
     */
    public $with_empty_option = false;
    public $eoption = ['value' => 0, 'title' => 'none'];
    /**
     *
     * @var array [ <br>
     *  [ value, title] <br>
     * ]<br>
     *
     * добавить поддержку <optgroup label="...">
     */
    public $options = [];

    /**
     * select constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct();
        $this->name = $name;
    }

    public static function factory($name)
    {
        return new select($name);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $values
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param mixed $multiple
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param bool $chosen
     */
    public function setChosen($chosen)
    {
        $this->chosen = $chosen;
        return $this;
    }

    /**
     * @param bool $with_empty_option
     */
    public function setWithEmptyOption($with_empty_option = true)
    {
        $this->with_empty_option = $with_empty_option;
        return $this;
    }

    /**
     * @param array $options [ ['value' => '', 'title' => ''] ]
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

}
