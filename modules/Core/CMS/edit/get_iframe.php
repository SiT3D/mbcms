<?php

namespace MBCMS;

class get_iframe implements \adminAjax
{

    public function ajax()
    {
        $isrc = \GetPost::get('src');
        $adr = \MBCMS\routes::remove_admin_adr($isrc);; // брать адрес из роутов
        
        $iframe = new Iframe;
        $iframe->isrc = $adr;
        $iframe->set_main_module(1,1);
    }

}
