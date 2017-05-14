<?php

namespace trud\conn\admin;

use Assets\jQuery;
use MBCMS\administration_page;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\Forms\deleter;
use MBCMS\Forms\output;
use MBCMS\mbcms_assets;
use MBCMS\routes;
use Plugins\choosen_select;
use Plugins\scrollbar;
use trud\admin\templates\admin_auth_panel;
use trud\admin\templates\admin_menu;
use trud\classes\auth;
use trud\conn\connector as connectortrud;
use trud\form_element\categories_picker;
use trud\form_element\city_picker;
use trud\form_element\user_picker;
use trud\standart;

/**
 * Class connector
 * @package trud\conn\admin
 * Все элементы передавать в $admin
 * self::$admin->add_content(Module $module);
 */
class connector extends \MBCMS\block
{

    /**
     *
     * @var administration_page
     */
    protected static $admin = null;

    public function init_files()
    {
        return [
            parent::init_files(),
            new mbcms_assets(),
            new jQuery(),
            new scrollbar(),
            new administration_page(),
            new admin_menu(),
            new admin_auth_panel(),
            new user_picker(),
            new input(null),
            new categories_picker(),
            new city_picker(),
            new form(),
            new standart(),
        ];
    }

    public function init()
    {
        if (routes::is_admin())
        {
            $this->fast_edit($this, [
                new output($this),
                new deleter($this)
            ]);
        }

        parent::init();

        if (!auth::factory()->admin())
        {
            self::$admin = new connector_not_admin;
            $panel       = new admin_auth_panel();
            $this->ADDM($panel, 'modules');
        }
        else
        {
            self::$admin = new administration_page();
            $this->ADDM(self::$admin, 'modules');
            self::$admin->ADDM(new admin_menu(), 'items');
        }
    }

    public function static_nature()
    {
        return $this->__static_nature();
    }

}


/**
 * Заглушка...
 */
class connector_not_admin 
{

    public function add_content()
    {
        
    }

}
