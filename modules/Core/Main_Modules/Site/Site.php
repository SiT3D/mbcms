<?php

namespace MBCMS;

use event\event;
use event\site\load;
use MBCMS\Site\wrapper;

defined('HOME_PATH') or die('No direct script access.');

if (routes::is_admin() && !configuration::factory()->is_superadmin())
{
    die();
}

class Site extends \Module
{

    public $json;

    public function init()
    {
        event_listners::init();

        $this->view_prioritet_index(-10);

        $resultModule = routes::start();

        if ($resultModule)
        {
            $this->ADDM($resultModule, 'modules');
        }

        $wp = new wrapper();
        $wp::setPageTitle(isset($resultModule->CMSData['settingsData']['metatitle']) && $resultModule->CMSData['settingsData']['metatitle']
            ? $resultModule->CMSData['settingsData']['metatitle'] : '');
        $wp::setPageDescription(isset($resultModule->CMSData['settingsData']['metadescription']) && $resultModule->CMSData['settingsData']['metadescription']
            ? $resultModule->CMSData['settingsData']['metadescription'] : '');
        $wp::setPageMetawords(isset($resultModule->CMSData['settingsData']['metakeywords']) && $resultModule->CMSData['settingsData']['metakeywords']
            ? $resultModule->CMSData['settingsData']['metakeywords'] : '');
        $wp->wrap_around_target($this, 'modules');
        $wp->view_prioritet_index(-10);

        $this->__modules();

        \GClass::update_file();

        event::factory(new load())->call();


        if (!routes::is_admin())
        {
            $this->json = json_encode([
                'is_static_templates' => configuration::factory()->is_static_templates(),
                'idTemplate'          => routes::get_current_idTemplate(),
                'is_admin'            => configuration::factory()->is_superadmin(),
            ]);
        }


    }

}
