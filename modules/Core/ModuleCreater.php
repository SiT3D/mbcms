<?php

namespace MBCMS;

use GClass;

class ModuleCreater extends \Modules
{

    const IGNOR = 'THIS_NODULE_IGNOR_INIT';
    public static  $AAA                   = 0;
    private static $VIEWS                 = [];
    private static $__files_check         = [];
    private static $__find_parent         = [];
    private static $__not_views           = [];
    private        $this_module;
    private        $URL                   = '';

    public static function call_after()
    {
        foreach (self::$OBJECTS as $id => $object)
        {

            if (self::__is_dinamic($object))
            {

                if (method_exists($object, 'after_init') && self::__is_dinamic($object) && $object->__cms_module_position != \Module::NO_POSITION)
                {
                    $object->after_init();
                }
            }
        }
    }

    private static function __is_dinamic($module)
    {

        if ($module->is_ignore_logic())
        {
            return false;
        }

        if (!self::__is_static($module) || routes::is_static_status()) // todo: поидеи это ни к чему, просто возвращать true после первой проверки!
        {
            return true;
        }

        return false;
    }

    /**
     *
     * @param $module
     * @param $ignore_admin = false
     * @return boolean
     */
    public static function __is_static($module, $ignore_admin = false)
    {
        if (routes::is_admin() && !$ignore_admin)
        {
            return false;
        }

        if (GClass::getClassInfo(get_class($module)) && file_exists(GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . \MBCMS\template::__STATIC_VIEW . '.php'))
        {
            return true;
        }

        return false;
    }

    //////////////////////////FILES AND VIEWS///////////////////////////////////

    public static function call_preview()
    {
        foreach (self::$OBJECTS as $object)
        {
            if (method_exists($object, 'preview') && self::__is_dinamic($object) && $object->__cms_module_position != \Module::NO_POSITION)
            {
                $object->preview();
            }

        }
    }

    public static function form_views()
    {
        $map = self::get_structure_map();

        self::recursive_views($map, -1);
        self::$MAIN_VIEWS = isset(self::$VIEWS[-1]) ? self::$VIEWS[-1] : [];

        // перебрать мейн вьюз по прриоритетам
        $newView        = [];
        $hightPrioritet = -999999;
        foreach (self::$MAIN_VIEWS as $id => $view)
        {
            $viewPrioritet             = isset(self::$OBJECTS[$id]) ? self::$OBJECTS[$id]->view_prioritet_index() : 0;
            $hightPrioritet            = $viewPrioritet > $hightPrioritet ? $viewPrioritet : $hightPrioritet;
            $newView[$viewPrioritet][] = $view;
        }
        self::$MAIN_VIEWS = isset($newView[$hightPrioritet]) ? $newView[$hightPrioritet] : null;
    }

    private static function recursive_views(&$map, $parentID = 0)
    {
        $map = $map ? $map : [];

        foreach ($map as $id => &$innerMap)
        {
            if (empty($innerMap))
            {
                unset($map[$id]);
            }

            if (!empty($innerMap))
            {
                unset($map[$id]);
                self::recursive_views($innerMap, $id);
            }
            self::$VIEWS[$parentID][$id] = self::get_module_view_mc($id);
        }
    }

    /**
     * @param integer $id
     * @return View|null
     */
    public static function get_module_view_mc($id)
    {

        $object = self::$OBJECTS[$id];

        if (!$object->is_render())
        {
            return null;
        }

        if ($ignore_view = $object->is_ignore_logic())
        {
            return View::factory($ignore_view, (array)$object);
        }

        $view_name = self::get_my_class_view_name($object);
        self::add_current_view($object);

        if ($view_name !== null)
        {
            if ($object->static_nature() && routes::is_static_status())
            {
                return $object->static_nature();
            }

            return View::factory($view_name, (array)$object);
        }

        return null;
    }

    private static function get_my_class_view_name($object)
    {
        $className = get_class($object);

        if (isset(self::$__find_parent[$className]))
        {
            return self::$__find_parent[$className];
        }

        if (GClass::autoLoad($className))
        {
            $folder = GClass::$classInfo['folder'];

            if (self::__is_static($object) && file_exists($folder . DIRECTORY_SEPARATOR . \MBCMS\template::__STATIC_VIEW . '.php'))
            {
                return $folder . DIRECTORY_SEPARATOR . \MBCMS\template::__STATIC_VIEW;
            }

            $view_name = self::find_my_view_name($folder);

            if ($view_name !== null)
            {
                self::$__find_parent[$className] = $view_name;

                return $view_name;
            }
        }


        $parent = get_parent_class($object);

        if ($parent !== 'Module' && GClass::autoLoad($parent))
        {
            $folder    = GClass::$classInfo['folder'];
            $view_name = self::find_my_view_name($folder);
            if ($view_name === null)
            {
                return self::get_my_class_view_name(new $parent);
            }
            else
            {
                self::$__find_parent[$className] = $view_name;

                return $view_name;
            }
        }

        return null;
    }

