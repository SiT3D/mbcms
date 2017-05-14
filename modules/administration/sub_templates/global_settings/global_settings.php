<?php


use MBCMS\form\form;
use MBCMS\form\input;

class global_settings extends \Module implements \adminAjax
{
    private static $data = null;
    private        $form;
    private        $__data;

    public static function getData($key = null)
    {
        $settings = new self();

        if ($key)
        {
            $data = $settings->__get_data();

            return isset($data->{$key}) ? $data->{$key} : null;
        }
        else
        {
            return $settings->__get_data();
        }
    }

    private function __get_data()
    {
        self::$data = self::$data ? self::$data : $this->__read_json();

        return self::$data;
    }

    private function __read_json()
    {
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'data.json'))
        {
            return json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data.json'));
        }

        return [];
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new input(0),
        ];
    }

    /**
     * тут расставляем формы с настройками.
     */
    public function init()
    {
        parent::init();

        $this->__data = $this->__read_json();

        $this->form = form::factory(__CLASS__ . '->save', 'global_settings_form');
        $this->ADDM($this->form, 'modules');

        $this->__add_settings_option_string('zagga', 'Заголовок этой страницы');

        $this->form->ADDM(input::factory('sub', 'Сохранить')->setType(input::TYPE_SUBMIT), 'modules');
    }

    private function __add_settings_option_string($key, $title)
    {
        $this->form->ADDM(input::factory("data[{$key}]", isset($this->__data->{$key}) ? $this->__data->{$key} : '')->setTitle($title)->setClass('trud-input mini'), 'modules');
    }

    public function save()
    {
        $data = \GetPost::uget('data', []);
        $this->__save_json($data);
    }

    private function __save_json($data)
    {
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data.json', json_encode($data));
    }
}