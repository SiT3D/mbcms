<?php

namespace trud\admin\templates\news_list;

use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\form\select;
use MBCMS\image_galary;
use MBCMS\image_galary\upload_form;
use MBCMS\image_galary\viewer;
use MBCMS\routes;
use trud\classes\model\news;
use trud\classes\model\news_categories;
use trud\form_element\text_area;
use trud\site_metrics;

class edit_form extends \Module implements \adminAjax
{
    public function init_files()
    {

        return [
            new text_area(),
            parent::init_files(),
            new form(),
            new input(1),
            new select(1),
            new viewer(),
            new upload_form(),
        ];
    }

    public function init()
    {
        parent::init();

        $id = \GetPost::uget('id');

        $news = (new news())->get_by_id($id)->get();

        $this->method = $id ? 'edit' : 'add';

        $form = form::factory(__CLASS__ . '->' . $this->method, 'admin_news_form');
        $this->ADDM($form, 'modules');

        $form->ADDM(input::factory('news_id', $id, input::TYPE_HIDDEN), 'modules');

        $form->ADDM(input::factory('title', isset($news->title) ? $news->title : '')->setTitle('Заголовок'), 'modules');

        $opt = new text_area('small_description', isset($news->small_description) ? $news->small_description : '');
        $opt->title = 'Описание превью';
        $form->ADDM($opt, 'modules');

        $opt = new text_area('description', isset($news->description) ? $news->description : '');
        $opt->title = 'Содержание статьи';
        $opt->setCKEditor(text_area::CKEDITOR_TYPE_ADMIN);
        $form->ADDM($opt, 'modules');

        $opt = new text_area('meta', isset($news->meta) ? $news->meta : '');
        $opt->title = 'Метаданные для поисковых систем';
        $form->ADDM($opt, 'modules');

        $categories = (new news_categories)->get_all()->get();

        $opt = select::factory('category_id');
        $options = [];
        $opt->setTitle('Категория');
        foreach ($categories as $category)
        {
            $options[] = ['value' => $category->id, 'title' => $category->name];
        }
        $opt->setOptions($options);
        $opt->setValues(isset($news->category_id) ? $news->category_id : '');
        $form->ADDM($opt, 'modules');


        $form->ADDM(input::factory('sub', 'Сохранить', input::TYPE_SUBMIT), 'modules');

        $remove_form = form::factory(__CLASS__ . '->remove', 'admin_news_remove_form');
        $this->ADDM($remove_form, 'modules');

        $remove_form->ADDM(input::factory('id', \GetPost::uget('id'), input::TYPE_HIDDEN), 'modules');
        $remove_form->ADDM(input::factory('sub', 'Удалить', input::TYPE_SUBMIT), 'modules');

        if ($id)
        {
            $this->__image_uploader($id);
        }
    }

    private function __image_uploader($news_id)
    {
        $tags = image_galary::factory()->find_by_tags(['news_id' => $news_id])->is_mono()->limit(1)->get();

        if ($tags)
        {
            $this->ADDM((new viewer())->setSrc($tags->image_id)->setSize('200px', 'auto')->setGalaryAdminHref(['ID статьи ' . $news_id]), '$image');
        }

        $image_uploader = form::factory('', 'image_uplouder');
        $this->ADDM($image_uploader, '$image');

        $image_uploader->ADDM(input::factory('news_id', $news_id, input::TYPE_HIDDEN), 'modules');
        $image_uploader->ADDM((new upload_form())->setMultiple(false), 'modules');
    }

    public function edit()
    {
        $values = $this->__get_data();
        (new news)->update($values, $values['title'], \GetPost::uget('news_id'));
        self::response();
    }

    private function __get_data()
    {
        return \GetPost::ar(['title', 'small_description', 'description', 'category_id', 'meta'], false, ['description']);
    }

    public function add()
    {
        $values = $this->__get_data();
        (new news())->add_news($values['title'], $values['category_id'], site_metrics::get_current_date(), $values);
        form::redirectJS(routes::link('admin_news'));
        self::response();
    }

    public function remove()
    {
        $id = \GetPost::uget('id');
        (new news())->delete($id);
        form::redirectJS(routes::link('admin_news'));
        self::response();
    }
}