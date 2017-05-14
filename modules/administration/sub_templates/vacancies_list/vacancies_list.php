<?php

namespace trud\admin\templates;

use GetPost;
use MBCMS\DB;
use MBCMS\form\input;
use MBCMS\form\select;
use trud\classes\model\vacancies;
use trud\templates\paginator;

class vacancies_list extends \Module
{

    const COUNT_IN_PAGE = 20;

    public function init_files()
    {
        return [
            parent::init_files(),
            new paginator(1, 1, 1),
            new input(1),
            new select(1),
            new filter_form(),
        ];
    }

    public function init()
    {
        parent::init();

        $query = vacancies::factory()->get_all()
            ->lj('t_users_employerinfo', 't_users_employerinfo.userid = t_vacancies.userid')
            ->lj('t_users', 't_vacancies.userid = t_users.id')
            ->lj('t_companies', 't_companies.id = t_users.companyid')
            ->s([
                't_users_employerinfo.*',
                't_users_employerinfo.userid as uid',
                't_companies.*',
                't_companies.id as cid',
                't_vacancies.*',
                't_vacancies.id',
            ])
            ->o('t_vacancies.id DESC');

        $query->offset((GetPost::uget('pg', 1) - 1) * self::COUNT_IN_PAGE);
        $query->limit(self::COUNT_IN_PAGE);
        $this->__filter_query($query);

        $all = $query->count();
        $pag = new paginator($all, GetPost::uget('pg', 1), 20);
        $this->ADDM($pag, 'paginate');
        $this->__filter_form();

        $this->__items = $query->get();


        foreach ($this->__items as $item)
        {
            $item->status = $item->visible ? 'активная' : 'скрыта';
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
            $query->w('t_vacancies.id = ?', $value);
        }

        if ($value = GetPost::uget('fullname'))
        {
            $query->w('t_users_employerinfo.fullname LIKE ?', "%$value%");
        }

        if ($value = GetPost::uget('order'))
        {
            $query->o($value);
        }

        if ($value = GetPost::uget('companyname'))
        {
            $query->w('t_companies.companyname LIKE ?', "%$value%");
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

    }

    private function __filter_form()
    {
        $form = filter_form::factory('');
        $this->ADDM($form, '$filter');

        $opt = input::factory('id', GetPost::uget('id'))->setTitle('id');
        $form->ADDM($opt, 'modules');

        $opt = input::factory('uname', GetPost::uget('uname'))->setTitle('email');
        $form->ADDM($opt, 'modules');

        $opt = input::factory('fullname', GetPost::uget('fullname'))->setTitle('Имя или фамилия');
        $form->ADDM($opt, 'modules');

        $opt = input::factory('companyname', GetPost::uget('companyname'))->setTitle('Компания');
        $form->ADDM($opt, 'modules');

        $opt = select::factory('order')
            ->setOptions([
                ['value' => 't_vacancies.id DESC', 'title' => 'id по убыванию'],
                ['value' => 't_vacancies.id ASC', 'title' => 'id по возрастанию'],
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
    }

}
