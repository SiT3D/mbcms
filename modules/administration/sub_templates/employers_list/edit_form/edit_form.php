<?php

namespace trud\admin\templates\employers_list;

use GetPost;
use Kontrolio\Factory;
use Kontrolio\Rules\Core\Email;
use Kontrolio\Rules\Core\NotBlank;
use Kontrolio\Rules\Core\QueryHave;
use MBCMS\block;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\routes;
use trud\classes\auth;
use trud\classes\model\user;
use trud\classes\model\users_employerinfo;

class edit_form extends block implements \adminAjax
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new form(),
            new input(null),
        ];
    }

    public function init()
    {
        parent::init();

        $type = null;
        $user_id_opt = null;

        if ($user_id = GetPost::uget('id'))
        {
            $type = 'edit';

            $user = user::factory()->get_user_with_employerinfo($user_id)->get();

            $user_id_opt = new input('user_id', $user_id, input::TYPE_HIDDEN);
        }
        else
        {
            $type = 'add';
        }

        $this->uid = $user_id;


        $form = form::factory(__CLASS__ . '->' . $type, 'admin_employers_edit');
        $this->ADDM($form, 'modules');


        if ($user_id_opt)
        {
            $form->ADDM($user_id_opt, 'modules');
        }

        $opt = input::factory('type', $type, input::TYPE_HIDDEN);
        $form->ADDM($opt, 'modules');

        $opt = new input('fullname', isset($user->fullname) ? $user->fullname : '');
        $opt->title = 'Имя и фамилия';
        $form->ADDM($opt, 'modules');


        $opt = new input('phone', isset($user->phone) ? $user->phone : '');
        $opt->title = 'Телефон';
        $form->ADDM($opt, 'modules');

        if ($type == 'edit')
        {
            $opt = new input('companyid', isset($user->companyid) ? $user->companyid : '');
            if (isset($user->companyid) && $user->companyid)
            {
                $opt->setReadonly(true);
            }
            $opt->title = 'ID компании';
            $form->ADDM($opt, 'modules');
        }


        $opt = new input('uname', isset($user->uname) ? $user->uname : '');
        if ($type == 'edit')
        {
            $opt->readonly = true;
        }
        $opt->title = 'Email';
        $form->ADDM($opt, 'modules');

        $opt = new input('upass');
        $opt->title = 'Пароль';
        $opt->placeholder = 'Значение = замена!';
        $opt->__attr_title = 'Введите значение, чтобы заменить. Оставьте пустым чтобы остался старый пароль!';
        $opt->add_attr('__attr_title', 'title');
        $form->ADDM($opt, 'modules');

        $opt = new input('approved', null, input::TYPE_CHECKBOX);
        $opt->title = 'Почта подтверждена';
        $opt->selected_index = isset($user->approved) ? $user->approved : '';
        $form->ADDM($opt, 'modules');

        $opt = new input('blocked', null, input::TYPE_CHECKBOX);
        $opt->title = 'Заблокирован';
        $opt->selected_index = isset($user->blocked) ? $user->blocked : '';
        $form->ADDM($opt, 'modules');

        $opt = new input('sub', null, input::TYPE_SUBMIT);
        $form->ADDM($opt, 'modules');
    }

    /**
     * ajax
     */
    public function edit()
    {
        if ($this->__validate())
        {
            list($values, $dop_values) = $this->__get_values();

            users_employerinfo::factory()->update_user(GetPost::uget('user_id'), $dop_values);
            user::factory()->update_user(GetPost::uget('user_id'), $values);
        }

        self::response();
    }

    private function __validate()
    {
        $rules = [
            'fullname' => new NotBlank(),
        ];

        if (GetPost::uget('type') == 'add')
        {
            $rules['uname'] = [new Email(), new QueryHave(user::factory()->get_user_by_email(GetPost::uget('uname')))];
            $rules['upass'] = [new NotBlank()];
        }

        $validator = Factory::getInstance()->make(GetPost::ar(['fullname', 'uname', 'upass']), $rules, [
            'fullname' => 'Укажите имя',
            'uname.email' => 'Укажите корректный email',
            'uname.query_have' => 'Такой пользователь уже есть',
            'upass' => 'Укажите пароль',
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

    private function __get_values()
    {
        $values = GetPost::ar([
            'companyid',
            'approved',
            'blocked',
            'uname',
        ]);

        if ($pass = GetPost::uget('upass'))
        {
            $values['upass'] = auth::factory()->get_pass($pass);
        }

        $dop_values = GetPost::ar([
            'fullname',
            'phone',
        ]);

        return [$values, $dop_values];
    }

    /**
     * ajax
     */
    public function add()
    {
        if ($this->__validate())
        {
            list($values, $dop_values) = $this->__get_values();
            user::factory()->add_user($values['uname'], $values['upass'], $values, $dop_values, GetPost::uget('companyid'), user::ACCTYPE_EMPLOYER);
        }

        form::redirectJS(routes::link('admin_employers'));
        self::response();
    }

    public function remove_user()
    {
        user::factory()->admin_remove_full(GetPost::uget('user_id'));
        self::add_response('__redirect', routes::link('admin_employers'));
        self::response();
    }

}
