<?php

namespace MBCMS;

class get_all_modules_window implements \adminAjax
{

    const TYPE_TEMPLATE = 'TYPE_TEMPLATE';
    const TYPE_OUTPUT   = 'TYPE_OUTPUT';

    /**
     *
     * @var string имя класса, в который подключается output, необходим чтобы получить take_list()
     */
    private $classParent;

    public function ajax()
    {
        $type              = \GetPost::get('filter_type', self::TYPE_TEMPLATE);
        $idParentTemplate  = \GetPost::get('idTemplate');
        $info              = \Module::ADDMT(null, $idParentTemplate);
        $this->classParent = isset($info->CMSData['name']) ? $info->CMSData['name'] : null;

        $modules = $this->get_files(MPATH, $type);

        $modules_form          = new modules_form();
        $modules_form->modules = $modules;

        $modules_form->set_main_module(1, 1); // выводим форму
    }

    private function get_files($path, $type = self::TYPE_TEMPLATE)
    {
        $array           = array();
        $files           = scandir($path);
        $folderNameArray = explode(DIRECTORY_SEPARATOR, $path);
        $folderName      = array_pop($folderNameArray);
        $listType        = $this->getTypeName($type);

        foreach ($files as $file)
        {
            $fp = $path . DIRECTORY_SEPARATOR . $file;
            if ($this->folders_filter($fp, $file))
            {
                if (file_exists($fp . DIRECTORY_SEPARATOR . $file . '.php') && strtolower($folderName) === $listType)
                {
                    $namespace = gGetNameSpace($fp . DIRECTORY_SEPARATOR . $file . '.php', true);
                    $className = $namespace . '\\' . $file;

                    $array[$file]['name'] = $className;

                    if (\GClass::autoLoad($className))
                    {
                        $class                 = new $className;
                        $array[$file]['alias'] = $class->take_alias();
                    }
                }
                else
                {
                    $array[$file] = $this->get_files($fp, $type);
                }
            }
        }

        $resultArray = array_filter($array, function($var)
        {
            return (!empty($var));
        });


        return $resultArray;
    }

    private function folders_filter($dir, $name)
    {
        if (file_exists($dir) && is_dir($dir) && $name !== '.' && $name !== '..' &&
                $name !== 'css' && $name !== 'bottom_js' &&
                $name !== 'top_js' && $name !== 'edit')
        {
            return true;
        }

        return false;
    }

    private function getTypeName($type)
    {
        if ($type === self::TYPE_TEMPLATE)
        {
            return 'templates';
        }
        elseif ($type === self::TYPE_OUTPUT)
        {
            return 'outputs';
        }

        return '.';
    }

}
