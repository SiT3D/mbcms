<?php


namespace trud\admin\templates;

use trud\admin\templates\admin_work_parser\user_picker;
use trud\classes\model\companies;
use trud\classes\model\import_site_links;
use trud\classes\model\user;
use trud\classes\model\vacancies;

class admin_work_parser extends \Module implements \adminAjax
{
    public function init()
    {
        parent::init();

        $this->ADDM((new user_picker())->setUserType(user::ACCTYPE_EMPLOYER), '$modules1');
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new user_picker(),
        ];
    }

    /**
     * ajax
     */
    public function get_links()
    {
        $url = \GetPost::uget('url');

        if (!$url)
        {
            self::add_response('errors[]', 'Укажите URL компании с work.ua');
            self::response();
        }

        $parser = new \work_ua_parser($url);
        self::add_response('links', $parser->get_all_vacancy_links());
        self::response();
    }

    public function write_vacancy()
    {
        list($user_id, $url) = \GetPost::ar(['user_id', 'url'], true);

        if (!$url)
        {
            self::add_response('errors[]', 'Укажите URL компании с work.ua');
        }
        else
        {
            if ($user_id)
            {
                $user       = user::factory()->get_user($user_id)->get();
                $company_id = $user->companyid;
                if ($company_id)
                {
                    (new import_site_links())->add_vacancy(import_site_links::DOMEN_WORK_UA, $user_id, $url, $company_id);
                }
                else
                {
                    self::add_response('errors[]', 'К пользователю не привязана компания!');
                }
            }
            else
            {
                self::add_response('errors[]', 'Укажите пользователя');
            }
        }


        self::response();
    }

    public function write_company()
    {
        list($url, $user_id) = \GetPost::ar(['url', 'user_id'], true);

        if (!$url)
        {
            self::add_response('errors[]', 'Укажите URL компании с work.ua');
        }
        else
        {

            $check_company = (new import_site_links)
                ->check(import_site_links::TYPE_COMPANY, import_site_links::DOMEN_WORK_UA, \work_ua_parser::get_out_id($url), null)
                ->get();

            if ($check_company)
            {
                self::add_response('errors[]', 'Компания такая уже есть у нас! И связана она с work.ua. Ее id: ' . $check_company->our_id);
                self::response();
            }

            if ($user_id)
            {

                $company = (new companies())->get_company_by_user_id($user_id)->get();

                if (!$company)
                {
                    (new import_site_links())->add_company(import_site_links::DOMEN_WORK_UA, $user_id, $url);
                }
                else
                {
                    self::add_response('errors[]', 'У этого пользователя уже есть компания!');
                }

            }
            else
            {
                self::add_response('errors[]', 'Укажите пользователя');
            }
        }

        self::response();
    }

    /**
     * ajax
     */
    public function remove_old()
    {
        $company_id = \GetPost::uget('company_id');
        vacancies::factory()->remove_old_vacancies($company_id);
    }
}