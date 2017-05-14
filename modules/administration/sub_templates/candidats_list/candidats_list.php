<?php

namespace trud\admin\templates;

use GetPost;
use MBCMS\DB;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\form\select;
use trud\classes\model\user;
use trud\classes\model\users_candidatinfo;
use trud\templates\paginator;

class candidats_list extends \Module
{

    const COUNT_IN_PAGE = 20;

    public function init_files()
    {
        return [
            parent::init_files(),
            new paginator(1, 1, 1),
            new filter_form(),
            new input(1),
            new select(1),

        ];
    }

    public function init()
    {
        parent::init();

        $query = user::factory()->get_candidats();
        $query->lj('t_users_candidatinfo', "t_users_candidatinfo.userid = t_users.id");
        $query->lj('t_resumes', 't_resumes.userid = t_users.id');
        $query->g('t_users.id');
        $query->s(['t_users_candidatinfo.*', 'count(t_resumes.id) as resumes', 't_users.*', 't_users.id as uid']);
        $query->offset((GetPost::uget('pg', 1) - 1) * self::COUNT_IN_PAGE);
        $query->limit(self::COUNT_IN_PAGE);
        $query->o('t_users.id DESC');
        $this->__filter_query($query);


        $count = $query->count();

        $this->__items = $query->get();

        $paginate = new paginator($count, GetPost::uget('pg', 1), self::COUNT_IN_PAGE);
        $this->ADDM($paginate, 'paginate');
        $this->__filter_form();

        foreach ($this->__items as &$item)
        {
            $item->sex = $item->sex == users_candidatinfo::SEX_WOMAN ? 'Жен' : 'Муж';
            $blocked = $item->blocked ? 'Заблокирован' : '';
            $app = $item->approved ? 'Подтвержден' : 'НЕ подтвержден';
            $item->status = $blocked . ' ' . $app;
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
            $query->w('t_users.id = ?', $value);
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
                ['value' => 't_users.id DESC', 'title' => 'id по убыванию'],
                ['value' => 't_users.id ASC', 'title' => 'id по возрастанию'],
                ['value' => 't_users.uname ASC', 'title' => 'email по алфавиту'],
                ['value' => 't_users.uname DESC', 'title' => 'email обратный алфавит'],
                ['value' => 'resumes ASC', 'title' => 'количество вакансий возрастание'],
                ['value' => 'resumes DESC', 'title' => 'количество вакансий убывание'],
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
