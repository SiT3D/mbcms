<?php

use MBCMS\ModuleCreater;

defined('HOME_PATH') or die('No direct script access.');

define('MPATH', preg_replace('~\\\\$~', DIRECTORY_SEPARATOR, HOME_PATH) . 'modules' . DIRECTORY_SEPARATOR);

include_once MPATH . 'Core/CMS/files/files.php';
include_once MPATH . 'Core/CMS/logger/logger.php';

function echo_attrs($__cms_attrs, $group = 'main')
{
    $group = !$group ? 'main' : $group;
    if (isset($__cms_attrs[$group]))
    {
        foreach ($__cms_attrs[$group] as $mattr)
        {
            echo $mattr;
        }
    }
    else if ($group === 0)
    {
        foreach ($__cms_attrs as $mattrs)
        {
            foreach ($mattrs as $mattr)
            {
                echo $mattr;
            }
        }
    }
}

/**
 * Выбирает из результатов, одинаковые $key например одинаковые id вакансий, ищет в них
 * $merge_keys и объединяет в 1 запись, где id => значения и обэекты с указанными ключами, в которых
 * список индексированный, всех встреченных значений.
 *
 * <b>НЕ ПРИМЕНЯТЬ К РЕЗУЛЬТАТУ С КЛЮЧАМИ, только к массиву массивов, поидеи =)</b>
 *
 * тоесть если выбрать все вакансии для компании с id 1 и объединить их по vacid то получим 1 запись,  внутри которой
 * будет массив с vacid вместо n записей, 1 запись с n ваканси id<br>
 *
 * <b>Например</b><br>
 * <b>для ['name_ru', 'urlkey']</b><br>
 * <?php foreach ($vacancy->urlkey as $key => $urlkey) : ?> <br>
 * <a href="/rabota/{{$urlkey or ''}}" class="wcity">{{$vacancy->name_ru[$key]}}</a><br>
 * <?php endforeach; ?><br>
 * * <b>Например</b><br>
 * <b>для ['name_ru', 'urlkey']</b><br>
 * <?php for ($i = 0; $i < count($vacancy->name_ru); $i++) : ?> <br>
 * <a href="/rabota/{{$vacancy->urlkey[$i]}}" class="wcity">{{$vacancy->name_ru[$i]}}</a><br>
 * <?php endfor; ?><br>
 * несколько неудобно, но это все что я придумал на скорую руку, можно конечно указывать общий ключ, и собирать их туда..
 * но сейчас некогда
 *
 * @param object $result
 * @param $merge_keys массив ключей, которые буду объединяться в объект например ['company_name']
 * на выдаче получим объект {id => {value, velue, company_name => {0 => c1, 1=> c2}}}
 * @param $key - ключ по которому идентифицируются записи, по умолчанию id
 * @param bool $distinct (= true) если true то исключать одинаковые значения, если false то нет
 * @param bool $is_object (= true) если true вернет объект, если false то вернет массив объектов
 * @return array|stdClass
 */
function __many($result, $merge_keys, $key = 'id', $distinct = true, $is_object = true)
{

    $return            = new \stdClass();
    $__merged_values   = [];
    $__distinct_values = [];


    foreach ($result as $values)
    {
        $__key = isset($values->$key) ? $values->$key : null;

        if ($__key !== null)
        {
            if (!isset($return->$__key))
            {
                $return->$__key = $values;
            }

            foreach ($return->$__key as $__ret_key => $__value)
            {
                if (in_array($__ret_key, $merge_keys))
                {
                    if (isset($__distinct_values[$__key]) && in_array($__value, $__distinct_values[$__key]) && $distinct)
                    {

                    }
                    else if (!$distinct)
                    {
                        $__merged_values[$__key][$__ret_key][] = isset($values->$__ret_key) ? $values->$__ret_key : null;
                    }
                    else
                    {
                        $__merged_values[$__key][$__ret_key][] = isset($values->$__ret_key) ? $values->$__ret_key : null;
                    }

                    $__distinct_values[$__key][] = $__value;
                }
            }
        }
    }

    foreach ($__merged_values as $index => $new_values)
    {
        foreach ($merge_keys as $merge_key)
        {
            $return->$index->$merge_key = isset($new_values[$merge_key]) ? $new_values[$merge_key] : null;
        }
    }

    return $is_object ? $return : (array)$return;
}

