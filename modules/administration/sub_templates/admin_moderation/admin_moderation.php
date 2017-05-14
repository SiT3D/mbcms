<?php

namespace trud\admin\templates;

use Assets\ck_editor;
use MBCMS\block;
use MBCMS\form\select;
use MBCMS\routes;
use trud\classes\mailer;
use trud\classes\model\companies;
use trud\classes\model\confirmes;
use trud\classes\model\resumes;
use trud\classes\model\vacancies;
use trud\site_metrics;

class admin_moderation extends \Module implements \adminAjax
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new ck_editor(),
        ];
    }

    public function init()
    {
        parent::init();

        $this->__confirmes = (new confirmes)
            ->get_all()
            ->o('date DESC')
            ->get();

        $this->ADDM(new ck_editor(), 'modules');

        foreach ($this->__confirmes as $confirme)
        {
            $this->__set_data($confirme);
            $confirme->date = site_metrics::get_date_range($confirme->date);
        }

    }

    private function __set_data($confirme)
    {
        switch ($confirme->type)
        {
            case confirmes::TYPE_COMPANY :
                $confirme->source_src = routes::link('admin_companies', 'edit', '?id=' . $confirme->trg_id);
                $confirme->types      = "Компания (id: $confirme->trg_id)";
                break;
            case confirmes::TYPE_RESUME :
                $confirme->types      = "Резюме (id: $confirme->trg_id)";
                $confirme->source_src = routes::link('admin_resumes', 'edit', '?id=' . $confirme->trg_id);
                break;
            case confirmes::TYPE_VACANCY :
                $confirme->types      = "Вакансия (id: $confirme->trg_id)";
                $confirme->source_src = routes::link('admin_vacancies', 'edit', '?id=' . $confirme->trg_id);
                break;
        }
    }

    public function ajax_remove_confirme()
    {
        (new confirmes())->remove(\GetPost::uget('id'));
        $this->__send_email(\GetPost::uget('id'));
        self::response();
    }

    public function ajax_send_message()
    {
        $this->__send_email(\GetPost::uget('id'));
    }

    private function __send_email($id)
    {
        $confirme = (new confirmes())->get_by_id($id)->get();

        if ($confirme)
        {
            $message = block::factory(\GetPost::get('message'));
            mailer::send($this->__get_email($confirme), $this->__get_subject($confirme), $message);
        }

        self::response();
    }

    private function __get_email($confirme)
    {
        switch ($confirme->type)
        {
            case confirmes::TYPE_COMPANY:
                return companies::factory()->get_by_id($confirme->trg_id)->lj('t_users', 't_users.id = t_companies.companyfounder')->is_mono()->limit(1)->get()->uname;
            case confirmes::TYPE_VACANCY:
                return vacancies::factory()->get_by_id($confirme->trg_id)->lj('t_users', 't_users.id = t_vacancies.userid')->is_mono()->limit(1)->get()->uname;
            case confirmes::TYPE_RESUME:
                return resumes::factory()->get_by_id($confirme->trg_id)->lj('t_users', 't_users.id = t_resumes.userid')->is_mono()->limit(1)->get()->uname;
        }
    }

    private function __get_subject($confirme)
    {
        switch ($confirme->type)
        {
            case confirmes::TYPE_COMPANY:
                return 'Подтверждение компании на сайте trud.net';
            case confirmes::TYPE_VACANCY:
                return 'Подтверждение вакансии на сайте trud.net';
            case confirmes::TYPE_RESUME:
                return 'Подтверждение резюме на сайте trud.net';
        }
    }
}