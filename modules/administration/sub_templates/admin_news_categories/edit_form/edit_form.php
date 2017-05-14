<?php

namespace trud\admin\templates\admin_news_categories;

use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\form\select;
use MBCMS\routes;
use trud\classes\model\news_categories;

class edit_form extends \Module implements \adminAjax
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new form(),
            new input(1),

        ];
    }

    public function init()
    {
        parent::init();

        $id = \GetPost::uget('id');

        $current_category = (new news_categories())->get_by_id($id)->get();

        $this->method = $id ? 'edit' : 'add';

        $form = form::factory(__CLASS__ . '->' . $this->method, 'admin_news_categories_form');
        $this->ADDM($form, 'modules');

        $form->ADDM(input::factory('category_id', $id, input::TYPE_HIDDEN), 'modules');

        $form->ADDM(input::factory('name', isset($current_category->name) ? $current_category->name : '')->setTitle('Название категории'), 'modules');

        $categories = (new news_categories)->get_all()->w('id != ?', $id)->w('parent_id = 0')->get();

        $opt = select::factory('parent_id');
        $options = [];
        $opt->setTitle('Родительская категория');
        $opt->setWithEmptyOption();
        foreach ($categories as $category)
        {
            $options[] = ['value' => $category->id, 'title' => $category->name];
        }
        $opt->setOptions($options);
        $opt->setValues(isset($current_category->parent_id) ? $current_category->parent_id : '');
        $form->ADDM($opt, 'modules');

        $form->ADDM(input::factory('visible')
            ->setType(input::TYPE_CHECKBOX)
            ->setSelectedIndex(isset($current_category->visible) ? $current_category->visible : '1')
            ->setTitle('Видимость в меню на сайте')
            , 'modules');

        $form->ADDM(input::factory('sub', null, input::TYPE_SUBMIT), 'modules');

        $remove_form = form::factory(__CLASS__ . '->remove', 'admin_news_categories_remove_form');
        $this->ADDM($remove_form, 'modules');

        $remove_form->ADDM(input::factory('id', \GetPost::uget('id'), input::TYPE_HIDDEN), 'modules');
        $remove_form->ADDM(input::factory('sub', 'Удалить', input::TYPE_SUBMIT), 'modules');
    }

    public function edit()
    {
        $values = $this->__get_data();
        (new news_categories())->update($values, \GetPost::uget('category_id'));
        self::response();
    }

    private function __get_data()
    {
        return \GetPost::ar(['name', 'visible', 'parent_id']);
    }

    public function add()
    {
        $values = $this->__get_data();
        (new news_categories())->add($values['name'], $values);
        form::redirectJS(routes::link('admin_news_categories'));
        self::response();
    }

    public function remove()
    {
        $id = \GetPost::uget('id');
        (new news_categories())->delete($id);
        form::redirectJS(routes::link('admin_news_categories'));
        self::response();
    }
}