/**
 *
 * @param $var
 * @param $attr 'class=' or 'display:' or 'id=' or 'margin-left:'
 * @param $render
 * @return string
 */
function attr(&$var, $attr, $render = true)
{
    if (isset($var) && !is_object($var) && !is_array($var) && trim((string)$var) !== '')
    {
        if (preg_match('~\:~', $attr) == 1)
        {
            if ($render)
            {
                echo $attr . ' ' . trim($var) . '; ';
            }
            else
            {
                return $attr . ' ' . trim($var) . '; ';
            }
        }
        else if (preg_match('~\=~', $attr) == 1)
        {
            if ($render)
            {
                echo $attr . '"' . trim($var) . '" ';
            }
            else
            {
                return $attr . '"' . trim($var) . '" ';
            }
        }
        else
        {
            if ($render)
            {
                echo $attr . ' ="' . trim($var) . '" ';
            }
            else
            {
                return $attr . ' ="' . trim($var) . '" ';
            }
        }
    }

    return '';
}

/**
 * Памятка как собирать количество единиц между даатами
 *
 * @param string $d1 - дата из будущего в формате Y-m-d H:i:s
 * @param string $d2 - дата из прошлого в формате Y-m-d H:i:s
 * @param string $key - y,m,d,h,i,s
 * @return int
 */
function date_range($d1, $d2, $key)
{
    $d1 = $d1 === null ? date('Y-m-d', time()) : $d1;
    $d1 = new \DateTime($d1);
    $d2 = new \DateTime($d2);

    return $d1->diff($d2)->{$key};
}

/**
 * Выводит виды из указанной переменной
 *
 * @param array $position - свойство модуля <br>
 * Позиция хранит массив с видами, вид можно получить через \Modules::get($module, true); второй параметр вернет вид<br>
 * Любому модулю, можно задать в любую позицию массив таких видов
 *
 * Например: $module->myPosition = array( <br>
 *      \Modules::get(new myModule(), true),<br>
 *      \Modules::get(new myModule(), true),<br>
 * );
 *
 * В самом $module в его файле вида, написать <?php echo_modules($myPosition); ?>
 * И туда будут выведены 2 вида из модуля myModule
 */
function echo_modules(&$position)
{
    if (isset($position) && is_array($position))
    {
        foreach ($position as $m)
        {
            if (!is_array($m))
            {
                echo $m;
            }
        }
    }
}

/**
 * @param $var
 * @param $default
 */
function isset_echo(&$var, $default = '')
{
    if (isset($var) && !is_array($var) && !is_object($var))
    {
        echo strip_tags($var);
    }
    else
    {
        echo $default;
    }
}

/**
 * @param $variale - значение или возможный массив значений
 * @param $search_value - значение которое необходимо найти, "эталонное значение" которое будет удовлетворять условию, 1 для checkbox например
 * @param $message
 * @param $key если использование $data->key приводит к ошибке @no result@ или как то так, то ключ вписывать сюда
 * @return boolean
 */
function selected(&$variale, $search_value, $message = null, $key = null)
{
    $value = null;

    if (is_array($search_value))
    {
        foreach ($search_value as $__val)
        {
            selected($variale, $__val, $message, $key);
        }
    }

    if (isset($variale))
    {
        if (is_object($variale))
        {
            $value = $key && isset($variale->{$key}) ? $variale->{$key} : $variale;
            $value = is_object($value) ? (array)$value : $value;
        }
        else if (is_array($variale))
        {
            $value = $key && isset($variale[$key]) ? $variale[$key] : $variale;
        }
        else
        {
            $value = (string)$variale;
        }

        if ($search_value === '!E' && empty($value))
        {
            return false;
        }
        else if ($search_value === '!E' && !empty($value))
        {
            if ($message)
            {
                echo $message;
            }

            return true;
        }

        if (is_array($value))
        {
            $__search = in_array($search_value, $value);

            if ($message && $__search)
            {
                echo $message;
            }

            return $__search;
        }
        else if (!is_array($search_value) && (string)$search_value === (string)$value)
        {
            if ($message)
            {
                echo $message;
            }

            return true;
        }
    }

    return false;
}

