<?php


namespace trud\admin\templates\admin_work_parser;

use trud\classes\model\import_site_links;

class  user_picker extends \trud\form_element\user_picker implements \adminAjax
{

    protected $__options = [];
    protected $__users = [];


    public function get()
    {
        list($this->user_type, $this->filter_string) = \GetPost::ar(['type', 'string'], true);

        $this->__options = $this->__get_options();
        self::add_response('options', $this->__options);
        self::add_response('linksdb', $this->__get_links());
        self::response();
    }

    protected function __get_links()
    {
        $this->__get_users_id();
        return $this->__get_links_query();
    }

    protected function __get_users_id()
    {
        $this->__users = [];

        foreach ($this->__options as $option)
        {
            $user_id = isset($option['value']) ? $option['value'] : null;

            if ($user_id)
            {
                $this->__users[] = $user_id;
            }
        }
    }

    protected function __get_links_query()
    {
        $out_ids = [];

        foreach ($this->__users as $user_id)
        {
            $value = (new import_site_links())->get_company_link_by_user_id(import_site_links::DOMEN_WORK_UA, $user_id)->get();
            $out_ids[$user_id] = isset($value->out_id) ? \work_ua_parser::URL_COMPANY . $value->out_id : false;
        }

        return $out_ids;
    }
}