<?php

namespace trud\admin\templates\vacancies_list;

use GetPost;
use Kontrolio\Factory;
use Kontrolio\Rules\Core\NotBlank;
use Kontrolio\Rules\Core\NotEmptyArray;
use MBCMS\block;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\form\select;
use MBCMS\routes;
use trud\classes\model\companies;
use trud\classes\model\user;
use trud\classes\model\vacancies;
use trud\classes\model\vacancy_categories;
use trud\classes\model\vacancy_cities;
use trud\form_element\categories_picker;
use trud\form_element\city_picker;
use trud\form_element\edclvl;
use trud\form_element\text_area;
use trud\form_element\user_picker;
use trud\form_element\work_exp;
use trud\form_element\worktype;

class edit_form extends \MBCMS\block implements \adminAjax
{

    public function init_files()
    {
        return [
            new text_area(null),
            parent::init_files(),
            new form(),
            new input(1),
            new user_picker(),
            new categories_picker(),
            new city_picker,
            new worktype,
            new work_exp,
            new edclvl,
            new block,
        ];
    }

    public function init()
    {
        parent::init();

        $type = null;
        $vacancy = null;
        $vacancy_id_opt = null;
        $company_id_opt = null;
        $userpicker = null;

        $categories = [];

        if ($id = GetPost::uget('id'))
        {
            $type = 'edit';

            $vacancy = vacancies::factory()->get_by_id(GetPost::uget('id'))->limit(1)->get();
            $categories = vacancy_categories::factory()->get_by_id($id);
            $city = vacancy_cities::factory()->get_by_id($id)->is_mono()->get();

            $vacancy_id_opt = new input('vacancy_id', $id, input::TYPE_HIDDEN);
            $company_id_opt = new input('company_id', isset($vacancy->companyid) ? $vacancy->companyid : '', input::TYPE_HIDDEN);
        }
        else
        {
            $type = 'add';

            $userpicker = new user_picker();
            $userpicker->user_type = user::ACCTYPE_EMPLOYER;
        }

        $form = form::factory(__CLASS__ . '->' . $type, 'admin_edit_vacancies');
        $this->ADDM($form, 'modules');

        if ($vacancy_id_opt)
        {
            $form->ADDM($vacancy_id_opt, 'modules');
            $form->ADDM($company_id_opt, 'modules');
        }

        if ($userpicker)
        {
            $form->ADDM($userpicker, 'modules');
        }


        $opt = input::factory('type', $type, input::TYPE_HIDDEN);
        $form->ADDM($opt, 'modules');

        $opt = new input('title', isset($vacancy->title) ? $vacancy->title : '');
        $opt->title = 'Заголовок';
        $form->ADDM($opt, 'modules');

        $opt = new categories_picker();
        $opt->multiple = true;
        $opt->values = $categories;
        $form->ADDM($opt, 'modules');

        $opt = new city_picker();
        $opt->values = isset($city->cityid) ? $city->cityid : null;
        $form->ADDM($opt, 'modules');

        $opt = new worktype();
        $opt->value = isset($vacancy->worktype) ? $vacancy->worktype : '';
        $form->ADDM($opt, 'modules');

        $opt = new input('salary', isset($vacancy->salary) ? $vacancy->salary : '');
        $opt->title = 'Зарплата';
        $form->ADDM($opt, 'modules');

        $opt = new input('salarycomment', isset($vacancy->salarycomment) ? $vacancy->salarycomment : '');
        $opt->title = 'Комментарий к зарплате';
        $form->ADDM($opt, 'modules');

        $opt = new work_exp();
        $opt->name = 'workexperience';
        $opt->value = isset($vacancy->workexperience) ? $vacancy->workexperience : '';
        $form->ADDM($opt, 'modules');

        $opt = new edclvl();
        $opt->name = 'educationlevel';
        $opt->student_value = isset($vacancy->studentwelcome) ? $vacancy->studentwelcome : '';
        $opt->value = isset($vacancy->educationlevel) ? $vacancy->educationlevel : '';
        $form->ADDM($opt, 'modules');


        $opt = new text_area('vacancydescription', isset($vacancy->vacancydescription) ? $vacancy->vacancydescription : '');
        $opt->title = 'Описание';
        $opt->setCKEditor();
        $form->ADDM($opt, 'modules');

        $opt = new input('ifpublish', NULL, input::TYPE_CHECKBOX);
        $opt->selected_index = isset($vacancy->ifpublish) ? $vacancy->ifpublish : '';
        $opt->title = 'Размещать данные контактного лица на странице';
        $form->ADDM($opt, 'modules');

        $opt = new select('visible');
        $opt->options = [
            ['value' => '0', 'title' => 'Неактивное'],
            ['value' => '1', 'title' => 'Активное'],
        ];
        $opt->title = 'Статус вакансии';
        $opt->values = isset($vacancy->visible) ? $vacancy->visible : '';
        $form->ADDM($opt, 'modules');


        $opt = new input('sub', '', input::TYPE_SUBMIT);
        $form->ADDM($opt, 'modules');

        $delbtn = block::factory('Удалить вакансию безвозвратно!', 'button', 'vacancy_full_deleter full_deleter');
        $delbtn->__del_id = $id;
        $delbtn->add_attr('__del_id', 'vacid');
        $this->ADDM($delbtn, 'modules');
    }

    public function edit()
    {

        if ($this->__validate())
        {
            vacancies::factory()->update($this->__get_data(), GetPost::uget('vacancy_id')
                , GetPost::uget('company_id'), GetPost::uget('title'), GetPost::get('categories', []), GetPost::uget('cities'));
        }

        form::redirectJS(routes::link('admin_vacancies'));
        self::response();
    }

    private function __validate()
    {
        $rules = [
            'title' => new NotBlank(),
            'vacancydescription' => new NotBlank(),
            'categories' => new NotEmptyArray(),
        ];

        if (GetPost::uget('type') == 'add')
        {
            $rules['users_in_userpicker'] = new NotBlank();
        }


        $validator = Factory::getInstance()->make(GetPost::ar(['title', 'vacancydescription', 'users_in_userpicker', 'categories']), $rules, [
            'title' => 'Укажите название вакансии',
            'vacancydescription' => 'Укажите Описание вакансии',
            'users_in_userpicker' => 'Укажите пользователя для этой вакансии, вакансия не может существовать без пользователя!',
            'categories.not_empty_array' => 'Укажите категории',
        ]);

        if ($validator->validate())
        {
            return true;
        }
        else
        {
            form::errors($validator->getErrors());
            return false;
        }
    }

    private function __get_data()
    {
        return GetPost::ar([
            'title',
            'worktype',
            'salary',
            'salarycomment',
            'workexperience',
            'educationlevel',
            'vacancydescription',
            'ifpublish',
            'studentwelcome',
            'visible',
        ], false, ['vacancydescription']);
    }

    public function add()
    {
        if ($this->__validate())
        {
            $user_id = GetPost::uget('users_in_userpicker');
            $company = companies::factory()->get_all()->w('companyfounder = ?', $user_id)->is_mono()->get();

            if (isset($company->id) && $company->id)
            {
                vacancies::factory()->add($this->__get_data(), $user_id, $company->id, GetPost::uget('title'), GetPost::get('categories', []), GetPost::uget('cities'));
            }
        }

        form::redirectJS(routes::link('admin_vacancies'));
        self::response();
    }

    public function ajax_remove()
    {
        $vacancy_id = GetPost::uget('vacancy_id');
        vacancies::factory()->remove_by_id_full($vacancy_id);
        self::add_response('__redirect', routes::link('admin_vacancies'));
        self::response();
    }

}
