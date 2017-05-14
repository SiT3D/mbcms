<?php

namespace MBCMS\Forms;

class flex extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_STYLE);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico margin_4_arrows KEY_SHIFT KEY_F', $this, 'view');
        }

        $this->form_method = 'POST';
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

        $opt               = new OPT\main_option($this->get_style('display'), 'display');
        $opt->metrix       = ['block', 'inline-block', 'none', 'destroy', 'inherit', 'inline', 'flex'];
        $opt->hide_value   = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Направление');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('flex_direction'), 'flex_direction');
        $opt->metrix       = ['row', 'row-reverse', 'column', 'column-reverse', 'destroy'];
        $opt->hide_value   = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Перенос');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('flex_wrap'), 'flex_wrap');
        $opt->metrix       = ['nowrap', 'wrap', 'wrap-reverse', 'destroy'];
        $opt->hide_value   = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Заполнение или позиция');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('justify_content'), 'justify_content');
        $opt->metrix       = ['flex-start', 'flex-end', 'center', 'space-between', 'space-around', 'destroy'];
        $opt->hide_value   = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Выравнивание');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('align_items'), 'align_items');
        $opt->metrix       = ['flex-start', 'flex-end', 'center', 'baseline', 'stretch', 'destroy'];
        $opt->hide_value   = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Выравнивание всего контента (перпендикулярная ось)');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('align_content'), 'align_content');
        $opt->metrix       = ['flex-start', 'flex-end', 'center', 'space-between', 'space-around', 'stretch', 'destroy'];
        $opt->hide_value   = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Порядковый приоритет (для дочернего элемента)');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('order'), 'order');
        $opt->hide_metric  = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Пропорция (для дочернего элемента)');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('flex_grow'), 'flex_grow');
        $opt->hide_metric  = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Максимальный размер (для дочернего элемента)');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('flex_basis'), 'flex_basis');
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new OPT\title('Личное выравнивание (для дочернего элемента)');
        $this->ADDM($opt, 'modules');

        $opt               = new OPT\main_option($this->get_style('align_self'), 'align_self');
        $opt->metrix       = ['flex-start', 'flex-end', 'center', 'baseline', 'stretch', 'auto', 'destroy'];
        $opt->hide_value   = true;
        $opt->metric_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');
    }

}
