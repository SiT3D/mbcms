<?php

namespace trud\admin\templates;

use MBCMS\block;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\routes;
use trud\classes\auth;
use trud\conn\connector;

class admin_auth_panel extends \MBCMS\block implements \ajax
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new connector(),
            new block(),
        ];
    }

    public function init()
    {
        parent::init();

        $form = form::factory(__CLASS__ . '->login', 'admin_auth_form');
        $form->method = 'GET';
        $this->ADDM($form, 'modules');

        $opt = new input('username');
        $opt->setClass('trud-input');
        $form->ADDM($opt, 'modules');

        $opt = new input('password', null, input::TYPE_PASSWORD);
        $opt->setClass('trud-input');
        $form->ADDM($opt, 'modules');

        $opt = new input('sub', 'Войти', input::TYPE_SUBMIT);
        $opt->setClass('trud-btn');
        $form->ADDM($opt, 'modules');

        $this->ADDM(block::factoryLink('Главная страница', routes::link('main_page')), 'modules');
        
    }

    public function login()
    {
        auth::factory()->login_admin(\GetPost::uget('email'), \GetPost::uget('password'));
        self::response();
    }


}