    private static function find_my_view_name($folder)
    {

        if (isset(self::$__not_views[$folder]))
        {
            return null;
        }

        if (!file_exists($folder))
        {
            return null;
        }

        if (file_exists(GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . '~' . GClass::$classInfo['name'] . '~.php'))
        {
            return GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . '~' . GClass::$classInfo['name'] . '~';
        }

        $files     = scandir($folder);
        $view_name = null;

        foreach ($files as $file)
        {
            $filename = $folder . DIRECTORY_SEPARATOR . $file;
            $pregtrue = preg_match('~\.php~i', $filename);
            if ($file !== '.' && $file != '..' && $file != \MBCMS\template::__STATIC_VIEW . '.php' && $file !== GClass::$classInfo['name'] . '.php' && is_file($filename) && $pregtrue === 1)
            {
                $view_name = GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . str_replace('.php', '', $file);
                break;
            }
        }

        return $view_name;
    }

    private static function add_current_view($object)
    {
        $id = $object->get_my_id();

        if (isset(self::$VIEWS[$id]))
        {
            $currentPrioritet = isset(self::$OBJECTS[$id]) ? self::$OBJECTS[$id]->view_prioritet_index() : 0;
            $range            = isset(self::$OBJECTS[$id]) ? self::$OBJECTS[$id]->get_prioritet_range() : 9999;
            foreach (self::$VIEWS[$id] as $mid => $view)
            {
                $childrenPrioritet = isset(self::$OBJECTS[$mid]) ? self::$OBJECTS[$mid]->view_prioritet_index() : 0;
                if ($currentPrioritet <= $childrenPrioritet + $range)
                {
                    $position          = isset(self::$OBJECTS[$mid]->__cms_module_position) && self::$OBJECTS[$mid]->__cms_module_position !== '' ? self::$OBJECTS[$mid]->__cms_module_position : 'NONE_POSITION_FOR_MODULES';
                    $object->$position = isset($object->$position) ? $object->$position : [];

                    array_push($object->$position, $view);
                    unset(self::$VIEWS[$id]);
                }
            }
        }
    }

    public static function add_in_global_object($module)
    {
        self::$OBJECTS[self::$GLOBAL_THIS_CONNECTION_MODULE_ID] = $module;
        self::$GLOBAL_THIS_CONNECTION_MODULE_ID++;
        $module->set_my_ids(self::$GLOBAL_THIS_CONNECTION_MODULE_ID);
    }

    /**
     * Подключает модуль в рамках данного запроса, собирает детей присваивает всем id
     * и сохраняет в массив все имеющиеся в данном запросе объекты модулей.
     *
     * @param Module $this_module модуль
     * @return null
     */
    public function connect_module($this_module)
    {


        if ($this_module->get_my_id() !== null)
        {
            return null;
        }


        $this->URL         = 'http://' . $_SERVER['HTTP_HOST'];
        $this->this_module = $this_module;

        return $this->call_module_class();
    }

    private function call_module_class()
    {
        $className = get_class($this->this_module);

        if (GClass::autoLoad($className))
        {

            $this->this_module->set_id(self::$GLOBAL_THIS_CONNECTION_MODULE_ID);
            self::$OBJECTS[self::$GLOBAL_THIS_CONNECTION_MODULE_ID] = $this->this_module;
            self::$GLOBAL_THIS_CONNECTION_MODULE_ID++;


            if (method_exists($this->this_module, 'init_files') && self::__is_dinamic($this->this_module) && !\Modules::is_second_init())
            {
                $modules = $this->this_module->init_files();
                self::__ADDM_module($this->this_module, $modules);
            }

            if (method_exists($this->this_module, 'init') && self::__is_dinamic($this->this_module) && $this->this_module->__cms_module_position != \Module::NO_POSITION)
            {
                $this->this_module->init();
            }

            if (method_exists($this->this_module, 'get_editor_modules') && self::__is_dinamic($this->this_module))
            {
                $this->this_module->get_editor_modules();
            }
        }
    }

