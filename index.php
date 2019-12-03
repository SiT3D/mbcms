<?php


/**
 * ROOT DIR . DIRECTORY_SEPARATOR
 */
define('HOME_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

include_once HOME_PATH . '/modules/Core/globals.php';


$ttttttttttt = microtime_float();

$page = new MBCMS\Site();
\Modules::get($page);

foreach (\Modules::get_main_views() as $view)
{
    echo \MBCMS\Site\wrapper::propagation($view);
}

////////////// лог посещаемости и скорости работы сайта. //////////////////

$log_timer = microtime_float($ttttttttttt, 'time', false);
\MBCMS\Site::site_info_panel($log_timer);
echo 'LUL';
