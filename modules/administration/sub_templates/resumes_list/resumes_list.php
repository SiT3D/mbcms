<?php

namespace trud\admin\templates;

use GetPost;
use MBCMS\DB;
use trud\classes\model\resumes;
use trud\form_element\city_picker;
use trud\templates\paginator;
use MBCMS\form\input;
use MBCMS\form\select;

class resumes_list extends \MBCMS\block
{

    const COUNT_IN_PAGE = 20;

    public function init_files()
    {
        return [
            parent::init_files(),
            new paginator(1, 1, 1),
            new filter_form(),
            new city_picker(),
            new input(1),
            new select(1),
        ];
    }

    public function init()
    {
        parent::init();

        $query = resumes::factory()->get_all_resumes();
        $query->lj('t_users_candidatinfo', "t_users_candidatinfo.userid = t_resumes.userid")
            ->lj('t_users', 't_users.id = t_resumes.userid')
            ->lj('t_resume_cities', 't_resume_cities.resume_id = t_resumes.id')
            ->lj('t_all_cities', 't_resume_cities.cityid = t_all_cities.id')
            ->o("t_resumes.id DESC");


        $query->offset((GetPost::uget('pg', 1) - 1) * self::COUNT_IN_PAGE);
        $query->limit(self::COUNT_IN_PAGE);
        $query->s(['t_users_candidatinfo.*', 't_users.*', 't_users.id as uid', 't_resumes.*', 't_all_cities.name_ru']);
        $this->__filter_query($query);


        $count = $query->count();

        $this->__items = $query->get();

        $paginate = new paginator($count, GetPost::uget('pg', 1), self::COUNT_IN_PAGE);
        $this->ADDM($paginate, 'paginate');
        $this->__filter_form();

        foreach ($this->__items as &$item)
        {
            $item->type = 'Тип резюме';
            $item->city = $item->name_ru;
            $item->title = trim($item->title) ? $item->title : 'Без названия';
        }
    }

    private function __filter_query(DB $query)
    {
        if ($value = GetPost::uget('uname'))
        {
            $query->w('t_users.uname LIKE ?', "%$value%");
        }

        if ($value = GetPost::uget('id'))
        {
            $query->w('t_resumes.id = ?', $value);
        }

        if ($value = GetPost::uget('fullname'))
        {
            $query->wc('L', 'AND');
            $query->w('t_users_candidatinfo.firstname LIKE ?', "%$value%");
            $query->w('t_users_candidatinfo.lastname LIKE ?', "%$value%", 'OR');
            $query->wc('R', '');
        }

        if ($value = GetPost::uget('order'))
        {
            $query->o($value);
        }

        if ($value = GetPost::get('blocks'))
        {
            foreach ($value as $val)
            {
                if ($val == 'blocked')
                {
                    $query->w('t_users.blocked = 1');
                }

                if ($val == 'approved')
                {
                    $query->w('t_users.approved = 1');
                }
                else if ($val == 'unapproved')
                {
                    $query->w('t_users.approved = 0');
                }
            }
        }

        if ($value = GetPost::uget('cities') && $value)
        {
            $query->w('t_all_cities.id = ?', $value);
        }

    }

    private function __filter_form()
    {
        $form = filter_form::factory('');
        $this->ADDM($form, 'filter');

        $opt = input::factory('id', GetPost::uget('id'))->setTitle('id');
        $form->ADDM($opt, 'modules');

        $opt = input::factory('uname', GetPost::uget('uname'))->setTitle('email');
        $form->ADDM($opt, 'modules');

        $opt = input::factory('fullname', GetPost::uget('fullname'))->setTitle('Имя или фамилия');
        $form->ADDM($opt, 'modules');


        $opt = select::factory('order')
            ->setOptions([
                ['value' => 't_resumes.id DESC', 'title' => 'id по убыванию'],
                ['value' => 't_resumes.id ASC', 'title' => 'id по возрастанию'],
                ['value' => 't_users.uname ASC', 'title' => 'email по алфавиту'],
                ['value' => 't_users.uname DESC', 'title' => 'email обратный алфавит'],
            ])
            ->setValues(GetPost::uget('order'))
            ->setTitle('Сортировка ответа');
        $form->ADDM($opt, 'modules');

        $opt = select::factory('blocks[]')
            ->setOptions([
                ['value' => 'blocked', 'title' => 'Заблокированные'],
                ['value' => 'approved', 'title' => 'Подтвержденные'],
                ['value' => 'unapproved', 'title' => 'НЕ подтвержденные'],
            ])
            ->setMultiple(true)
            ->setValues(GetPost::uget('blocks'))
            ->setTitle('Фильтр заблокированных');
        $form->ADDM($opt, 'modules');

        $opt = new city_picker();
        $opt->values = GetPost::uget('cities');
        $opt->with_empty = true;
        $form->ADDM($opt, 'modules');
    }

    public function ajax_remove()
    {
        resumes::factory()->remove_by_id_full(GetPost::uget('resume_id'));
    }

}
