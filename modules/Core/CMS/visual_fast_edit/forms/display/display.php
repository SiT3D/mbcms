<?php

namespace MBCMS\Forms;

use MBCMS\Forms\OPT\main_option;

class display extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_STYLE);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico display_icos KEY_D', $this, 'view');
        }

        $this->form_method = 'GET';
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new OPT\title,
            new OPT\main_option(null, null),
        ];
    }

    public function init()
    {
        parent::init();

        $opt = new OPT\title('Тип блока');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('display'), 'display');
        $opt->metrix = ['block', 'inline-block', 'none', 'destroy', 'inherit', 'inline', 'flex'];
        $opt->hide_value = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Вертикальное выравнивание');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('vertical_align'), 'vertical_align');
        $opt->metrix = ['middle', 'top', 'bottom', 'none', 'inherit'];
        $opt->hide_value = true;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Прилипание');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('float'), 'float');
        $opt->metrix = ['none', 'right', 'left'];
        $opt->hide_value = true;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Слой');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('z_index'), 'z_index');
        $opt->hide_metric = true;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Прозрачность');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('opacity'), 'opacity');
        $opt->hide_metric = true;
        $opt->step = 0.05;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Скрытие содержимого');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('overflow'), 'overflow');
        $opt->hide_value = true;
        $opt->metrix = ['auto', 'hidden', 'scroll', 'visible', 'no-content', 'no-display'];
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title(' Цвет фона');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('background_color'), 'background_color');
        $opt->hide_metric = true;
        $opt->setColorPIcker();
        $opt->type = OPT\main_option::TYPE_TEXT;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Тень');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('box_shadow'), 'box_shadow');
        $opt->hide_metric = true;
        $opt->type = OPT\main_option::TYPE_AREA;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Изображение фона: url(тут адрес изображения)');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_style('background_image'), 'background_image');
        $opt->setPlaceholder('пример: url(http://cms.trud.kr.ua/images/f4/2b/1d/65/72/ce/fa2e35323334dc334bd6.jpeg)');
        $opt->hide_metric = true;
        $opt->type = OPT\main_option::TYPE_TEXT;
        $opt->value_width = main_option::MW_LARGEXX;
        $this->ADDM($opt, 'modules');
    }

}