    private static function __ADDM_module($object, $modules)
    {
        if (is_array($modules))
        {
            foreach ($modules as $module)
            {
                if (is_array($module))
                {
                    self::__ADDM_module($object, $module);
                }
                else if ($module && $module instanceof \Module)
                {
                    $object->ADDM($module, \Module::NO_POSITION);
                }
            }
        }
    }

    /**
     * Собирает файлы и виды модулей js|css|php
     */
    public function get_modules_files()
    {
        if (!self::$first)
        {
            return;
        }

        foreach (self::$OBJECTS as $object)
        {
            $className = get_class($object);

            if (GClass::autoLoad($className) && !$object->is_not_files() && !isset(self::$__files_check[$className]))
            {
                self::$__files_check[$className] = true;
                $modulePath                      = GClass::$classInfo['folder'];
                $this->get_module_files($modulePath, $className, $object);
            }
        }
    }

    private function get_module_files($modulePath, $className, $object = null)
    {
        $mfiles = [];


        if (self::__is_static($object) && !routes::is_static_status())
        {
            $staticCss     = self::__is_static($object) ? $this->get_files_by_type($modulePath, DIRECTORY_SEPARATOR . template::__STATIC__ . '/css', $className, 'css') : [];
            $mfiles['css'] = array_merge(isset($mfiles['css']) ? $mfiles['css'] : [], $staticCss);

            $staticBJ            = self::__is_static($object) ? $this->get_files_by_type($modulePath, DIRECTORY_SEPARATOR . template::__STATIC__ . '/bottom_js', $className, 'js') : [];
            $mfiles['bottom_js'] = array_merge(isset($mfiles['bottom_js']) ? $mfiles['bottom_js'] : [], $staticBJ);

            $staticTJ         = self::__is_static($object) ? $this->get_files_by_type($modulePath, DIRECTORY_SEPARATOR . template::__STATIC__ . '/top_js', $className, 'js') : [];
            $mfiles['top_js'] = array_merge(isset($mfiles['top_js']) ? $mfiles['top_js'] : [], $staticTJ);
        }
        else
        {
            $css           = $this->get_files_by_type($modulePath, '/css', $className, 'css');
            $editCss       = routes::is_admin() ? $this->get_files_by_type($modulePath, '/edit/css', $className, 'css') : [];
            $mfiles['css'] = array_merge($css, $editCss);

            $bottom_js           = $this->get_files_by_type($modulePath, '/bottom_js', $className, 'js');
            $editBJ              = routes::is_admin() ? $this->get_files_by_type($modulePath, '/edit/bottom_js', $className, 'js') : [];
            $mfiles['bottom_js'] = array_merge($bottom_js, $editBJ);

            $top_js           = $this->get_files_by_type($modulePath, '/top_js', $className, 'js');
            $editTJ           = routes::is_admin() ? $this->get_files_by_type($modulePath, '/edit/top_js', $className, 'js') : [];
            $mfiles['top_js'] = array_merge($top_js, $editTJ);
        }

        foreach (self::$FILES as $key => &$typeArray)
        {
            $typeArray = array_merge($typeArray, isset($mfiles[$key]) ? $mfiles[$key] : []);
        }
    }

    /**
     * /css | /edit/css | /bootom_js | etc
     * @param $modulePath
     * @param $pathType
     * @param $className
     * @param $etc
     * @return array
     */
    private function get_files_by_type($modulePath, $pathType, $className, $etc)
    {
        $ret   = [];
        $dir   = $modulePath . $pathType;
        $files = files::get_files_in_dir($dir, $etc);
        $list  = null;

        $__list_path = $modulePath . $pathType . DIRECTORY_SEPARATOR . 'list.json';

        if (file_exists($__list_path))
        {
            $list = json_decode(file_get_contents($__list_path));
            $list = is_array($list) ? $list : [$list];
        }

        $module = str_replace(HOME_PATH, DIRECTORY_SEPARATOR, $modulePath);

        foreach ($files as $file)
        {
            if ($list && !in_array($file, $list))
            {
                continue;
            }

            $metafile              = [];
            $metafile['name']      = $file;
            $metafile['metapath']  = 'http://' . $_SERVER['HTTP_HOST'] . $module . $pathType . DIRECTORY_SEPARATOR . $file;
            $metafile['derictory'] = $dir;
            $metafile['classname'] = $className;
            $metafile['template']  = GClass::$classInfo['name'];
            $ret[]                 = $metafile;
        }

        return $ret;
    }

}
