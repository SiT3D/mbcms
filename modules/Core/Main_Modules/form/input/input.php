<?php

namespace MBCMS\form;

use Plugins\choosen_select;

class input extends \Module
{

    public $title;
    public $type;

    /**
     * css classes
     * @var string
     */
    public $class;
    public $name;
    public $value;
    public $multiple;
    public $placeholder;
    public $readonly;

    /**
     * Индекс который нужно найти при checkbox
     * тоесть при редактирование полученные данные передавать сюда, для checkbox и radio эементов
     * value в таком случае служит значением по умолчанию!!!
     * 
     * @var integer
     */
    public $selected_index = -1;

    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_TEXT     = 'text';
    const TYPE_NUMBER   = 'number';
    const TYPE_FILE     = 'file';
    const TYPE_RADIO    = 'radio';
    const TYPE_SUBMIT   = 'submit';
    const TYPE_EMAIL    = 'email';
    const TYPE_PASSWORD = 'password';
    const TYPE_HIDDEN   = 'hidden';

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @param input::TYPE_ $type
     */
    public function __construct($name, $value = null, $type = null)
    {
        parent::__construct();

        $this->name  = $name;
        $this->value = $value;
        $this->type  = $type;
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @param input ::TYPE_ $type
     * @return input
     */
    public static function factory($name, $value = null, $type = null)
    {
        return new input($name, $value, $type);
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new choosen_select(),
        ];
    }

    public function init()
    {
        parent::init();

        if (($this->type == self::TYPE_RADIO || $this->type == self::TYPE_CHECKBOX) && !$this->value)
        {
            $this->value = 1;
        }

        if ($this->type == self::TYPE_SUBMIT && !$this->value)
        {
            $this->value = 'Сохранить';
        }

        if ($this->type == self::TYPE_SUBMIT && !$this->class)
        {
            $this->setClass('trud-btn mini');
        }
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param null $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed|null $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param mixed $multiple
     * @return $this
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @param mixed $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @param mixed $readonly
     * @return $this
     */
    public function setReadonly($readonly)
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * Используется вместо values для чекбокса и радио!
     * @param int $selected_index
     */
    public function setSelectedIndex($selected_index)
    {
        $this->selected_index = $selected_index;
        return $this;
    }

}
