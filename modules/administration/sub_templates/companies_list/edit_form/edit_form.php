<?php

namespace trud\admin\templates\companies_list;

use GetPost;
use Kontrolio\Factory;
use Kontrolio\Rules\Core\NotBlank;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\form\select;
use MBCMS\image_galary;
use MBCMS\image_galary\upload_form;
use MBCMS\image_galary\viewer;
use MBCMS\routes;
use trud\classes\model\companies;
use trud\classes\model\user;
use trud\form_element\text_area;
use trud\form_element\user_picker;

class edit_form extends \MBCMS\block implements \adminAjax
{

    public function init_files()
    {
        return [
            new text_area(),
            parent::init_files(),
            new form(),
            new input(1),
            new user_picker(),
            new select(1),
            new viewer(),
            new upload_form(),
        ];
    }

    public function init()
    {
        parent::init();

        $user_id_opt     = null;
        $user_picker_opt = null;

        if ($id = GetPost::uget('id'))
        {
            $type = 'edit';

            $company     = companies::factory()->get_by_id($id)->get();
            $user_id_opt = new input('user_id', isset($company->companyfounder) ? $company->companyfounder : '', input::TYPE_HIDDEN);
        }
        else
        {
            $type = 'add';

            $user_picker_opt            = new user_picker;
            $user_picker_opt->user_type = user::ACCTYPE_EMPLOYER;
        }

        $form = form::factory(__CLASS__ . '->' . $type, 'mc_company_save_data');
        $this->ADDM($form, 'modules');


        if ($user_id_opt)
        {
            $form->ADDM($user_id_opt, 'modules');
        }

        if ($user_picker_opt)
        {
            $form->ADDM($user_picker_opt, 'modules');
        }

        $opt        = new input('companyname', isset($company->companyname) ? $company->companyname : '');
        $opt->title = 'Название компании';
        $form->ADDM($opt, 'modules');

        $opt = new input('company_id', isset($company->id) ? $company->id : '', input::TYPE_HIDDEN);
        $form->ADDM($opt, 'modules');

        $opt          = new select('companytype');
        $opt->options = [
            ['value' => 1, 'title' => 'Прямой работодатель'],
            ['value' => 2, 'title' => 'Агентство'],
        ];
        $opt->values  = isset($company->companytype) ? $company->companytype : '';
        $opt->title   = 'Тип компании';
        $opt->chosen  = false;
        $form->ADDM($opt, 'modules');

        $opt        = new input('website', isset($company->website) ? $company->website : '');
        $opt->title = 'Сайт';
        $form->ADDM($opt, 'modules');

        $opt        = new input('email', isset($company->email) ? $company->email : '');
        $opt->title = 'email';
        $form->ADDM($opt, 'modules');

        $opt        = new input('phone', isset($company->phone) ? $company->phone : '');
        $opt->title = 'Телефон';
        $form->ADDM($opt, 'modules');

        $opt        = new text_area('companydescription', isset($company->companydescription) ? $company->companydescription : '');
        $opt->title = 'Описание компании';
        $opt->setCKEditor();
        $form->ADDM($opt, 'modules');

        $opt                 = new input('companyapproved', '', input::TYPE_CHECKBOX);
        $opt->title          = 'Проверенная компания';
        $opt->selected_index = isset($company->companyapproved) ? $company->companyapproved : '';
        $form->ADDM($opt, 'modules');

        $opt = new input('sub', '', input::TYPE_SUBMIT);
        $form->ADDM($opt, 'modules');

        if($type == 'edit')
        {
            $this->__image_uploader($id);
        }

    }

    private function __image_uploader($company_id)
    {
        $tags = image_galary::factory()->find_by_tags(['company_id' => $company_id])->is_mono()->limit(1)->o('id DESC')->get();

        if ($tags)
        {
            $this->ADDM((new viewer())->setSrc($tags->image_id)->setSize('200px', 'auto')->setGalaryAdminHref(['ID_компании_' . $company_id]), 'modules');
        }

        $image_uploader = form::factory('', 'image_uplouder');
        $this->ADDM($image_uploader, 'modules');

        $image_uploader->ADDM(input::factory('company_id', $company_id, input::TYPE_HIDDEN), 'modules');
        $image_uploader->ADDM(input::factory('is_admin_add', true, input::TYPE_HIDDEN), 'modules');
        $image_uploader->ADDM((new upload_form())->setMultiple(false), 'modules');
    }

    /**
     * ajax
     */
    public function add()
    {
        if ($this->__validate())
        {
            $company_id = companies::factory()->add_company(GetPost::uget('users_in_userpicker'), $this->__get_values());
            user::factory()->update_user(GetPost::uget('users_in_userpicker'), ['companyid' => $company_id]);
        }

        form::redirectJS(routes::link('admin_companies'));
        self::response();
    }

    private function __get_values()
    {
        $values = GetPost::ar([
                    'companyname',
                    'companytype',
                    'website',
                    'email',
                    'phone',
                    'companydescription',
                    'companyapproved',
        ], false, ['companydescription']);

        return $values;
    }

    /**
     * ajax
     */
    public function edit()
    {
        if ($this->__validate())
        {
            companies::factory()->update(GetPost::uget('company_id'), $this->__get_values());
            user::factory()->update_user(GetPost::uget('user_id'), ['companyid' => GetPost::uget('company_id')]);
        }

        form::redirectJS(routes::link('admin_companies'));
        self::response();
    }

    private function __validate()
    {
        $rules = [
            'companyname' => new NotBlank(),
            'companydescription' => new NotBlank(),
        ];

        if (!GetPost::uget('user_id'))
        {
            $rules['users_in_userpicker'] = new NotBlank();
        }

        $validator = Factory::getInstance()->make(GetPost::ar(['companyname', 'companydescription', 'users_in_userpicker']), $rules,[
            'companyname' => 'Укажите название компании',
            'companydescription' => 'Укажите Описание компании',
            'users_in_userpicker' => 'Укажите пользователя для этой компании, компания не может существовать без пользователя!',
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

}