/**
 * Разрешает обращение к классу через ajax
 */
interface ajax
{

}

/**
 * Разрешает обращение к классу через ajax
 * но только при успешном прохождение авторизации суперадмина configuration
 */
interface adminAjax
{

}

class GClass
{

    /**
     *
     * @var array массив с информацией по последнему подключеному классу. namespace + className + folderPath
     */
    public static  $classInfo;
    public static  $TIME                   = 0;
    private static $allClasses             = [];
    private static $__files_classes        = [];
    private static $__files_classes_update = false;
    private static $exc                    = [];

    public static function getExc()
    {
        return self::$exc;
    }

    public static function getClassInfo($className)
    {
        self::autoLoad($className);

        if (isset(self::$allClasses[$className]))
        {
            self::$classInfo = self::$allClasses[$className]['classinfo'];

            return true;
        }

        self::$classInfo = null;

        return false;
    }

    static function autoLoad($className)
    {

        if (isset(self::$allClasses[$className]) && class_exists($className))
        {
            self::$classInfo = self::$allClasses[$className]['classinfo'];

            return true;
        }


        if (count(self::$__files_classes) == 0)
        {
            self::$__files_classes = \MBCMS\files::get_json(\MBCMS\files::PATH_CLASSES);

            foreach (self::$__files_classes as $__class)
            {
                self::$allClasses[$__class->class]['classinfo']             = self::nameNamespace($__class->class, $__class->script);
                self::$allClasses[$__class->class]['classinfo']['filename'] = $__class->script;
                self::$allClasses[$__class->class]['classinfo']['view']     = $__class->view;
            }
        }

        if ($className === '' || $className === null)
        {
            self::$classInfo = null;

            return false;
        }

        $filename    = isset(self::$allClasses[$className]['classinfo']['filename']) ? self::$allClasses[$className]['classinfo']['filename'] : '';
        $sql         = new stdClass();
        $sql->script = $filename;
        $sql->class  = $className;

        if (isset($sql->script))
        {
            if ($sql->script === '' || $sql->script === '0' || !file_exists($sql->script))
            {
                $filename = self::recursiveFinder($className);
            }
            elseif (file_exists($sql->script))
            {
                $filename = $sql->script;
            }
        }
        else
        {
            $filename = self::recursiveFinder($className);
        }

        $connect = self::connect($filename);
        self::updateDB($connect, $sql, $filename, $className, $sql->script);

        if (!$connect && !isset(self::$exc[$className]) && trim($className) !== '')
        {
            self::$exc[$className] = 'Не найден класс, ' . $className;
        }
        else if ($connect && !isset(self::$allClasses[$className]))
        {
            self::$allClasses[$className]['classinfo'] = self::$classInfo;
        }

        return $connect;
    }

    private static function nameNamespace($className, $path = '')
    {
        $path = self::get_normal_path($path);

        $exp          = explode('\\', $className);
        $classNamePop = array_pop($exp);
        $namespace    = trim(implode('\\', $exp));

        $parray = explode(DIRECTORY_SEPARATOR, $path);
        array_pop($parray);
        $folderPath = implode(DIRECTORY_SEPARATOR, $parray);

        return ['namespace' => $namespace, 'name' => $classNamePop, 'folder' => $folderPath];
    }

    public static function get_normal_path($path)
    {
        $path         = str_replace([DIRECTORY_SEPARATOR, '\\'], DIRECTORY_SEPARATOR, $path);
        $current_path = str_replace(HOME_PATH, '', $path);
        $current_path = str_replace('\\', DIRECTORY_SEPARATOR, $current_path);
        $left_path    = preg_replace('~\\\$|/$~', '', HOME_PATH);

        return str_replace('//', DIRECTORY_SEPARATOR, $left_path . DIRECTORY_SEPARATOR . $current_path);
    }

    private static function recursiveFinder($className)
    {
        unset(self::$__files_classes->$className);

        $class        = self::nameNamespace($className);
        $classNamePop = $class['name'];
        $namespace    = $class['namespace'];

        $result = self::recFind(MPATH, $classNamePop, $namespace);

        return realpath($result);
    }

