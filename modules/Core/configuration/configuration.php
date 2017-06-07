<?php

namespace MBCMS;

use trud\classes\auth;

class configuration extends \Autoload implements \adminAjax
{

    const TEMPORAL_FOLDER = 'tmp';
    private static $__conf = null;
    private $__db_config = [
        'host' => 'localhost', // тестовый сервер
        'database' => 'trudOe',
        'username' => 'u_trudOe',
        'password' => 'ytp6oRdM',
    ];

    /**
     * Режимы разработки true-продакшен, live-тест на статичных шаблонах(F5; ctrl + F5 в браузере при live), false-чистый тест без сборок(низкая производительность)
     * @var string|boolean true|false|live
     */
    private $__static_templates = 'live';
    /**
     * адрес конструктора
     * @var string
     */
    private $adm_constructor = '/viewer';

    /**
     *
     * @return configuration
     */
    public static function factory()
    {
        routes::not_ajax(__METHOD__);

        self::$__conf = self::$__conf ? self::$__conf : new configuration();
        return self::$__conf;
    }

    public function is_static_templates()
    {
        return $this->__static_templates;
    }

    public function is_dev_mod()
    {
        return $this->is_static_templates() === 'live' || $this->is_static_templates() === false;
    }

    public function get_db_config()
    {
        routes::not_ajax(__METHOD__);
        return $this->__db_config;
    }

    public function get_constructor_url()
    {
        return $this->adm_constructor;
    }

    public function is_superadmin()
    {
        return auth::factory()->admin();
    }

    public function is_superadmin_access_die()
    {
        if (!$this->is_superadmin())
        {
            die();
        }
    }


}

ini_set('error_reporting', E_ALL);
ini_set('display_errors', (int) !(new configuration())->is_static_templates() || (new configuration())->is_static_templates() === 'live');
ini_set('display_startup_errors', 1);
ini_set('opcache.enable_cli', 1);
