<?php

namespace trud\admin\templates;

use MBCMS\administration_page\header_element;
use MBCMS\administration_page\left_menu_element;
use MBCMS\block;
use MBCMS\routes;
use Plugins\scrollbar;
use trud\classes\model\confirmes;

class admin_menu extends block
{

    public function init_files()
    {
        return [
            parent::init_files(),
            new header_element,
            new left_menu_element(),
            new scrollbar(),
        ];
    }

    public function init()
    {
        parent::init();

        $this->__cms_block_type = 'empty';

        $item = new header_element();
        $item->title = 'Сайт главная';
        $item->href = routes::link('main_page');
        $this->ADDM($item, 'modules');

        $item = new header_element();
        $item->title = 'Работа';
        $this->ADDM($item, 'modules');

        $confirmes_count = (new confirmes)->get_all()->count();

        $item = left_menu_element::factory([
            [routes::link('admin_candidats'), 'Кандидаты'],
            [routes::link('admin_resumes'), 'Резюме'],
            [routes::link('admin_employers'), 'Работодатели'],
            [routes::link('admin_companies'), 'Компании'],
            [routes::link('admin_vacancies'), 'Вакансии'],
            [routes::link('admin_moderation'), 'Заявки на модерацию ' . ($confirmes_count ? "+$confirmes_count" : '')],
        ]);
        $this->ADDM($item, 'modules');

        $item        = new header_element();
        $item->title = 'Парсеры';
        $this->ADDM($item, 'modules');

        $item = left_menu_element::factory([
                    [routes::link('admin_work_parser'), 'work.ua'],
                    [routes::link('admin_vrabote_parser'), 'vrabote.ua'],
                    [routes::link('admin_xml_parser'), 'XML редакция'],
        ]);
        $this->ADDM($item, 'modules');


        $item        = new header_element();
        $item->title = 'Статьи';
        $this->ADDM($item, 'modules');

        $item = left_menu_element::factory([
            [routes::link('admin_news'), 'Статьи'],
            [routes::link('admin_news_categories'), 'Категории для статей'],
            [routes::link('admin_partners'), 'Партнеры'],
        ]);
        $this->ADDM($item, 'modules');


        $item        = new header_element();
        $item->title = 'Настройки и функции';
        $this->ADDM($item, 'modules');

        $item = left_menu_element::factory([
            [routes::link('admin_synonyms'), 'Управление синонимами'],
            [routes::link('admin_settings'), 'Глобальные настройки сайта'],
        ]);
        $this->ADDM($item, 'modules');


        $item = new header_element();
        $item->title = 'Еще добавить';
        $this->ADDM($item, 'modules');

        $item = left_menu_element::factory([
            ['#', 'Города'],
            ['#', 'Категории'],
            ['#', 'Платные услуги'],
            ['#', 'Настройки платных услуг'],
            ['#', 'Основные настройки сайта'],
            ['#', 'Отправка писем и рассылок'],
            ['#', 'Размещение банеров'],
        ]);
        $this->ADDM($item, 'modules');

        $item = new header_element();
        $item->title = 'Галерея';
        $item->href = routes::link('admin_galary');
        $this->ADDM($item, 'modules');

        $item = new header_element();
        $item->title = 'Выход';
        $item->href = routes::link('administration', '?fc=1');
        $this->ADDM($item, 'modules');

    }

}
