<?php

namespace MBCMS\Forms;

use MBCMS\block;
use MBCMS\files;
use MBCMS\Forms\OPT\main_option;
use MBCMS\Forms\OPT\title;

class template extends main_form implements \adminAjax
{

    public function __construct($parent = null)
    {
        parent::__construct(main_form::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico templates KEY_A', $this);
        }
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new title(),
            new OPT\main_option(null, null),
            new block(),

        ];
    }

    public function init()
    {
        parent::init();

        $opt = new title('ШАБЛОН');
        $this->ADDM($opt, 'modules');

        $opt = block::factory('Генерация всех шаблонов', 'button', 'generate_all_templates');
        $this->ADDM($opt, 'modules');

        $opt = new title('idTemplate шаблона');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_setting('idTemplate'), 'idTemplate');
        $opt->hide_metric = true;
        $opt->type = 'text';
        $opt->readonly = true;
        $opt->value_width = OPT\main_option::MW_LARGE;
        $this->ADDM($opt, 'modules');

        $opt = new title('meta title');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_settingData('metatitle'), 'metatitle');
        $opt->hide_metric = true;
        $opt->type = main_option::TYPE_AREA;
        $this->ADDM($opt, 'modules');

        $opt = new title('meta description');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_settingData('metadescription'), 'metadescription');
        $opt->hide_metric = true;
        $opt->type = main_option::TYPE_AREA;
        $this->ADDM($opt, 'modules');

        $opt = new title('meta keywords');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_settingData('metakeywords'), 'metakeywords');
        $opt->hide_metric = true;
        $opt->type = main_option::TYPE_AREA;
        $this->ADDM($opt, 'modules');

        $opt = new title('Название');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_setting('title'), 'title');
        $opt->type = 'text';
        $opt->hide_metric = true;
        $opt->value_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

        $opt = new title('Класс модуля');
        $this->ADDM($opt, 'modules');


        $class_css = $this->get_settingData('__user_cms_class');
        $class_css = $class_css ? $class_css : 'this';

        $opt = new OPT\main_option($class_css, '__user_cms_class');
        $opt->hide_metric = true;
        $opt->type = 'text';
        $opt->value_width = OPT\main_option::MW_LARGE;
        $this->ADDM($opt, 'modules');

        $opt = new title('Дополнительные классы');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\main_option($this->get_settingData('__user_cms_dop_css_classes'), '__user_cms_dop_css_classes');
        $opt->hide_metric = true;
        $opt->type = 'text';
        $opt->value_width = OPT\main_option::MW_LARGEX;
        $this->ADDM($opt, 'modules');

        $opt = new title('Тип блока');
        $this->ADDM($opt, 'modules');

        $blocktype = $this->get_settingData('__cms_block_type') ? $this->get_settingData('__cms_block_type') : 'div';
        $opt = new OPT\main_option($blocktype, '__cms_block_type');
        $opt->metrix = ['', 'div', 'span'];
        $opt->type = 'text';
        $opt->value_width = OPT\main_option::MW_NORMAL;
        $this->ADDM($opt, 'modules');

    }

    public function update_settings()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $data = \GetPost::get('data');

        foreach ($data as &$__value)
        {
            $__value = OPT\main_option::array_to_string($__value, false);
        }

        $template = new \MBCMS\template();

        if (isset($data['__user_cms_class']))
        {
            $data['__user_cms_class'] = isset($data['__user_cms_class']) ? str_replace(['.', ', ', ':', ' '], '', $data['__user_cms_class']) : ' ';
        }

        $template->update_settings_by_id($idTemplate, $data);
    }

    /**
     * ajax
     */
    public function get_pages()
    {
        $pages = files::get_json(files::PATH_PAGES);

        self::add_response('pages', $pages);
        self::response();
    }

}
