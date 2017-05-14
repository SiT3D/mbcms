<?php

namespace MBCMS\form;


/**
 * формочка имеет js события event.form.{}
 */
class form extends \Module
{

    const ORDER_HORIZONTAL = 'horizont';
    const ORDER_VERTICAL = 'vertical';
    /**
     * @var string
     */
    public $method = 'POST';
    /**
     * Произойдет стандартное срабатывание формы
     *
     * @var bool
     */
    public $not_ajax_send = false;
    public $input_order = '';
    /**
     * @var string
     */
    public $action_class;
    /**
     * @var string
     */
    public $action = '';
    /**
     * @var string
     */
    public $form_id;

    /**
     * @param string $action_class
     * @param string $form_id
     * @return form
     */
    public static function factory($action_class, $form_id = 'form')
    {
        $ret = new form();
        $ret->action_class = $action_class;
        $ret->input_order = self::ORDER_VERTICAL;
        $ret->form_id = $form_id;
        return $ret;
    }

    /**
     * @param array $errors ['name' => [0=>'error title', '1' => 'error title']] || [['errtext'], ['errtext']]
     */
    public static function errors($errors)
    {
        self::add_response('errors', $errors);
    }

    /**
     * Сформирует ответ для перехода на стороне js по this.redirect() в событии sucess формы
     * @param string $addres
     */
    public static function redirectJS($addres)
    {
        self::add_response('__redirect', $addres);
    }

    public function init_files()
    {
        return [
            parent::init_files(),
        ];
    }

    public function init()
    {
        parent::init();

        $this->add_attr('action', 'action');

    }

    public function after_init()
    {
        parent::after_init();
        $this->__set_ajax_class();
        $this->add_attr('input_order', 'order');
        $this->add_attr('form_id', 'id');

    }

    private function __set_ajax_class()
    {
        if ($this->action_class)
        {
            $hidden_class = new input('class', $this->action_class, input::TYPE_HIDDEN);
            $this->ADDM($hidden_class, 'modules');
        }
    }

    public function setDisplay($value)
    {
        $this->display = $value;
        return $this;
    }

    /**
     * @param string $method GET|POST
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }


}
