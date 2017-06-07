<?php

/**
 * ROOT DIR . DIRECTORY_SEPARATOR
 */
define('HOME_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

include_once HOME_PATH . '/modules/Core/globals.php';
include_once HOME_PATH . '/modules/Core/CMS/logger/logger.php';

$ttttttttttt = microtime_float();

$page = new MBCMS\Site();
\Modules::get($page);

foreach (\Modules::get_main_views() as $view)
{
    echo \MBCMS\Site\wrapper::propagation($view);
}

////////////// лог посещаемости и скорости работы сайта. //////////////////

if (!\MBCMS\routes::is_admin() && !\MBCMS\routes::is_ajax())
{
    $log_timer = microtime_float($ttttttttttt, 'time', false);
    $log_timer = round($log_timer, 3);

    logger::write('people', [
        'browser_time_to_ms' => $log_timer * 1000,
        'page'               => $_SERVER['REQUEST_URI'],
        'ip'                 => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
    ]);
}
