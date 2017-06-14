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
$log_timer = round($log_timer, 3);

if (!\MBCMS\routes::is_admin() && !\MBCMS\routes::is_ajax())
{
    logger::write('people', [
        'browser_time_to_ms' => $log_timer * 1000,
        'page'               => $_SERVER['REQUEST_URI'],
        'ip'                 => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
    ]);

    if (\MBCMS\configuration::factory()->is_dev_mod() && \trud\classes\auth::factory()->admin())
    {
        echo '<div style="z-index: 1000; position: fixed; background: #ffad4f; padding: 5px 20px; bottom: 0; left: 0; opacity: 0.5; pointer-events: none">
    mods: ' . Module::get_modules_connect_count() . ' time: '. ($log_timer * 1000) . 'ms</div>';
    }
}

