<?php

namespace trud\admin\templates;

use MBCMS\block;
use MBCMS\form\form;
use MBCMS\form\input;

class filter_form extends form
{

    public static function factory($action_class, $form_id = 'form')
    {
        $ret = new filter_form();
        $ret->action_class = $action_class;
        $ret->input_order = self::ORDER_VERTICAL;
        $ret->form_id = $form_id;
        $ret->add_attr('form_id', 'id');
        return $ret;
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new form(),
            new block(),
            new input(0),

        ];
    }

    public function init()
    {
        parent::init();

        $this->method = 'GET';
        $this->not_ajax_send = true;
        $this->input_order = filter_form::ORDER_HORIZONTAL;

    }

    public function after_init()
    {
        parent::after_init();

        $this->ADDM(input::factory('sub', 'Фильтр', input::TYPE_SUBMIT), 'modules');

        $button = new block;
        $button->__text = 'Сбросить фильтры';
        $button->__cms_block_type = 'button';
        $button->__user_cms_class = 'filter-form-clear';
        $this->ADDM($button, 'modules');
    }
}