    private static function recFind($path, $className, $namespace) // return filename or false
    {
        if (file_exists($path) && is_dir($path))
        {
            $filename = self::find($path, $className, $namespace);
            if (!$filename)
            {
                return self::findNextFolder($path, $className, $namespace);
            }

            return $filename;
        }


        return false;
    }

    private static function find($path, $className, $namespace) // return filename or false
    {
        $files = scandir($path);

        $findResult = false;

        foreach ($files as $file)
        {

            $findResult = self::deepFind($path, $className, $namespace, $file);

            if ($findResult !== false)
            {
                return $findResult;
            }
        }

        return $findResult;
    }

    private static function deepFind($path, $className, $namespace, $file)
    {
        $fpath = $path . DIRECTORY_SEPARATOR . $file;

        if (strtolower($file) === strtolower($className) . '.php' && $namespace == '' && self::getNameSpace($fpath, true) == '')
        {
            return $fpath;
        }
        elseif (strtolower($file) === strtolower($className) . '.php' && $namespace !== '' && self::containtNamespace($fpath, $namespace))
        {
            return $fpath;
        }

        return false;
    }

    private static function getNameSpace($patch, $string = false)
    {
        $retResult = [];
        if (file_exists($patch) && is_file($patch))
        {
            $file   = fopen($patch, 'r');
            $text   = fread($file, filesize($patch));
            $result = [];
            preg_match_all('~\Wnamespace\s+(.*);~', $text, $result);
            fclose($file);
            $retResult = $result[1];
        }

        if ($string)
        {
            return isset($retResult[0]) ? $retResult[0] : '';
        }

        return $retResult;
    }

    private static function containtNamespace($patch, $namespace) // ret bool
    {
        if (file_exists($patch) && is_file($patch))
        {
            $result = self::getNameSpace($patch);
            foreach ($result as $name)
            {
                if ($namespace === $name)
                {
                    return true;
                }
            }
        }

        return false;
    }

    private static function findNextFolder($patch, $className, $namespace) // return filename or false
    {
        $files    = scandir($patch);
        $filename = false;
        foreach ($files as $file)
        {
            $newPatch = $patch . DIRECTORY_SEPARATOR . $file;
            $newPatch = str_replace(['//', '\\\\'], DIRECTORY_SEPARATOR, $newPatch);
            if (is_dir($newPatch) && self::dirFilter($file))
            {
                $filename = self::recFind($newPatch, $className, $namespace);
                if ($filename !== false)
                {
                    return $filename;
                }
            }
        }

        return $filename;
    }

    private static function dirFilter($filename)
    {
        if ($filename !== '.' && $filename !== '..' && $filename !== 'css' && $filename !== 'bottom_js' && $filename !== 'top_js')
        {
            return true;
        }

        return false;
    }

    private static function connect($filename)
    {
        if (file_exists($filename) && !is_dir($filename))
        {
            include_once($filename);

            return true;
        }

        return false;
    }

    private static function updateDB($find, $sql, $filename, $className, $script)
    {
        if (isset($sql->script))
        {
            if (!$find)
            {
                self::$__files_classes_update = true;
                unset(self::$__files_classes->$className);
            }
            elseif ($filename !== $script && $filename !== false)
            {
                self::$__files_classes_update = true;
                self::reg($className, $filename);
            }
        }
        elseif ($find)
        {
            self::reg($className, $filename);
        }
    }

    private static function reg($className, $scriptPath = '')
    {
        if (!isset(self::$__files_classes->$className) || self::$__files_classes_update)
        {
            $ar = explode(DIRECTORY_SEPARATOR, $scriptPath);
            array_pop($ar);

            $data_array = [
                'name'   => @array_pop(@explode('\\', $className)),
                'folder' => implode(DIRECTORY_SEPARATOR, $ar),
            ];

            self::$__files_classes_update      = true;
            self::$__files_classes->$className = ['class' => $className, 'script' => $scriptPath, 'view' => self::getModuleView($data_array)];
            self::update_file();
        }
    }

