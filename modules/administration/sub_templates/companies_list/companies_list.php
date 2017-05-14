<?php

namespace trud\admin\templates;

use GetPost;
use MBCMS\DB;
use MBCMS\form\input;
use MBCMS\form\select;
use trud\classes\model\companies;
use trud\site_metrics;
use trud\templates\paginator;

class companies_list extends \Module
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new paginator(1, 2, 3),
            new filter_form(),
            new input(1),
            new select(1),
        ];
    }

    public function init()
    {
        parent::init();

        $query = companies::factory()->get_all();

        $query->s([
            't_companies.*',
            'count(t_vacancies.id) as vacancies',
            't_users_employerinfo.fullname',
            't_users_employerinfo.userid as uid',
            't_users.uname',
        ]);
        $query->lj('t_vacancies', 't_vacancies.companyid = t_companies.id');
        $query->lj('t_users', 't_users.id = t_companies.companyfounder');
        $query->lj('t_users_employerinfo', 't_users_employerinfo.userid = t_companies.companyfounder');
        $query->g('t_companies.id');
        $query->o('t_companies.id DESC');

        $lim = 20;

        $query->offset((GetPost::uget('pg', 1) - 1) * $lim);
        $query->limit($lim);
        $this->__filter_query($query);

        $all = $query->count();
        $pag = new paginator($all, GetPost::uget('pg', 1), $lim);
        $this->ADDM($pag, 'paginate');
        $this->__filter_form();

        $this->__items = $query->get();


        foreach ($this->__items as $item)
        {
            $item->companyname = trim($item->companyname) ? $item->companyname : 'Нет названия';
            $item->companytype = site_metrics::company_type($item->companytype);
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
            $query->w('t_companies.id = ?', $value);
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
                ['value' => 't_companies.id DESC', 'title' => 'id по убыванию'],
                ['value' => 't_companies.id ASC', 'title' => 'id по возрастанию'],
                ['value' => 't_users.uname ASC', 'title' => 'email по алфавиту'],
                ['value' => 't_users.uname DESC', 'title' => 'email обратный алфавит'],
                ['value' => 'vacancies ASC', 'title' => 'количество вакансий возрастание'],
                ['value' => 'vacancies DESC', 'title' => 'количество вакансий убывание'],
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
