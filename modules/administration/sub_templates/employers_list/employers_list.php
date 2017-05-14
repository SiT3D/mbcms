<?php

namespace trud\admin\templates;

use MBCMS\block;
use trud\classes\model\user;
use trud\templates\paginator;
use GetPost;
use MBCMS\form\select;
use MBCMS\form\input;
use MBCMS\DB;


class employers_list extends block
{

    const COUNT_IN_PAGE = 20;

    public function init()
    {
        parent::init();

        $query = user::factory()->get_employers();
        $query->lj('t_companies', "t_companies.id = t_users.companyid");
        $query->j('t_users_employerinfo', "t_users_employerinfo.userid = t_users.id");
        $query->lj('t_vacancies', "t_vacancies.userid = t_users.id");
        $query->s(['count(t_vacancies.id) as vacancies', 't_users_employerinfo.*', 't_companies.*', 't_companies.id as cid', 't_users.*']);
        $query->o("t_users.id DESC");
        $query->g("t_users.id");
        $query->offset((\GetPost::uget('pg', 1) -1) * 20);
        $query->limit(20);
        $this->__filter_query($query);


        $this->__items = $query->get();

        $all = $query->count();
        $paginator = new paginator($all, \GetPost::uget('pg', 1), self::COUNT_IN_PAGE);
        $this->ADDM($paginator, '$paginate');
        $this->__filter_form();

        foreach ($this->__items as $item)
        {
            if ($item->blocked)
            {
                $b = new block();
                $b->__cms_block_type = 'button';
                $b->__text = 'Заблокирован';
                $this->ADDM($b, 'status' . $item->id);
            }

            $b = new block();
            $b->__cms_block_type = 'button';
            if ($item->approved)
            {
                $b->__text = 'Подтвержден';
            }
            else
            {
                $b->__text = 'НЕ подтвержден';
            }
            $this->ADDM($b, 'status' . $item->id);

        }
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new paginator(null, null, null),
            new block(),
            new filter_form(),
            new input(null),
            new select(null),
        ];
    }

    private function __filter_query(DB $query)
    {
        if ($value = GetPost::uget('uname'))
        {
            $query->w('t_users.uname LIKE ?', "%$value%");
        }

        if ($value = GetPost::uget('id'))
        {
            $query->w('t_users.id = ?', $value);
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
                ['value' => 't_users.id DESC', 'title' => 'id по убыванию'],
                ['value' => 't_users.id ASC', 'title' => 'id по возрастанию'],
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
