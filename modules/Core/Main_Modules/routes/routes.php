<?php

namespace MBCMS;

use event\standart_security_login_event;
use GetPost;

class routes extends \Module
{

    private static $currentAdr           = '';
    private static $__set_admin          = null;
    private static $__is_static_status   = false;
    private static $__all_pages          = null;
    private static $__url_params         = [];
    private static $__trg_method         = '';
    private static $__current_route_name = null;

    public static function start()
    {

        if (!self::$__all_pages)
        {
            self::$__all_pages = files::get_json(files::PATH_PAGES);
        }

        $adr              = self::__get_url_adr();
        self::$currentAdr = $adr;

        if (self::is_ajax())
        {
            return self::ajax_page();
        }

        if (self::is_admin())
        {
            return self::admin_page($adr);
        }

        return self::get_module($adr);
    }

    private static function __get_url_adr()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function is_ajax()
    {
        $adr = self::__get_url_adr();

        return preg_match('~/ajax/?($|\?)~', $adr) ? true : false;
    }

    private static function ajax_page()
    {
        $className = GetPost::get('class');
        $redirect  = GetPost::get('redirect');

        $result = [];
        preg_match('~\->(.*)~', $className, $result);
        $method             = isset($result[1]) ? $result[1] : null;
        $className          = preg_replace('~\->(.*)~', '', $className);
        self::$__trg_method = $method;


        if (!$className)
        {
            die('Не указан класс обращения, передайте параметр class в GET или POST запросе, с указанием namespace и class php класса!');
        }

        $classNameN = str_replace('\\\\', '\\', $className);

        if (\GClass::autoLoad($classNameN))
        {
            $class = new $classNameN();

            $implaments = class_implements($class);

            if (in_array('ajax', $implaments))
            {

            }
            else if (in_array('adminAjax', $implaments) && configuration::factory()->is_superadmin())
            {

            }
            else
            {
                die('Нельзя обратиться к этому классу через ajax' . ' ' . $classNameN);
            }


            if ($method && method_exists($class, $method))
            {
                $cc = new \ReflectionMethod($class, $method);

                if ($cc->isPublic())
                {
                    $class->$method();
                }
                else
                {
                    die('Метод к которому вы обращаетесь не публичный!');
                }
            }
            else if (method_exists($class, 'ajax'))
            {
                $class->ajax();
            }
            else
            {
                die('Несуществующий метод для обращения! ' . $method);
            }
        }

        if ($redirect)
        {
            self::redirect($redirect);
        }
    }

    /**
     *
     * @param string $uri
     * @param string $code
     * @param bool $with_die = false если осуществляется редирект на другую страницу (не 404) то нужно обрывать выполнение кода через die()
     * @return bool
     */
    public static function redirect($uri, $code = null, $with_die = false)
    {
        if ($code === null)
        {
            $code = '302';
        }

        if (self::is_static_status() || routes::is_admin())
        {
            return false;
        }

        header('Location: ' . $uri, true, $code);

        if ($with_die)
        {
            die();
        }
    }

    /**
     * Проверяет происходит ли сейчас формирование статичного вида для шаблона, событие внутри движка, который формирует 1 файл вида, общий css и js
     *
     * @return bool
     */
    public static function is_static_status()
    {
        return self::$__is_static_status;
    }

    public static function is_admin()
    {
        if (self::$__set_admin)
        {
            return false;
        }

        $adr = self::__get_url_adr();


        if (
            preg_match('~\\' . configuration::factory()->get_constructor_url() . '/|' . configuration::factory()->get_constructor_url() . '$~', $adr) == 1
            || GetPost::get('mbcms_admin_status')
        )
        {
            \Module::add_response('is_admin', 'yes');
            \Module::add_response('mbcms_admin_status', GetPost::get('mbcms_admin_status', 'null'));
            \Module::add_response('is_admin_true', '1');

            return true;
        }


        return false;
    }

    private static function admin_page($adr)
    {
        if (!configuration::factory()->is_superadmin())
        {
            return p404::error404();
        }

        $vadr             = self::remove_admin_adr($adr);
        self::$currentAdr = $vadr;
        $module           = self::get_module($vadr);
        $adm              = new CMS;
        $adm->innerModule = $module;

        return $adm;
    }

    public static function remove_admin_adr($adr)
    {
        $adr = preg_replace('~\\' . configuration::factory()->get_constructor_url() . '/|' . configuration::factory()->get_constructor_url() . '$~i', '/', $adr, 1);

        $adr = empty($adr) ? '/' : $adr;
        $adr = preg_replace('~/$~', '', $adr);

        return $adr;
    }

    private static function get_module($adr)
    {
        $adr        = preg_replace('~/$~', '', $adr);
        $idTemplate = self::get_template_id($adr);

        if ($idTemplate == null)
        {
            return new p404();
        }
        else
        {
            if ($ret = \Module::ADDMT(null, $idTemplate))
            {
                return $ret;
            }
            else
            {
                return new p404();
            }
        }
    }