    private static function getModuleView($data) // todo: искать нужно только для модулей!
    {

        $folder = $data['folder'];


        if (file_exists($folder . DIRECTORY_SEPARATOR . '~' . $data['name'] . '~.php'))
        {
            return $folder . DIRECTORY_SEPARATOR . '~' . $data['name'] . '~';
        }

        if (!file_exists($folder))
        {
            return false;
        }

        $files     = scandir($folder);
        $view_name = null;

        foreach ($files as $file)
        {
            $filename = $folder . DIRECTORY_SEPARATOR . $file;
            $pregtrue = preg_match('~\.php~i', $filename);
            if ($file !== '.' && $file != '..' && $file != '__static_view.php' && $file !== $data['name'] . '.php' && is_file($filename) && $pregtrue === 1)
            {
                $view_name = $folder . DIRECTORY_SEPARATOR . str_replace('.php', '', $file);
                break;
            }
        }

        return $view_name;
    }

    /**
     * Обновляет список всех классов, если были внесены изменения
     */
    public static function update_file()
    {
        if (self::$__files_classes_update)
        {
            MBCMS\files::set_json(MBCMS\files::PATH_CLASSES, self::$__files_classes);
        }
    }

}

class Autoload
{

    static function myAutoloader($class)
    {
        if (!class_exists($class))
        {
            \GClass::autoLoad($class);
        }
    }
}

function gGetNameSpace($path, $string = false)
{
    $retResult = [];
    if (file_exists($path) && is_file($path))
    {
        $file   = fopen($path, 'r');
        $text   = fread($file, filesize($path));
        $result = [];
        preg_match_all('~\Wnamespace\W(.*);~', $text, $result);
        fclose($file);
        $retResult = $result[1];
    }

    if ($string)
    {
        return isset($retResult[0]) ? $retResult[0] : '';
    }

    return $retResult;
}

spl_autoload_register(['Autoload', 'myAutoloader']);

class Modules extends \Autoload
{

    protected static $GLOBAL_THIS_CONNECTION_MODULE_ID = 0;
    /**
     * @var array
     */
    protected static $OBJECTS                      = [];
    protected static $FILES                        = ['css' => [], 'top_js' => [], 'bottom_js' => []];
    protected static $MAIN_VIEWS                   = [];
    protected static $STRUCTURE_MAP                = [];
    protected static $first                        = true;
    private static   $ALL_THIS_CONNECTION_GET_NUMB = 0;
    private static   $listner_functions            = [];
    private static   $lastStructure                = [];

    /**
     * Формирует запрос на получение вида модуля, без сбора файлов, для вторичных запросов на виды
     * ВНИМАНИЕ!!!! Если добавить до этого запроса, в модуль блок, не из его init методов,
     * а просто со стороны, то не вернет вид! Поэтому нкжно передавать массив модулей! вторым параметром
     *
     * @param \MBCMS\module|Module $module
     * @param array $connect_modules - только модулям в массиве нужно устанавливать позицию __cms_module_position
     * @return view
     */
    public static function get_module_view(\Module $module, $connect_modules = [])
    {
        self::$first = false;

        \Modules::__clear_struct();

        $module->view_prioritet_index(1, 1);
        $module->THIS_CONNECTION_PARENT_ID = null;
        $module->THIS_CONNECTION_MODULE_ID = null;
        $module->__cms_module_position     = null;

        foreach ($connect_modules as $m)
        {
            $module->ADDM($m, $m->__cms_module_position);
        }

        \Modules::get($module);

        $views = \Modules::get_main_views();


        return isset($views[0]) ? $views[0] : '';
    }

    private static function __clear_struct()
    {
        self::$OBJECTS                          = [];
        self::$FILES                            = [];
        self::$MAIN_VIEWS                       = [];
        self::$STRUCTURE_MAP                    = [];
        self::$ALL_THIS_CONNECTION_GET_NUMB     = 0;
        self::$GLOBAL_THIS_CONNECTION_MODULE_ID = 0;
    }

