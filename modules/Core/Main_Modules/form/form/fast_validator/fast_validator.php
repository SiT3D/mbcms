<?php

namespace trud;

use Kontrolio\Factory;
use Kontrolio\Rules\Core\Email;
use Kontrolio\Rules\Core\Password;
use Kontrolio\Rules\Core\QueryHave;
use MBCMS\DB;
use MBCMS\form\form;

class  fast_validator extends \Module implements \ajax
{
    public function ajax_validate_email()
    {
        $data      = \GetPost::ar(['email']);
        $rules     = ['email' => [new Email(), new QueryHave(DB::q()->s(['*'], 't_users')->w('uname = ?', $data['email']))]];
        $messages  = [
            'email.email'      => 'Вы указали некорректный Email',
            'email.query_have' => 'Такой пользователь уже есть!',
        ];
        $validator = Factory::getInstance()->make($data, $rules, $messages);

        if (!$validator->validate())
        {
            form::errors($validator->getErrors());
        }

        self::response();
    }

    public function ajax_validate_password()
    {
        $data      = \GetPost::ar(['password', 'confirm_password']);
        $rules     = ['password' => new Password($data['password'], $data['confirm_password']),];
        $messages  = ['password.password' => 'Пароли не совпадают',];
        $validator = Factory::getInstance()->make($data, $rules, $messages);

        if (!$validator->validate())
        {
            form::errors($validator->getErrors());
        }

        self::response();
    }
}