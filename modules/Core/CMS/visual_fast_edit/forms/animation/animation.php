<?php


namespace MBCMS\Forms;

use MBCMS\Forms\animation\ui;
use MBCMS\Forms\OPT\main_option;
use MBCMS\Forms\OPT\title;

class animation extends main_form implements \adminAjax
{
    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico border_ico KEY_SHIFT KEY_S', $this, 'view');
        }
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new ui(),
        ];
    }

    public function init()
    {
        parent::init();

        $this->ADDM(new title('Имя анимации'), 'modules');

        $this->ADDM(
            (new main_option($this->get_settingData('animation_name'), 'animation_name'))
                ->setType(main_option::TYPE_TEXT)
                ->setValueWidth(main_option::MW_LARGE)
                ->hideMetric()
            , 'modules');

        $this->ADDM(new title('Кадры'), 'modules');

        $this->ADDM(new ui(), 'modules');

        $this->ADDM(new title('Время выполнения'), 'modules');

        $this->ADDM(
            (new main_option($this->get_settingData('animation_time'), 'animation_time'))
                ->setStep(0.1)
                ->hideMetric()
            , 'modules');

        $this->ADDM(new title('Время задержки перед анимацией'), 'modules');

        $this->ADDM(
            (new main_option($this->get_settingData('animation_delay'), 'animation_delay'))
                ->setStep(0.1)
                ->hideMetric()
            , 'modules');

        $this->ADDM(new title('Время выполнения'), 'modules');

        $this->ADDM(
            (new main_option($this->get_settingData('animation_type'), 'animation_type'))
                ->hideValue()
                ->setMetrix([
                    'ease',
                    'linear',
                    'ease-in',
                    'ease-out',
                    'ease-in-out',
                    'step-start',
                    'step-end',

                ]), 'modules');


        $this->ADDM(new title('Количество повторений'), 'modules');

        $this->ADDM(
            (new main_option($this->get_settingData('animation_count'), 'animation_count'))
                ->setStep(1)
                ->hideMetric()
            , 'modules');


        $this->ADDM(new title('Разновидность движения'), 'modules');

        $this->ADDM(
            (new main_option($this->get_settingData('animation_direction'), 'animation_direction'))
                ->hideValue()
                ->setMetrix([
                    'alternate',
                    'alternate-reverse',
                    'normal',
                    'reverse',
                ]), 'modules');

    }

    public function generate_animation()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $out_index  = \GetPost::get('out_index');

        $data = \MBCMS\output::get_module_output_data_by_id($idTemplate, $out_index);


        if (isset($data['data']['animation_name']))
        {
            $framekeys = $this->__get_classes_content($idTemplate, $data['data']['animation_name']);

            $animation_full_name = "{$idTemplate}_{$data['data']['animation_name']}";
            $result_content_css  = "@keyframes {$animation_full_name} {";

            for ($i = 0; $i < count($framekeys); $i++)
            {
                $framekey = $framekeys[$i];

                if ($i == 0)
                {
                    $result_content_css .= "from $framekey";
                }
                else if ($i == count($framekeys) - 1)
                {
                    $result_content_css .= "to $framekey";
                }
                else
                {
                    $persentage         = (int)(100 / count($framekeys) - 1) * $i;
                    $result_content_css .= "{$persentage}% $framekey";
                }
            }

            $result_content_css .= "}
            .{$animation_full_name}
            {
                animation: {$animation_full_name} {$data['data']['animation_time']}s {$data['data']['animation_count']} {$data['data']['animation_type']};
                animation-direction: {$data['data']['animation_direction']};
                animation-delay: {$data['data']['animation_delay']}s;
            }
            ";

            file_put_contents($this->__folder . DIRECTORY_SEPARATOR . $animation_full_name . '.css', $result_content_css);
        }

    }

    private function __get_classes_content($idTemplate, $animation_name)
    {
        $d         = self::get_module_cms_data_by_id($idTemplate);
        $className = $d['name'];

        if (\GClass::autoLoad($className))
        {
            $folder         = \GClass::$classInfo['folder'] . '/css';
            $this->__folder = $folder;
            $files          = [];

            if (file_exists($folder))
            {
                $files = scandir($folder);
            }

            foreach ($files as $file)
            {
                if (preg_match("~{$animation_name}_kf~", $file))
                {
                    $content = file_get_contents($folder . DIRECTORY_SEPARATOR . $file);
                    preg_match('~({.*})~Usi', $content, $m);
                    $content  = isset($m[1]) ? $m[1] : '';
                    $result[] = $content;
                }
            }
        }

        return $result;
    }

    public function get_keyframes()
    {
        list($idTemplate, $animation_name) = \GetPost::ar(['idTemplate', 'animation_name'], true);

        $d         = self::get_module_cms_data_by_id($idTemplate);
        $className = $d['name'];
        $result    = [];

        if (\GClass::autoLoad($className))
        {
            $folder = \GClass::$classInfo['folder'] . '/css';
            $files  = [];

            if (file_exists($folder))
            {
                $files = scandir($folder);
            }

            foreach ($files as $file)
            {
                if (preg_match("~{$animation_name}_kf~", $file))
                {
                    $result[] = str_replace('.css', '', $file);
                }
            }
        }

        self::add_response('result', $result);
        self::response();
    }

    /**
     * template animate_template_class_fc_number
     */
    public function clone_class()
    {
        $idTemplate     = \GetPost::get('idTemplate');
        $css_class      = \GetPost::get('css_class');
        $animation_name = \GetPost::get('animation_name');


        $d         = self::get_module_cms_data_by_id($idTemplate);
        $className = $d['name'];

        if (\GClass::autoLoad($className))
        {
            $folder = \GClass::$classInfo['folder'] . '/css';
            $files  = [];

            if (file_exists($folder))
            {
                $files = scandir($folder);
            }

            foreach ($files as $file)
            {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'css' && $file == $css_class . '.css')
                {
                    $new_name = preg_replace_callback('~_kf([0-9])+~', function ($string)
                    {
                        if (isset($string[1]))
                        {
                            return '_kf' . (string)((int)$string[1] + 1);
                        }
                    }, $file);

                    if (!preg_match('~_kf~', $new_name))
                    {
                        $new_name = preg_replace_callback('~\.css~', function ($string) use ($animation_name)
                        {
                            if ($string)
                            {
                                return "_{$animation_name}_kf1" . '.css';
                            }
                        }, $new_name);
                    }

                    copy($folder . DIRECTORY_SEPARATOR . $file, $folder . DIRECTORY_SEPARATOR . $new_name);
                    self::add_response('newname', str_replace('.css', '', $new_name));

                    $content = file_get_contents($folder . DIRECTORY_SEPARATOR . $new_name);
                    file_put_contents($folder . DIRECTORY_SEPARATOR . $new_name, str_replace([$css_class, '.css'], [$new_name, ''], $content));
                }
            }
        }

        self::response();
    }
}