<?php

namespace MBCMS\Forms\OPT;


class main_option extends \Module
{

    const MW_MINI     = 'input-mini';
    const MW_SMALL    = 'input-small';
    const MW_NORMAL   = 'input-medium';
    const MW_LARGE    = 'input-large';
    const MW_LARGEX   = 'input-xlarge';
    const MW_LARGEXX  = 'input-xxlarge';
    const TYPE_TEXT   = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_AREA   = 'textarea';

    public $metrix       = ['px', '%', 'auto', 'destroy'];
    public $value;
    public $hide_value   = false;
    public $hide_metric  = false;
    public $metric_width = 'input-small';
    public $value_width  = 'input-mini';
    public $step         = 1;
    public $type         = self::TYPE_NUMBER;
    public $readonly     = false;
    public $checked      = false;
    /**
     * @var string css class dop
     */
    public $dop_classes_value  = '';
    public $dop_classes_metric = 'hide_metric';
    public $multiple_metrix    = false;

    /**
     *
     * @var type массив вида data[min_width][0][value] для передачи в ????! или просто min_width не ясно еще
     * ну короче нейм для формы, для сохранения
     */
    public $name;

    /**
     *
     * @param $value
     * @param $name
     */
    public function __construct($value, $name)
    {
        parent::__construct();

        $this->value    = $value;
        $this->name     = $name;
        $this->__cn     = null;
        $this->ckeditor = null;
    }

    /**
     *
     * @param $value_array
     * @param $is_styles
     * @return string
     */
    public static function array_to_string($value_array, $is_styles = true)
    {
        $exps = ['px', 'em', '%'];

        if (!isset($value_array['metrica']) && !isset($value_array['value']))
        {
            return is_string($value_array) ? $value_array : '';
        }

        if (isset($value_array['metrica']) && ($value_array['metrica'] == 'auto' || $value_array['metrica'] == 'destroy' || !isset($value_array['value'])))
        {
            return $value_array['metrica'];
        }
        else if (isset($value_array['value']) && isset($value_array['metrica']) && $value_array['value'] !== '' && $value_array['value'] !== null
            && $value_array['metrica'] && preg_match('~\d~', $value_array['value'])
        )
        {
            return $value_array['value'] . $value_array['metrica'];
        }
        else if (isset($value_array['value']) && $value_array['value'] !== '' && $value_array['value'] !== null)
        {
            return $value_array['value'];
        }
        else if (isset($value_array['metrica']) && $value_array['metrica'] && !in_array($value_array['metrica'], $exps))
        {
            return $value_array['metrica'];
        }

        if ($is_styles)
        {
            return 'destroy';
        }

        return '';
    }

    public function init()
    {
        parent::init();

        $this->string_to_format_metric();

        $this->__value_mousedown = $this->type == self::TYPE_AREA || $this->type == self::TYPE_TEXT ? '__mousedown' : '';

        $this->checked  = $this->value == 'true' ? $this->checked = true : $this->checked = false;
        $this->readonly = $this->readonly ? 'readonly' : '';
    }

    /**
     *
     * @param $string типа 10px или 25% или auto должно преобразовать в массив типа
     * $data[$css][$index]['value'] = 10; $data[$css][$index]['metrica'] = 'px';
     */
    private function string_to_format_metric()
    {
        $mathes = [];

        if ($this->hide_metric)
        {
            return;
        }
        else if ($this->hide_value)
        {
            $this->metrica = $this->value;

            return;
        }

        preg_match('~[^\-^\d]+[a-zA-Z\-]+~', $this->value, $mathes);
        $this->metrica = isset($mathes[0]) ? $mathes[0] : '';
        preg_match('~^\-?[\d\.]+~', $this->value, $mathes);
        $this->value = isset($mathes[0]) ? $mathes[0] : '';


        if (!in_array($this->metrica, $this->metrix))
        {
            $this->value = $this->metrica;
        }
    }

    public function setCKEditor($type = 'superadmin')
    {
        $this->ckeditor = true;
        $this->__cn     = $type;

        return $this;
    }

    /**
     * @return $this
     */
    public function setColorPIcker()
    {
        $this->colorpicker = true;

        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setPlaceholder($string)
    {
        $this->placeholder = strip_tags($string);

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setType($value)
    {
        $this->type = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function hideMetric()
    {
        $this->hide_metric = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function hideValue()
    {
        $this->hide_value = true;

        return $this;
    }

    public function setStep($value)
    {
        $this->step = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValueWidth($value)
    {
        $this->value_width = $value;

        return $this;
    }

    /**
     * @param array $metrix
     */
    public function setMetrix($metrix)
    {
        $this->metrix = $metrix;

        return $this;
    }
}
