<?php

/**
 * ROOT DIR . DIRECTORY_SEPARATOR
 */
define('HOME_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

include_once HOME_PATH . '/modules/Core/globals.php';

$page = new MBCMS\Site();
\Modules::get($page);

foreach (\Modules::get_main_views() as $view)
{
    echo \MBCMS\Site\wrapper::propagation($view);
}

