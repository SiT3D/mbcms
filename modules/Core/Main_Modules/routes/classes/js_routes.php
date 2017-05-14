<?php

namespace MBCMS\routes;

class js_routes implements ajax
{
    public function link() /// НЕНЕНЕНЕНЕНЕ только моделировать поведение в js! А тут при инициализации просто получать все роуты, в массив не более!
    {
        $route_name = \GetPost::uget('name');
        $params = \GetPost::uget('params');
        
        $link = call_user_func_array("MBCMS\routes::link", array_unshift($params, $route_name));
        
        \Module::add_response('link', $link);
        \Module::response();
    }
}
