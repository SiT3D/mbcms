<?php

namespace trud\admin\templates\candidats;

use GetPost;
use Kontrolio\Factory;
use Kontrolio\Rules\Core\Email;
use Kontrolio\Rules\Core\NotBlank;
use Kontrolio\Rules\Core\QueryHave;
use MBCMS\form\input;
use MBCMS\form\form;
use MBCMS\routes;
use trud\classes\auth;
use trud\classes\model\user;
use trud\classes\model\users_candidatinfo;
use trud\form_element\date_picker;
use trud\form_element\sex_picker;

class edit_form extends \Module implements \adminAjax
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new date_picker(),
            new form(),
            new input(1),
            new sex_picker(),

        ];
    }

    public function init()
    {
        parent::init();

        $user = null;

        if ($user_id = GetPost::uget('id'))
        {
            $type = 'edit';
            $user = user::factory()->get_user_width_candidate($user_id)->get();
        }
        else
        {
            $type = 'add';
        }

        $this->uid = $user_id;


        $form = form::factory(__CLASS__ . '->' . $type, 'candidate_admin_edit');
        $this->ADDM($form, 'modules');

        $opt = input::factory('type', $type, input::TYPE_HIDDEN);
        $form->ADDM($opt, 'modules');

        $opt = new input('firstname', isset($user->firstname) ? $user->firstname : '');
        $opt->title = 'Имя';
        $form->ADDM($opt, 'modules');

        $opt = new input('lastname', isset($user->lastname) ? $user->lastname : '');
        $opt->title = 'Фамилия';
        $form->ADDM($opt, 'modules');

        $opt = new sex_picker();
        $opt->values = isset($user->sex) ? $user->sex : '';
        $form->ADDM($opt, 'modules');

        $opt = new date_picker();
        list($year, $month, $day) = explode('-', isset($user->birthday) ? $user->birthday : '--');
        $opt->year_value = $year;
        $opt->month_value = $month;
        $opt->day_value = $day;
        $form->ADDM($opt, 'modules');

        $opt = new input('phone', isset($user->phone) ? $user->phone : '');
        $opt->title = 'Телефон';
        $form->ADDM($opt, 'modules');

        $opt = new input('uname', isset($user->uname) ? $user->uname : '');
        if ($type == 'edit')
        {
            $opt->readonly = true;
        }
        $opt->title = 'email';
        $form->ADDM($opt, 'modules');

        $opt = new input('upass');
        $opt->title = 'Пароль';
        $opt->placeholder = 'Пустой не меняется';
        $form->ADDM($opt, 'modules');

        $opt = new input('approved', null, input::TYPE_CHECKBOX);
        $opt->title = 'Подтвержденная почта';
        $opt->selected_index = isset($user->approved) ? $user->approved : '';
        $form->ADDM($opt, 'modules');

        $opt = input::factory('blocked', null, input::TYPE_CHECKBOX)
            ->setSelectedIndex(isset($user->blocked) ? $user->blocked : '')
            ->setTitle('Заблокировать');
        $form->ADDM($opt, 'modules');

        $opt = new input('user_id', isset($user->id) ? $user->id : '', input::TYPE_HIDDEN);
        $form->ADDM($opt, 'modules');

        $opt = new input('sun', '', input::TYPE_SUBMIT);
        $form->ADDM($opt, 'modules');
    }

    /**
     * ajax
     */
    public function add()
    {

        if ($this->__validate())
        {
            list($values, $dop_values) = $this->__get_data();
            user::factory()->add_user($values['uname'], $values['upass'],$values, $dop_values, null, user::ACCTYPE_CANDIDATE);
        }

        form::redirectJS(routes::link('admin_candidats'));
        self::response();
    }

    /**
     *
     * @return array [$values, $dop_values]
     */
    private function __get_data()
    {
        $values = GetPost::ar([
            'approved' => 0,
            'uname',
            'blocked',
            'uname',
        ]);

        if ($pass = GetPost::uget('upass'))
        {
            $values['upass'] = auth::factory()->get_pass($pass);
        }

        $dop_values = GetPost::ar([
            'phone',
            'lastname',
            'firstname',
            'sex',
        ]);

        list($Y, $M, $D) = GetPost::ar(['year' => '1900', 'month' => '01', 'day' => '01'], true);
        $dop_values['birthday'] = $Y . '-' . $M . '-' . $D;

        return [$values, $dop_values];
    }

    /**
     * ajax
     */
    public function edit()
    {

        if ($this->__validate())
        {
            list($values, $dop_values) = $this->__get_data();
            users_candidatinfo::factory()->update_user(GetPost::uget('user_id'), $dop_values);
            user::factory()->update_user(GetPost::uget('user_id'), $values);
        }

        self::response();
    }

    private function __validate()
    {

        $rules = [
            'lastname' => new NotBlank(),
            'firstname' => new NotBlank(),
        ];

        if (GetPost::uget('type') == 'add')
        {
            $rules['uname'] = [new Email(), new QueryHave(user::factory()->get_user_by_email(GetPost::uget('uname')))];
            $rules['upass'] = [new NotBlank()];
        }

        $validator = Factory::getInstance()->make(GetPost::ar(['lastname', 'firstname', 'uname', 'upass']),$rules,[
            'lastname' => 'Укажите фамилию',
            'firstname' => 'Укажите Имя',
            'uname.email' => 'Укажите email - некорректный email',
            'uname.query_have' => 'Такой пользователь уже есть!',
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

    public function remove_user()
    {
        user::factory()->admin_remove_full(GetPost::uget('uid'));
        self::add_response('__redirect', routes::link('admin_candidats'));
        self::response();
    }

}
