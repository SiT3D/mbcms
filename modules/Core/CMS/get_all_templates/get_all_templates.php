<?php

namespace MBCMS;

use MBCMS\GetAllTemplates\finder;
use MBCMS\GetAllTemplates\folder;

class get_all_templates extends block
{

    public function ajax()
    {
        $this->set_main_module(1, 1);
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new finder(),
            new folder(),
            new template_element(),
        ];
    }

    function init()
    {
        $this->create_folders();
        $templates = $this->getTemplatesList();

        foreach ($templates as $template)
        {
            $m = new \MBCMS\template_element();
            $m->clone_settings($template);
            $this->ADDM($m, 'modules');
        }

    }

    /**
     * Получает список всех существующих шаблонов из базы данных
     *
     * @return array mixed массив с необходимыми свойствами модуля
     */
    private function getTemplatesList()
    {
        $path  = \GetPost::get('path', '');
        $count = \GetPost::get('count', '');

        $count = $count ? ' LIMIT ' . $count : '';

        $this->pathes  = explode(DIRECTORY_SEPARATOR, $path);
        $this->path    = $path;
        $all_templates = template::get_all_templates();
        $ret           = array();

        foreach ($all_templates as $idTemplate)
        {
            $module = self::get_module_cms_data_by_id($idTemplate, true);
            $mpath  = isset($module->path) ? $module->path : '';

            if ($mpath == $path && $module)
            {
                $result_desc = isset($module->description) && $module->description !== '' ? $module->description : '';
                $result_name = isset($module->title) && $module->title !== '' ? $module->title : '';
                $result      = array();

                $result['name']       = $module->name;
                $result['desc']       = $result_desc;
                $result['idTemplate'] = $module->idTemplate;
                $result['title']      = $result_name;
                $result['css_class']  = isset($module->settingsData['class']) ? $module->settingsData['class'] : '';

                $ret[] = $result;
            }
        }

        return $ret;
    }

    private function create_folders()
    {
        $path = \GetPost::get('path', DIRECTORY_SEPARATOR);
        $path = !$path ? DIRECTORY_SEPARATOR : $path;

        $folders = GetAllTemplates\folder_actions::get_all_folders();

        foreach ($folders as $fpath => $folderInfo)
        {
            if ($folderInfo->path == $path)
            {
                $folder           = new GetAllTemplates\folder;
                $folder->path     = $folderInfo->path;
                $folder->name     = $folderInfo->name;
                $folder->fullPath = str_replace('//', DIRECTORY_SEPARATOR, $fpath);
                $this->ADDM($folder, 'modules');
            }
        }
    }

    public static function create_new_module_php($newIdTemplate = null, $class_name = 'User\\tblock')
    {
        if (\GClass::autoLoad($class_name))
        {
            $folder = MPATH . 'templates';

            // создаем папку модуля и файл внутри
            $module_path = $folder . DIRECTORY_SEPARATOR . $newIdTemplate;

            if (file_exists($module_path))
            {
                return null;
            }

            mkdir($module_path);

            // не ннадо передавать $class_name скорее всего.. 
            $ftext = self::__get_php_text($newIdTemplate, 'User\\tblock');

            file_put_contents($module_path . DIRECTORY_SEPARATOR . $newIdTemplate . '.php', $ftext);
            // копирование css

            self::__copy_css_files(\GClass::$classInfo['folder'], $folder . DIRECTORY_SEPARATOR . $newIdTemplate, $newIdTemplate, $newIdTemplate, $class_name);

            return \GClass::$classInfo['namespace'] . '\\' . $newIdTemplate;
        }
    }

    static function copy_css_files($idTemplate_copy, $idTemplate_paste, $list)
    {
        $dcopy  = self::get_module_cms_data_by_id($idTemplate_copy);
        $dpaste = self::get_module_cms_data_by_id($idTemplate_paste);

        if (\GClass::autoLoad($dcopy['name']))
        {
            $folder = \GClass::$classInfo['folder'];
        }

        if (\GClass::autoLoad($dpaste['name']))
        {
            $new_folder = \GClass::$classInfo['folder'];
        }

        if (file_exists($new_folder) && file_exists($folder))
            self::__copy_css_files($folder, $new_folder, \GClass::$classInfo['name'], $dpaste['idTemplate'], $dcopy['name'], $list);
    }

    private static function __copy_css_files($folder, $new_folder, $new_module_name, $newIdTemplate, $old_css_name, $list = false)
    {
        // замена класса css!!!
        \GClass::getClassInfo($old_css_name);
        $old_css_name = preg_replace('~\\\\~', '__', \GClass::$classInfo['namespace'] . '\\' . \GClass::$classInfo['name']);
        $new_css_name = preg_replace('~\\\\~', '__', \GClass::$classInfo['namespace'] . '\\' . $new_module_name);

        $css_dir     = $folder . '/css';
        $new_css_dir = $new_folder . '/css';
        $files       = [];
        if (file_exists($css_dir))
        {
            $files = scandir($css_dir);
        }


        if (!empty($files) && !file_exists($new_css_dir))
        {
            mkdir($new_css_dir);
        }

        $list = $list ? $list : [];

        foreach ($list as &$__item)
        {
            $__item .= '.css';
        }

        $list = is_array($list) ? array_flip($list) : $list;



        foreach ($files as $file)
        {
            $filename  = $css_dir . DIRECTORY_SEPARATOR . $file;
            $copy_file = true;

            if ($list && !isset($list[$file]))
                $copy_file = false;

            if (is_file($filename) && $copy_file)
            {
                $content = file_get_contents($filename);
                $content = preg_replace('~\.' . $old_css_name . '~', '.' . $new_css_name, $content);
                if ($newIdTemplate)
                    $content = preg_replace('~parentidtemplate=\"(\d*)\"~', 'parentidtemplate="' . $newIdTemplate . '"', $content);
                file_put_contents($new_css_dir . DIRECTORY_SEPARATOR . $file, $content);
            }
        }
    }

    static function delete_css_files($idTemplate, $list)
    {
        $dcopy = self::get_module_cms_data_by_id($idTemplate);

        if (\GClass::autoLoad($dcopy['name']))
        {
            $folder = \GClass::$classInfo['folder'] . '/css/';
        }

        if (file_exists($folder))
        {
            foreach ($list as $css)
            {
                $path = $folder . $css . '.css';
                if (file_exists($path))
                    unlink($path);
            }
        }
    }

    static function clone_css_files($idTemplate, $list)
    {
        $dcopy = self::get_module_cms_data_by_id($idTemplate);

        if (\GClass::autoLoad($dcopy['name']))
        {
            $folder = \GClass::$classInfo['folder'] . '/css/';
        }

        if (file_exists($folder))
        {
            foreach ($list as $css)
            {
                $path       = $folder . $css . '.css';
                $clone_path = $folder . $css . '_cloned.css';
                $content    = file_get_contents($path);
                $content    = preg_replace('~\.' . $css . '~', '.' . $css . '_cloned', $content);
                if (file_exists($path))
                    file_put_contents($clone_path, $content);
            }
        }
    }

    private static function __get_php_text($new_module_name, $class_name)
    {
        return '<?php

namespace ' . \GClass::$classInfo['namespace'] . ';

class ' . $new_module_name . ' extends \\' . $class_name . ' {}
    ';
    }

}
