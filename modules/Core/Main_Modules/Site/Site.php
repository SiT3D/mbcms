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

    public static $TIMES  = [];
    public static $TIMERS = [];
    public        $json;

    public static function START()
    {
        self::$TIMERS[] = microtime_float();
    }

    public static function END($key)
    {
        self::$TIMES[$key] = isset(self::$TIMES[$key]) ? self::$TIMES[$key] : 0;
        self::$TIMES[$key] += microtime_float(array_pop(self::$TIMERS), 'time', false);
    }

    /**
     * @param $log_timer
     */
    public static function site_info_panel($log_timer)
    {
        $log_timer = round($log_timer, 3);

        if (!\MBCMS\routes::is_admin() && !\MBCMS\routes::is_ajax())
        {
            \logger::write('people', [
                'browser_time_to_ms' => $log_timer * 1000,
                'page'               => $_SERVER['REQUEST_URI'],
                'ip'                 => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            ]);

            if (\MBCMS\configuration::factory()->is_dev_mod() && \trud\classes\auth::factory()->admin())
            {
                $bdinfo   = \MBCMS\DB::get_info();
                $sqltime  = round($bdinfo[0], 3) * 1000;
                $sqlcount = $bdinfo[2];
                $clear_log_timer = $log_timer * 1000 - $sqltime;

                echo "<style>
            .MBCMS-infotable.open
            {
                height: 99%;
                width: 98%;
                padding: 0;
                padding-left: 2%;
                padding-top: 1%;
                opacity: 1;
                overflow: auto;
            }
            
            .MBCMS-infotable
            {
                z-index: 1000; 
                height: 20px;
                position: fixed; 
                background: #ffad4f; 
                padding: 5px 20px;
                bottom: 0; 
                left: 0;
                opacity: 0.5;
                width: 350px;
                cursor: pointer;
                border-top-right-radius: 10px;
            }
            
            .MBCMS-infotable pre
            {
                border-top: 1px solid #eee;
                padding-top: 20px;
            }
            
            .MBCMS-infotable:hover
            {
                opacity: 1;
            }
            
            body.help-content-close
            {
                overflow: hidden;
            }
</style>";

                echo '
                    <div class="MBCMS-infotable">
                    mods: ' . \Module::get_modules_connect_count() . ' time: ' . ($log_timer * 1000). "({$clear_log_timer})" . 'ms; SQL ' . $sqlcount . ' за ' . $sqltime . 'ms
                    <pre>
                        '. $bdinfo[1] .'
                    </pre>
                    </div>
                    ';


                echo "
                <script>
                    $('.MBCMS-infotable')
                    .click(function()
                    {
                        $(this).toggleClass('open'); 
                        $('body').toggleClass('help-content-close');
                    });
                
                </script>
                ";
            }
        }
    }

    public function init()
    {
        event_listners::init();

        $this->view_prioritet_index(-10);

        $resultModule = routes::start();


        $wp = new wrapper();
        $wp::setPageTitle(isset($resultModule->CMSData['settingsData']['metatitle']) && $resultModule->CMSData['settingsData']['metatitle']
            ? $resultModule->CMSData['settingsData']['metatitle'] : '');
        $wp::setPageDescription(isset($resultModule->CMSData['settingsData']['metadescription']) && $resultModule->CMSData['settingsData']['metadescription']
            ? $resultModule->CMSData['settingsData']['metadescription'] : '');
        $wp::setPageMetawords(isset($resultModule->CMSData['settingsData']['metakeywords']) && $resultModule->CMSData['settingsData']['metakeywords']
            ? $resultModule->CMSData['settingsData']['metakeywords'] : '');


        if ($resultModule)
        {
            $this->ADDM($resultModule, 'modules');
        }


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
                'is_admin'            => (boolean) configuration::factory()->is_superadmin(),
            ]);
        }


    }

}