    /**
     * Запускает механизм сбора модулей. Запускает структурирование и формирование видов.
     * После чего можно обращаться к функциям Module::get_all_modules и другим..
     *
     * @param array $modules массив с объектами Module
     * усли false - вернет массив, с данными по модулю, его видом, и файлами которые он подключает
     */
    public static function get($modules = null)
    {
        self::$ALL_THIS_CONNECTION_GET_NUMB++;

        \MBCMS\Site::START();

        if (is_array($modules))
        {
            foreach ($modules as $module)
            {
                \MBCMS\Site::START();
                $mc = new ModuleCreater();
                $mc->connect_module($module);
                \MBCMS\Site::END('modele creater and connect 1');
            }
        }
        elseif (is_object($modules))
        {
            \MBCMS\Site::START();
            $mc = new ModuleCreater();
            $mc->connect_module($modules);
            \MBCMS\Site::END('modele creater and connect');
        }


        if ($modules)
        {
            self::$ALL_THIS_CONNECTION_GET_NUMB--;
            self::post_connect_actions();
        }

        \MBCMS\Site::END('get');
    }

    private static function post_connect_actions()
    {

        if (self::$ALL_THIS_CONNECTION_GET_NUMB === 0)
        {
            ModuleCreater::call_after();

            self::form_structure_map();

            if (!\Modules::is_second_init())
            {
                $mc = new ModuleCreater();
                $mc->get_modules_files();
            }


            ModuleCreater::call_preview();
            ModuleCreater::form_views();
        }
    }

    public static function form_structure_map()
    {

        self::$STRUCTURE_MAP = [];
        foreach (self::$OBJECTS as $id => $module)
        {

            $parentId  = $module->get_my_parent_id();
            $parentIdv = $parentId !== null ? $parentId : -1;

            self::$STRUCTURE_MAP[$parentIdv][$id] = [];
        }

        foreach (self::$STRUCTURE_MAP as $id => &$qs)
        {
            foreach ($qs as $childId => $qs2)
            {
                if (isset(self::$STRUCTURE_MAP[$childId]))
                {
                    $qs[$childId] = self::$STRUCTURE_MAP[$childId];
                    unset(self::$STRUCTURE_MAP[$childId]);
                }
            }
        }

        for ($i = 0; count(self::$STRUCTURE_MAP) > 1; $i++)
        {
            if (self::$lastStructure === self::$STRUCTURE_MAP)
            {
                break;
            }

            self::$lastStructure = self::$STRUCTURE_MAP;
            self::sub_sort(self::$STRUCTURE_MAP);
        }
    }

    private static function sub_sort(&$currentStructure)
    {
        foreach ($currentStructure as $id => &$qs)
        {
            if (empty($qs) && isset(self::$STRUCTURE_MAP[$id]))
            {
                $currentStructure[$id] = self::$STRUCTURE_MAP[$id];
                unset(self::$STRUCTURE_MAP[$id]);
            }
            else if (!empty($qs))
            {
                self::sub_sort($qs);
            }
        }
    }

    public static function is_second_init()
    {
        return !self::$first;
    }

    public static function get_main_views()
    {
        return self::$MAIN_VIEWS;
    }

    /**
     * Заменяет массив всех объектов указанным массивом
     *
     * @param $array
     */
    public static function set_all_modules_array($array)
    {
        self::$OBJECTS = $array;
    }

    /**
     * Формирует массив с данными о шаблоне модуле
     *
     * @param int $idTemplate ID шаблона
     * @return type
     * @internal param bool $view если true - вернет только вид модуля, который можно выводить через echo.
     * усли false - вернет массив, с данными по модулю, его видом, и информацией из базы данных
     */
    public static function get_template($idTemplate = null)
    {
        return self::get(\Module::ADDMT(null, $idTemplate));
    }

    public static function get_all_modules()
    {
        return self::$OBJECTS;
    }

    public static function get_all_files()
    {
        return self::$FILES;
    }

    public static function get_structure_map()
    {
        return isset(self::$STRUCTURE_MAP[-1]) ? self::$STRUCTURE_MAP[-1] : [];
    }

    public static function add_main_module($module)
    {
        $mc = new ModuleCreater();
        $mc->connect_module($module);
    }

