<?php

namespace trud\admin\templates\resumes_list;

use GetPost;
use Kontrolio\Factory;
use Kontrolio\Rules\Core\NotBlank;
use Kontrolio\Rules\Core\NotEmptyArray;
use MBCMS\block;
use MBCMS\form\input;
use MBCMS\form\form;
use MBCMS\form\select;
use MBCMS\routes;
use trud\classes\model\resume_education;
use trud\classes\model\resume_workexperience;
use trud\classes\model\resumes;
use trud\classes\model\user;
use trud\conn\connector;
use trud\form_element\categories_picker;
use trud\form_element\city_picker;
use trud\form_element\education_picker;
use trud\form_element\text_area;
use trud\form_element\user_picker;
use trud\form_element\worktype;

class edit_form extends \MBCMS\block implements \adminAjax
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new form(),
            new user_picker(),
            new categories_picker(),
            new city_picker(),
            new worktype(),
            new text_area(null),
            new education_picker(),
            new block,
        ];
    }

    public function init()
    {
        parent::init();

        $resume = null;

        $form = form::factory(__CLASS__ . '->', 'admin_resumes_edit');
        $this->ADDM($form, 'modules');
        $educations = [];
        $exps = [];

        if ($id = GetPost::uget('id'))
        {
            $type = 'edit';
            $query = resumes::factory()->get_by_id($id);
            $query->lj('t_resume_cities', 't_resume_cities.resume_id = t_resumes.id')
                ->lj('t_resume_categories', 't_resume_categories.resume_id = t_resumes.id')
                ->is_mono(false);
            $resume = $query->get();

            $resume = __many($resume, ['catid'], 'id', false, false);
            $resume = array_shift($resume);

            $opt = new input('user_id', $resume->userid, input::TYPE_HIDDEN);
            $form->ADDM($opt, 'modules');
            $opt = new input('resume_id', GetPost::uget('id'), input::TYPE_HIDDEN);
            $form->ADDM($opt, 'modules');

            $educations = resume_education::factory()->get_educations($resume->id)->get();
            $exps = resume_workexperience::factory()->get_exps($resume->id)->get();
        }
        else
        {
            $type = 'add';
        }

        $form->action_class .= $type;

        if ($type == 'add')
        {
            $opt = new user_picker();
            $opt->user_type = user::ACCTYPE_CANDIDATE;
            $form->ADDM($opt, 'modules');
        }

        $opt = new input('title', isset($resume->title) ? $resume->title : '');
        $opt->title = 'Заголовок';
        $form->ADDM($opt, 'modules');

        $opt = new categories_picker();
        $opt->multiple = true;
        $opt->name = 'categories[]';
        $opt->values = isset($resume->catid) ? $resume->catid : '';
        $opt->title = 'Категории';
        $form->ADDM($opt, 'modules');

        $opt = new city_picker();
        $opt->title = 'Города';
        $opt->values = isset($resume->cityid) ? $resume->cityid : '';
        $opt->name = 'city';
        $form->ADDM($opt, 'modules');

        $opt = new worktype();
        $opt->name = 'worktype';
        $opt->value = isset($resume->worktype) ? $resume->worktype : '';
        $form->ADDM($opt, 'modules');

        $opt = new input('salary', isset($resume->salary) ? $resume->salary : '');
        $opt->title = 'Зарплата';
        $form->ADDM($opt, 'modules');

        $opt = new select('visible');
        $opt->options = [
            ['value' => '0', 'title' => 'Неактивное'],
            ['value' => '1', 'title' => 'Активное'],
        ];
        $opt->title = 'Статус резюме';
        $opt->values = isset($resume->visible) ? $resume->visible : '';
        $form->ADDM($opt, 'modules');

        $opt = new text_area('resumedescription', isset($resume->resumedescription) ? $resume->resumedescription : '');
        $opt->title = 'Описание резюме';
        $form->ADDM($opt, 'modules');


        $opt = new education_picker();
        $opt->educations = isset($educations) ? $educations : [];
        $opt->works = $exps;
        $form->ADDM($opt, 'modules');

        $opt = new input('sub', null, input::TYPE_SUBMIT);
        $form->ADDM($opt, 'modules');


        $delbtn = block::factory('Удалить резюме безвозвратно!', 'button', 'resume_full_deleter full_deleter');
        $delbtn->__del_id = $id;
        $delbtn->add_attr('__del_id', 'resid');
        $this->ADDM($delbtn, 'modules');
    }

    /**
     * ajax
     */
    public function edit()
    {
        if ($this->__validate('edit'))
        {
            $values = GetPost::ar([
                'worktype',
                'salary',
                'visible',
                'resumedescription',
                'title',
            ]);

            resumes::factory()->update_by_id(GetPost::uget('resume_id'),
                GetPost::uget('user_id'), $values, GetPost::uget('title'),
                GetPost::uget('categories', []), GetPost::uget('city'));

            education_picker::save(GetPost::uget('resume_id'));
        }

        self::response();
    }

    /**
     * ajax
     */
    public function add()
    {
        if ($this->__validate('add'))
        {
            $values = GetPost::ar([
                'worktype',
                'salary',
                'visible',
                'resumedescription',
                'title',
            ]);

            $resume_id = resumes::factory()->add_resume($values, GetPost::uget('users_in_userpicker'), GetPost::uget('title'), GetPost::get('categories', []), GetPost::uget('city'));

            if ($resume_id)
            {
                education_picker::save($resume_id);
            }
        }

        form::redirectJS(routes::link('admin_resumes'));
        self::response();
    }

    private function __validate($type)
    {

        $rules = [
            'title' => new NotBlank(),
            'resumedescription' => new NotBlank(),
            'categories' => new NotEmptyArray(),
        ];

        if ($type == 'add')
        {
            $rules['users_in_userpicker'] = new NotBlank();
        }

        $validator = Factory::getInstance()->make(GetPost::ar(['users_in_userpicker', 'title', 'categories', 'resumedescription']), $rules, [
            'users_in_userpicker' => 'Выберите пользователя к которому нужно прикрепить резюме',
            'title' => 'Укажите Заголовок резюме',
            'resumedescription' => 'Укажите описание резюме',
            'categories' => 'Укажите категории для резюме',
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

    public function ajax_remove()
    {
        $resume_id = GetPost::uget('resume_id');
        resumes::factory()->remove_by_id_full($resume_id);
        self::add_response('__redirect', routes::link('admin_resumes'));
        self::response();
    }

}
