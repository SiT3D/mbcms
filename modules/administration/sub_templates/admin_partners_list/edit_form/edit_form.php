<?php

namespace trud\admin\templates\admin_partners;

use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\form\select;
use MBCMS\image_galary;
use MBCMS\image_galary\upload_form;
use MBCMS\image_galary\viewer;
use MBCMS\routes;
use trud\classes\model\partners;
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

        $partners = (new partners())->get_by_id($id)->get();

        $this->method = $id ? 'edit' : 'add';

        $form = form::factory(__CLASS__ . '->' . $this->method, 'admin_partners_form');
        $this->ADDM($form, 'modules');

        $form->ADDM(input::factory('partner_id', $id, input::TYPE_HIDDEN), 'modules');

        $form->ADDM(input::factory('title', isset($partners->title) ? $partners->title : '')->setTitle('Заголовок'), 'modules');

        $opt = new text_area('small_description', isset($partners->small_description) ? $partners->small_description : '');
        $opt->title = 'Описание превью';
        $form->ADDM($opt, 'modules');

        $opt = new text_area('description', isset($partners->description) ? $partners->description : '');
        $opt->title = 'Содержание статьи';
        $opt->setCKEditor(text_area::CKEDITOR_TYPE_ADMIN);
        $form->ADDM($opt, 'modules');

        $opt = new text_area('meta', isset($partners->meta) ? $partners->meta : '');
        $opt->title = 'Метаданные для поисковых систем';
        $form->ADDM($opt, 'modules');


        $form->ADDM(input::factory('sub', 'Сохранить', input::TYPE_SUBMIT), 'modules');

        $remove_form = form::factory(__CLASS__ . '->remove', 'admin_partners_remove_form');
        $this->ADDM($remove_form, 'modules');

        $remove_form->ADDM(input::factory('id', \GetPost::uget('id'), input::TYPE_HIDDEN), 'modules');
        $remove_form->ADDM(input::factory('sub', 'Удалить', input::TYPE_SUBMIT), 'modules');

        if ($id)
        {
            $this->__image_uploader($id);
        }
    }

    private function __image_uploader($partner)
    {
        $tags = image_galary::factory()->find_by_tags(['partner_id' => $partner])->is_mono()->limit(1)->get();

        if ($tags)
        {
            $this->ADDM((new viewer())->setSrc($tags->image_id)->setSize('200px', 'auto')->setGalaryAdminHref(['ID статьи ' . $partner]), '$image');
        }

        $image_uploader = form::factory('', 'image_uplouder');
        $this->ADDM($image_uploader, '$image');

        $image_uploader->ADDM(input::factory('partner_id', $partner, input::TYPE_HIDDEN), 'modules');
        $image_uploader->ADDM((new upload_form())->setMultiple(false), 'modules');
    }

    public function edit()
    {
        $values = $this->__get_data();
        (new partners())->update($values, $values['title'], \GetPost::uget('partner_id'));
        self::response();
    }

    private function __get_data()
    {
        return \GetPost::ar(['title', 'small_description', 'description', 'meta'], false, ['description']);
    }

    public function add()
    {
        $values = $this->__get_data();
        (new partners())->add_partner($values['title'], site_metrics::get_current_date(), $values);
        form::redirectJS(routes::link('admin_partners'));
        self::response();
    }

    public function remove()
    {
        $id = \GetPost::uget('id');
        (new partners())->delete($id);
        form::redirectJS(routes::link('admin_partners'));
        self::response();
    }
}