    public static function destroy_module($module, $forChildrens = false)
    {
        if ($module->get_my_id() === null)
        {
            $module->set_main_module();
        }

        $id = $module->get_my_id();

        if (isset(self::$OBJECTS[$id]))
        {
            unset(self::$OBJECTS[$id]);
        }

        if ($forChildrens)
        {
            self::destroy_for_childrens($id);
        }
        else
        {
            self::opacity_destroy($module);
        }
    }

    private static function destroy_for_childrens($id)
    {
        $childrens = self::find_object_childrens($id);
        foreach ($childrens as $child)
        {
            $child->destroy(true);
        }
    }

    /**
     *
     * @param $moduleId
     * @param $property_filter
     * @param $recursive
     * @return type
     */
    public static function find_object_childrens($moduleId, $property_filter = [], $recursive = false)
    {
        if ($moduleId === null)
        {
            return [];
        }

        $ret = [];

        foreach (self::$OBJECTS as $module)
        {
            if ($module->get_my_parent_id() === $moduleId)
            {
                if ($recursive)
                {
                    $__in_childs = self::find_object_childrens($module->get_my_id(), $property_filter, $recursive);
                    $__in_childs = is_array($__in_childs) ? $__in_childs : [];
                    $ret         = array_merge($ret, $__in_childs);
                }

                $add_in_result = true;


                foreach ($property_filter as $property => $value)
                {

                    if ($value === null && isset($module->{$property}))
                    {
                        continue;
                    }
                    else if ($value && isset($module->{$property}) && $module->{$property} == $value)
                    {
                        continue;
                    }
                    else
                    {
                        $add_in_result = false;
                    }
                }


                if ($add_in_result)
                {
                    $ret[$module->get_my_id()] = $module;
                }
            }
        }

        return $ret;
    }

    private static function opacity_destroy($module)
    {

        $childrens = self::find_object_childrens($module->get_my_id());
        $parent    = \Modules::find_object_parent($module);
        $pos       = $module->__cms_module_position;
        foreach ($childrens as $children)
        {
            if ($parent !== null)
            {
                $parent->ADDM($children, $pos);
            }
            else
            {
                $children->set_main_module();
            }
        }
    }

    public static function find_object_parent($module)
    {
        $parentId = $module->get_my_parent_id();
        if (isset(self::$OBJECTS[$parentId]))
        {
            return self::$OBJECTS[$parentId];
        }

        return null;
    }

    public static function add_listner($function, $id = null)
    {
        if ($id === null)
        {
            self::$listner_functions[] = $function;
        }
        else
        {
            self::$listner_functions[$id] = $function;
        }
    }

    public static function remove_listner($id)
    {
        if (isset(self::$listner_functions[$id]))
        {
            unset(self::$listner_functions[$id]);
        }
    }

    public static function find_objects_by_name($moduleName)
    {
        $ret = [];
        foreach (self::$OBJECTS as $id => $module)
        {
            if (get_class($module) === $moduleName)
            {
                $ret[$id] = $module;
            }
        }

        return $ret;
    }

    private static function find_my_parent(&$array, $parentId, $id)
    {
        if (isset($array[$parentId]))
        {
            $array[$parentId][$id] = [];
        }
        else
        {
            foreach ($array as &$ar)
            {
                self::find_my_parent($ar, $parentId, $id);
            }
        }
    }

}

/**
 *
 * @param $time_start
 * @param $request
 * @param $dump
 * @param $filter_value $time >  $filter_value = dump
 * @param $vardump_value $time >  $filter_value = dump
 * @return float
 */
function microtime_float($time_start = null, $request = '', $dump = true, $filter_value = 0, $vardump_value = null)
{
    if ($time_start === null)
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }
    else
    {
        $time_end = microtime_float();
        $time     = $time_end - $time_start;
        if ($dump && $time > $filter_value)
        {
            if ($vardump_value)
            {
                var_dump("=====================OPEN << $request >> ==================");
                echo '<br/>';
            }

            if (!$vardump_value)
            {
                var_dump('microtime_end: ' . $request);
            }
            var_dump($time);
            echo '</br>';

            if ($vardump_value)
            {
                echo '<pre class="btn-inverse">';
                var_dump($vardump_value);
                echo '</pre>';
                var_dump("=====================CLOSE << $request >> ==================");
                echo '<br/>';
            }
        }

        return $time;
    }
}
