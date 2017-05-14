<?php

namespace trud\admin\templates;

use MBCMS\form\form;
use MBCMS\form\input;

class admin_vrabote_parser extends \Module implements \adminAjax
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new input(1),
        ];
    }

    public function init()
    {
        parent::init();

        $form = form::factory(__CLASS__ . '->parse', 'vrabote_parser');
        $this->ADDM($form, 'modules');

        $form->ADDM(input::factory('url')->setTitle('URL категории')->setClass('trud-input mini')->setPlaceholder('http://vrabote.ua/вакансии/ит/Украина'), 'modules');
        $form->ADDM(input::factory('title')->setTitle('Заголовок категории')->setClass('trud-input mini')->setPlaceholder('IT-КОМПЬЮТЕРЫ, ИНТЕРНЕТ'), 'modules');

        $form->ADDM(input::factory('sub')->setValue('Парсировать')->setType(input::TYPE_SUBMIT), 'modules');

    }

    public function parse()
    {
        list($url, $title) = \GetPost::ar(['url', 'title'], true);

        if (!$url || !$title)
        {
            form::errors([['Вы не указали url или категорию']]);
        }

        self::add_response('cat', [$url]);
        self::add_response('alias', [$title]);

        \Module::response();
    }

    public function get_all_categories()
    {
        list($url, $title) = \GetPost::uget('url', 'title');
//        list($links, $alias) = (new \vrabote_parser())->get_all_categories();

        \Module::add_response('categories', [$url]);
        \Module::add_response('categories_alias', [$title]);
        self::response();
    }

    public function parse_category()
    {
        $url = \GetPost::uget('url');

        list($links, $next_page) = (new \vrabote_parser())->get_page_vacancies($url);

        self::add_response('vacancies', $links);
        self::add_response('next_page', $next_page);
        self::response();

    }

    public function write_vacancy()
    {
        list($url, $category) = \GetPost::ar(['url', 'category'], true);

        $parser = new \vrabote_parser();
        $parser->parse_vacancy($url, $category);

        self::add_response('$url', $url);
        self::response();
    }
}