    /**
     * @param string $adr
     * @return string
     */
    public static function get_template_id($adr = null)
    {
        $name_page = $adr === null ? self::__get_url_adr() : $adr;
        $name_page = self::remove_GET_adr($name_page);
        $name_page = !empty($name_page) ? $name_page : '/';

        return self::database_get_id($name_page);
    }

    private static function remove_GET_adr($adr)
    {
        $adr = preg_replace("/\?.{0,}/", '', $adr);
        $adr = preg_replace("/\#.{0,}/", '', $adr);

        return $adr;
    }

    private static function database_get_id($name_page)
    {
        $name_page = urldecode($name_page);

        foreach (self::$__all_pages as $route_name => $page)
        {
            $page->unicalPageName = preg_replace_callback('~/$~', function ()
            {
                return '';
            }, $page->unicalPageName);

            if (preg_match("~$page->unicalPageName~i", $name_page, self::$__url_params))
            {
                self::$__current_route_name = $route_name;
                array_shift(self::$__url_params);

                return $page->idTemplate;
            }
        }

        return null;
    }

    /**
     * @param $uri $_SERVER['REQUEST_URI'] or full URL
     * @return string|null
     */
    public static function get_route_name_by_uri($uri)
    {
        $uri = str_replace($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '', $uri);

        foreach (self::$__all_pages as $route_name => $page)
        {
            if (preg_match("~$page->unicalPageName~i", $uri))
            {
                return self::$__current_route_name = $route_name;
            }
        }
    }

    /**
     *
     * @internal param bool $status
     */
    public static function set_admin_status()
    {
        self::$__set_admin = true;
    }

    public static function get_current_address()
    {
        return self::$currentAdr;
    }

    public static function remove_host($adr)
    {
        $adr = str_replace($_SERVER['HTTP_HOST'], '', $adr);
        $adr = str_replace('http://', '', $adr);
        $adr = str_replace('https://', '', $adr);

        return $adr;
    }

    public static function get_current_route()
    {
        return self::$__current_route_name;
    }

    public static function get_current_idTemplate()
    {
        return self::get_template_id(self::$currentAdr);
    }

    public static function set_gen_static_status()
    {
        self::$__is_static_status = true;
    }

    /**
     *
     * list($p1, $p2, $p3) = \MBCMS\routes::get_url_param([0, 1, 2]);
     * or
     * $p1 = \MBCMS\routes::get_url_param(0);
     *
     * @param array|int $index [0,1,2,3] или 0
     * @return type
     */
    public static function get_url_param($index)
    {
        if (is_array($index))
        {
            $ret = [];
            foreach ($index as $__index)
            {
                $ret[] = isset(self::$__url_params[$__index]) ? self::$__url_params[$__index] : null;
            }

            return $ret;
        }

        return isset(self::$__url_params[$index]) ? self::$__url_params[$index] : null;
    }

    /**
     * Сверяет его с целью запроса.
     *
     * @param $method __METHOD__ из которого вызывается данный запрет
     */
    public static function not_ajax($method)
    {
        if (self::is_ajax() && self::is_target_method($method))
        {
            die('Нельзя обратиться к данному методу! через ajax');
        }
    }

    /**
     * @param $method __METHOD__
     * @return mixed
     */
    public static function is_target_method($method)
    {
        return self::__get_route_method($method) == self::$__trg_method;
    }

    private static function __get_route_method($method)
    {
        return preg_replace_callback('~.*::~', function ()
        {
            return '';
        }, $method);
    }

    /**
     *
     * @param string $page_name pages-key
     * @param arguments
     * @return string
     */
    public static function link($page_name)
    {
        $params = func_get_args();
        array_shift($params);

        if (!self::$__all_pages)
        {
            self::$__all_pages = files::get_json(files::PATH_PAGES);
        }

        if (count(self::$__all_pages) && isset(self::$__all_pages->$page_name))
        {
            $route  = self::$__all_pages->$page_name;
            $i      = 0;
            $string = preg_replace_callback("~\(.*\)~Usi", function ($regular_path) use (&$i, $params)
            {
                $ret = isset($params[$i]) && !self::__is_get($params[$i]) ? $params[$i] : '';

                $regular_path[0] = str_replace(['.'], '', $regular_path[0]);
                $m               = [];
                preg_match("~$regular_path[0]~", $ret, $m);
                $i++;

                return isset($m[1]) ? $m[1] : '';
            }, $route->unicalPageName);

            $string = trim(urldecode(mb_strtolower($string)));
            $string = str_replace(['^', '$', '{0,1}', '\\', '\\\\', '|', '*', '.', '?'], '', $string);
            $string = str_replace(['/////', '////', '///', '//'], '/', $string);
            $string = str_replace(['  '], [' '], $string);

            return $string . self::__dop_get($params);
        }

        return '!none_route!';
    }

    /**
     * @param $string
     * @return int
     */
    private static function __is_get($string)
    {
        return preg_match('~\?~', $string);
    }

    /**
     * @param $params
     * @return null
     */
    private static function __dop_get($params)
    {
        foreach ($params as $param)
        {
            if (self::__is_get($param))
            {
                return $param;
            }
        }

        return null;
    }

}
