<?php

namespace trud\admin\templates;

use MBCMS\files;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\form\select;

class admin_xml_parser extends \Module implements \adminAjax
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new form(),
            new select(null),
            new input(null),
        ];
    }

    public function init()
    {
        parent::init();

        $form = form::factory(__CLASS__ . '->load', 'load_xml_file_to_parse');
        $this->ADDM($form, 'modules');

        $opt = (new select('city_id'))
            ->setOptions([
                ['value' => '', 'title' => 'none'],
                ['value' => '419', 'title' => 'Одесса'],
                ['value' => '415', 'title' => 'Киев'],
                ['value' => '433', 'title' => 'Запорожье'],
                ['value' => '417', 'title' => 'Днепр'],
                ['value' => '416', 'title' => 'Харьков'],
                ['value' => '499', 'title' => 'Краматорск'],
                ['value' => '428', 'title' => 'Винница'],
                ['value' => '429', 'title' => 'Житомир'],
                ['value' => '437', 'title' => 'Кропивницкий'],
            ])->setChosen(false);
        $form->ADDM($opt, 'modules');

        $opt = input::factory('file', '', input::TYPE_FILE);
        $form->ADDM($opt, 'modules');

        $opt = input::factory('sub', 'Загрузить и обработать', input::TYPE_SUBMIT);
        $form->ADDM($opt, 'modules');
    }

    public function load()
    {
        $city_id = \GetPost::uget('city_id');
        $pathes = files::upload(HOME_PATH . 'tmp', 'file', ['xml'], 1024 * 1024 * 100);

        foreach ($pathes as $path)
        {
            \xml_parser::start($path, $city_id);
            unlink($path);
        }

        if (files::is_upload_errors())
        {
            self::add_response('upload_errors', files::get_upload_errors());
        }

        self::response();
    }
}