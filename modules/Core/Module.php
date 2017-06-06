<?php


use MBCMS\configuration;
use MBCMS\ModuleCreater;
use MBCMS\routes;

class Module extends Autoload
{

    const __cms_connect_type_TEMPLATE      = '__cms_connect_type_TEMPLATE';
    const __cms_connect_type_OUTPUT        = '__cms_connect_type_OUTPUT';
    const __cms_connect_type_PROGRAMM_ADDM = '__cms_connect_type_PROGRAMM_ADDM';
    const MODULE_TAG_VIEW                  = 'MODULE_TAG_VIEW';
    const MODULE_TAG_TECH                  = 'MODULE_TAG_TECH';
    const STANDART_TEMPLATE_NAMESPACE      = 'User\\';
    const NO_POSITION                      = 'mbcms_standart_module_position_none';
    public static  $__templates_metr      = 0;
    public static  $__templates_time      = 0;
    public static  $__templates_time_addm = 0;
    private static $IIIN                  = 15000;
    private static $__response            = [];
    /**
     *
     * @var string Позиция для вывода модуля, название переменной указанной в echo_modules
     */
    public $__cms_module_position = '';
    public $__cms_attrs           = [];
    /**
     *
     * @var array Массив с информацией из ЦМС
     */
    public $CMSData = [];
    public $echo_module_class;
    /**
     * @var int
     */
    public $THIS_CONNECTION_MODULE_ID = null;
    /**
     * @var int
     */
    public    $THIS_CONNECTION_PARENT_ID = null;
    protected $__cms_connect_type;
    protected $__cms_module_tags         = [];
    protected $__cms_fast_edit           = true;
    protected $__cms_template_index;
    protected $__cms_output_index;
    protected $__user_cms_parent_output_index;
    protected $__cms_php_class_name;
    protected $__ignore_logic            = false;
    private   $__take_alias              = '';
    private   $__is_set_id               = false;
    private   $__render                  = true;
    private   $__not_files               = false;
    private   $__view_prioritet_index    = 0;
    private   $__view_prioritet_range    = 9999;

    public function __construct()
    {
        routes::not_ajax(__METHOD__);
        defined('HOME_PATH') or die('No direct script access.');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    //=========================================== idTemplate =========================================//
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function get_module_output_data_by_id($idTemplate, $out_index)
    {
        $d = self::get_module_cms_data_by_id($idTemplate);

        return isset($d['outputs'][$out_index]) ? $d['outputs'][$out_index] : null;
    }

    /**
     *
     * @param string $idTemplate - id шаблона который будет подключен (класс)
     * @param bool $is_object = false if true return stdClass
     * @return object
     */
    public static function get_module_cms_data_by_id($idTemplate = null, $is_object = false)
    {

        if ($idTemplate == null)
        {
            return null;
        }

        $classname = self::STANDART_TEMPLATE_NAMESPACE . $idTemplate;

        if (GClass::autoLoad($classname) && property_exists($classname, 'testdata'))
        {
            return $is_object ? (object)$classname::$testdata : $classname::$testdata;
        }

        return null;
    }

    public static function update_settings_by_id($idTemplate, $settings)
    {
        $d        = self::get_module_cms_data_by_id($idTemplate);
        $settings = array_merge(is_array($d['settingsData']) ? $d['settingsData'] : [], $settings);
        self::save_settings_by_id($idTemplate, $settings);
    }

    public static function save_settings_by_id($idTemplate, $settingsData)
    {
        $d                 = self::get_module_cms_data_by_id($idTemplate);
        $d['settingsData'] = $settingsData;
        if (isset($settingsData['title']))
        {
            $d['title'] = $settingsData['title'];
            unset($settingsData['title']);
        }
        self::generate_php_template($idTemplate, $d);
    }

    /**
     *
     * @param string $idTemplate User\tblock_idTemplate можно без tblock всегда таймспейс User
     * поэтому указывать просто как idTemplate уникальное имя класса, без tblock подстановки<br>
     * @param array $templateData <br> [ <br>
     * 'idTemplate' => '{{idTemplate}}', <br>
     * 'childrens' => '{{childrens}}', <br>
     * 'name' => '{{name}}', <br>
     * 'settingsData' => '{{settingsData}}', <br>
     * 'outputs' => '{{outputs}}', <br>
     * 'title' => '{{title}}', <br>
     * 'description' => '{{description}}',<br>
     * 'path' => '{{path}}'<br>
     * ];<br>
     */
    public static function generate_php_template($idTemplate, $templateData)
    {
        if (!isset($templateData['idTemplate']) || !isset($templateData['name']))
        {
            return;
        }

        if (!$idTemplate || !$templateData || empty($templateData))
        {
            return;
        }

        $templateData['settingsData'] = $templateData['settingsData'] ? $templateData['settingsData'] : '[]';
        $templateData['childrens']    = $templateData['childrens'] ? $templateData['childrens'] : '[]';
        $templateData['outputs']      = $templateData['outputs'] ? $templateData['outputs'] : '[]';
        $templateData['title']        = $templateData['title'] ? $templateData['title'] : 'Новый шаблон';
        $templateData['path']         = $templateData['path'] ? $templateData['path'] : '';

        GClass::getClassInfo(self::STANDART_TEMPLATE_NAMESPACE . $idTemplate);

        $folder       = GClass::$classInfo['folder'];
        $template_php = $folder . DIRECTORY_SEPARATOR . $idTemplate . '.php';

        if (!file_exists($folder))
        {
            mkdir($folder);
        }

        self::__file_put_contents($template_php, self::__generate_template_php($idTemplate, $templateData));
    }

    /**
     * Записывает в файл с блокировкой
     *
     * @param $filename
     * @param $data
     */
    private static function __file_put_contents($filename, $data)
    {
        $fp = fopen($filename, "a+");
        flock($fp, LOCK_EX); //блокировка файла
        ftruncate($fp, 0);
        fwrite($fp, $data);
        flock($fp, LOCK_UN); //снятие блокировки
        fclose($fp);
    }

    private static function __generate_template_php($idTemplate, $templateData)
    {

        $text = "<?php\n\n ";
        $text .= file_get_contents(MPATH . 'Core/standart_template_class.php');

        foreach ($templateData as $key => $val)
        {
            if (is_array($val) || is_object($val) || $key == 'childrens' || $key == 'settingsData' || $key == 'outputs')
            {
                $val = self::__generate_array($val);
            }
            else if (is_string($val))
            {
                $val = self::__generate_string($val);
            }

            $text = str_replace('{{' . $key . '}}', $val, $text);
        }

        $text = str_replace('{{idTemplate_class}}', $idTemplate, $text);
        $text = str_replace('{{standart_namespace}}', str_replace('\\', '', self::STANDART_TEMPLATE_NAMESPACE), $text);

        return $text;
    }

    private static function __generate_array($array, $depth = 1)
    {
        if (count((array)$array) == 0 || !is_array($array))
        {
            return '[]';
        }

        $text = '';
        $tabs = str_repeat("\t", $depth);
        $text .= "\n" . $tabs . '[';


        foreach ($array as $key => $item)
        {
            $item = is_string($item) ? self::__generate_string($item) : $item;
            $item = is_array($item) || is_object($item) ? self::__generate_array($item, $depth + 1) : $item;
            $text .= "\n" . $tabs . '\'' . $key . '\' => ' . $item . ',';
        }

        $text .= "\n" . $tabs . ']';

        return $text;
    }

    private static function __generate_string($string)
    {
        $string = str_replace(['\\', "'"], ['\\\\', "\'"], $string);

        return '\'' . $string . '\'';
    }

    /**
     *
     * @param $idTemplate
     * @param $out_index
     * @param $data объект с [ключ => значение] для замены
     */
    public static function update_output_by_id($idTemplate, $out_index, $data)
    {
        $d    = self::get_module_cms_data_by_id($idTemplate);
        $data = is_array($data) ? $data : [];
        if (isset($d['outputs'][$out_index]['data']))
        {
            if (isset($data['name']) && GClass::autoLoad($data['name']))
            {
                $d['outputs'][$out_index]['name'] = $data['name'];
                unset($data['name']);
            }

            $d['outputs'][$out_index]['data'] = array_merge($d['outputs'][$out_index]['data'], $data);
        }

        self::save_outputs_by_id($idTemplate, $d['outputs']);
    }

    public static function save_outputs_by_id($idTemplate, $outputs)
    {
        $d            = self::get_module_cms_data_by_id($idTemplate);
        $d['outputs'] = $outputs;
        self::generate_php_template($idTemplate, $d);
    }

    /**
     *
     * @param $idTemplate
     * @param $data объект с [ключ => значение] для замены
     */
    public static function update_output_by_id_array($idTemplate, $data)
    {
        $d    = self::get_module_cms_data_by_id($idTemplate);
        $data = is_array($data) ? $data : [];

        foreach ($data as $out_index => $cdata)
        {
            if (isset($d['outputs'][$out_index]['data']))
            {
                $d['outputs'][$out_index]['data'] = array_merge($d['outputs'][$out_index]['data'], $cdata);
            }
        }

        self::save_outputs_by_id($idTemplate, $d['outputs']);
    }

    /**
     *
     * @param string $idTemplate
     * @param string $name
     * @param mixed $data array or out_index для клонирования data
     * @param int $count
     * @param string $position
     * @return string
     */
    public static function add_output($idTemplate, $name, $data = [], $count = 1, $position = 'modules')
    {
        $d     = self::get_module_cms_data_by_id($idTemplate);
        $index = null;

        if (!is_array($data) && isset($d['outputs'][$data]['data']))
        {
            $index = $data;
            $data  = $d['outputs'][$index]['data'];
        }

        for ($i = 0; $i < $count; $i++)
        {
            $random_id                  = self::__generate_random_id();
            $data['__cms_output_index'] = $random_id;
            $d['outputs'][$random_id]   = [
                'name'     => $name,
                'position' => $position,
                'data'     => $data,
            ];

            if ($index != null)
            {
                self::__add_new_childrens($index, $random_id, $d['outputs']);
            }
        }

        self::save_outputs_by_id($idTemplate, $d['outputs']);

        return $random_id;
    }

    static function __generate_random_id()
    {
        return rand(100, 1000) . rand(100, 1000);
    }

    private static function __add_new_childrens($index, $new_index, &$outputs)
    {
        if (trim($index) == '' || $index == null)
        {
            return;
        }

        foreach ($outputs as $__index => $output)
        {
            if (isset($output['data']['__user_cms_parent_output_index']) && $output['data']['__user_cms_parent_output_index'] == $index)
            {
                $data                                   = $output['data'];
                $data['__user_cms_parent_output_index'] = $new_index; // проверять на их индекс!
                $random_id                              = self::__generate_random_id();
                $data['__cms_output_index']             = $random_id;
                $outputs[$random_id]                    = [
                    'name'     => $output['name'],
                    'position' => $output['position'],
                    'data'     => $data,
                ];

                self::__add_new_childrens($__index, $random_id, $outputs);
            }
        }
    }

    /**
     *
     *
     * @param $idTemplate
     * @param $outputs - массив [name, position, data => []] - В ДАННЫХ НЕ ДОЛЖНО БЫТЬ ОДИНАРНЫХ КАВЫЧЕК!!! экранировать их!
     */
    public static function add_output_array($idTemplate, $outputs)
    {
        $d = self::get_module_cms_data_by_id($idTemplate);

        foreach ($outputs as $output)
        {
            $rand                                 = rand(100, 1000000);
            $output['data']['__cms_output_index'] = $rand;
            $d['outputs'][$rand]                  = $output;
        }

        self::save_outputs_by_id($idTemplate, $d['outputs']);
    }

    public static function remove_output($idTemplate, $out_index)
    {
        $d         = self::get_module_cms_data_by_id($idTemplate);
        $out_index = is_array($out_index) ? $out_index : [$out_index];

        foreach ($out_index as $__index)
        {
            foreach ($d['outputs'] as $index => $out)
            {
                if ($index == $__index)
                {
                    unset($d['outputs'][$index]);
                    self::__delete_and_new_parents($d['outputs'], $index);
                }
            }
        }

        self::save_outputs_by_id($idTemplate, $d['outputs']);
    }

    private static function __delete_and_new_parents(&$outputs, $index)
    {
        foreach ($outputs as $__index => $out)
        {
            if (isset($out['data']['__user_cms_parent_output_index']) && $out['data']['__user_cms_parent_output_index'] == $index)
            {
                unset($outputs[$__index]);
                self::__delete_and_new_parents($outputs, $__index);
            }
        }
    }

    public static function resort_outputs($idTemplate, $indexis)
    {
        $d           = self::get_module_cms_data_by_id($idTemplate);
        $new_outputs = [];


        foreach ($indexis as $old_index)
        {
            if (isset($d['outputs'][$old_index]))
            {
                $new_outputs[$old_index] = $d['outputs'][$old_index];
            }
        }

        self::save_outputs_by_id($idTemplate, $new_outputs);
    }

    public static function resort_templates($idTemplate, $indexis)
    {
        $d             = self::get_module_cms_data_by_id($idTemplate);
        $new_templates = [];

        foreach ($indexis as $index)
        {
            if (isset($d['childrens'][$index]))
            {
                $new_templates[$index] = $d['childrens'][$index];
            }
        }

        self::save_templates_by_id($idTemplate, $new_templates);
    }

    public static function save_templates_by_id($idTemplate, $childrens)
    {
        $d              = self::get_module_cms_data_by_id($idTemplate);
        $d['childrens'] = $childrens;
        self::generate_php_template($idTemplate, $d);

        return true;
    }

    /**
     *
     * @param string $idTemplate
     * @param $new_idTemplate
     * @param string $position
     * @return string
     */
    public static function add_new_template($idTemplate, $new_idTemplate, $position = 'modules')
    {
        $parentData          = self::get_module_cms_data_by_id($idTemplate);
        $children_idTemplate = \MBCMS\template::create_new_template($new_idTemplate, $parentData['path']);

        self::add_template($idTemplate, $children_idTemplate, $position);

        return $children_idTemplate;
    }

    /**
     *
     * @param string $idTemplate
     * @param string $children_idTemplate
     * @param string $position
     */
    public static function add_template($idTemplate, $children_idTemplate, $position = 'modules')
    {
        if (!$children_idTemplate)
        {
            return;
        }

        $d = self::get_module_cms_data_by_id($idTemplate);

        $d['childrens'][$children_idTemplate] = $position;

        self::save_templates_by_id($idTemplate, $d['childrens']);
    }

    public static function clone_template($parent_idTemplate, $clone_idTemplate, $count = 1)
    {
        $chd = self::get_module_cms_data_by_id($clone_idTemplate);

        $className = isset($chd['name']) ? $chd['name'] : null;
        if (GClass::autoLoad($className))
        {

            for ($i = 0; $i < $count; $i++)
            {
                $postfix         = '_c' . $i;
                $d               = $chd;
                $new_idTemplate  = $chd['idTemplate'] . $postfix;
                $d['idTemplate'] = $new_idTemplate;
                $d['name']       = $d['name'] . $postfix;

                \MBCMS\get_all_templates::create_new_module_php($new_idTemplate);
                self::generate_php_template($new_idTemplate, $d);
                self::add_template($parent_idTemplate, $new_idTemplate);
                self::add_response('new_idTemplate[]', $new_idTemplate);
            }
        }
    }

    /**
     * Добавляет\заменяет значения которые необходимо вернуть
     *
     * @param $key
     * @param $val
     */
    public static function add_response($key, $val)
    {

        if (preg_match('~\[\]~', $key))
        {
            $key                      = str_replace('[]', '', $key);
            $key                      = trim($key);
            self::$__response[$key][] = $val;
        }
        else
        {
            $key                    = trim($key);
            self::$__response[$key] = $val;
        }
    }

    public static function nature($module)
    {
        return Modules::get_module_view($module);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    //================================================================================================//
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function create_template($idTemplate, $cms_data = null)
    {
        if ($cms_data)
        {
            $data = $cms_data;
        }
        else
        {
            $data = self::get_module_cms_data_by_id($idTemplate);
        }

        $className = $data['name'];
        if (GClass::autoLoad($className))
        {
            $newModule = new $className;
            isset($data['settingsData']) ? $newModule->clone_settings($data['settingsData']) : null;
            $newModule->CMSData = $data;

            return $newModule;
        }

        return null;
    }

    /**
     * Возвращает массив значений для ajax и убивает  дальнейший вывод die()
     * @param null $method - если указан метод __METHOD__ проверяет является ли он целью ajax запроса, и только в этом случае вызывает die()
     */
    public static function response($method = null)
    {
        self::$__response['response'] = 'true';
        echo json_encode(self::$__response, JSON_UNESCAPED_UNICODE);
        self::$__response = null;

        if ($method && routes::is_target_method($method))
        {
            die();
        }
        else if ($method === null)
        {
            die();
        }
    }

    /**
     * @param $key
     * @param null $standart
     * @return mixed|null
     */
    public static function response_get_key_value($key, $standart = null)
    {
        return isset(self::$__response[$key]) ? self::$__response[$key] : $standart;
    }

    public static function get_modules_connect_count()
    {
        return 15000 - self::$IIIN;
    }

    /**
     * @param $module
     * @return view
     */
    protected static function get_module_view($module)
    {
        return \Modules::get_module_view($module);
    }

    public function get_editor_modules()
    {
        routes::not_ajax(__METHOD__);
        $this->generate_content();
    }

    private function generate_content()
    {
        if (!$this->CMSData)
        {
            return;
        }

        $this->add_category();

        if (isset($this->CMSData['outputs'])) /* подключаем outputs */
        {
            foreach ($this->CMSData['outputs'] as $module)
            {
                self::ADDMO($this, $module['name'], $module['data'], $module['position']);
            }
        }

        $__detect_none_template = false;

        if (isset($this->CMSData['childrens'])) /* подключаем templates */
        {
            foreach ($this->CMSData['childrens'] as $__idTemplate => $position)
            {
                if (GClass::autoLoad(self::STANDART_TEMPLATE_NAMESPACE . $__idTemplate))
                {
                    self::ADDMT($this, $__idTemplate, $position);
                }
                else
                {
                    $__detect_none_template = true;
                }
            }
        }

        if ($__detect_none_template)
        {
            $this->__clear_non_templates();
        }
    }

    private function add_category()
    {
        $this->__cms_connect_type = isset($this->__cms_connect_type) ? $this->__cms_connect_type : '';

        if ($this->__cms_connect_type !== self::__cms_connect_type_OUTPUT && isset($this->CMSData['idTemplate']))
        {
            $this->__cms_connect_type = self::__cms_connect_type_TEMPLATE;
            $this->add_tag(self::MODULE_TAG_VIEW);
        }
        else if ($this->__cms_connect_type !== self::__cms_connect_type_OUTPUT && !isset($this->CMSData['idTemplate']))
        {
            $this->__cms_connect_type = self::__cms_connect_type_PROGRAMM_ADDM;
        }
    }

    /**
     * Это типы модулей которые возможны, они по разному выводятся и отображаются в итерфейсе админке
     * и по разному инициализируются. по тегу с помощью JS
     *
     * @param string $tag
     */
    public function add_tag($tag)
    {
        routes::not_ajax(__METHOD__);
        $this->__cms_module_tags[$tag] = $tag;
    }

    /**
     *
     * @param $parent
     * @param $className
     * @param $data
     * @param string|type $position
     * @return className
     */
    public static function ADDMO($parent, $className, $data, $position = 'modules')
    {
        if (GClass::autoLoad($className))
        {
            $new_output                         = new $className();
            $new_output->__cms_parentidtemplate = isset($parent->CMSData['idTemplate']) ? $parent->CMSData['idTemplate'] : 'NONE!';
            $new_output->add_attr('__cms_parentidtemplate', 'parentidtemplate');

            $new_output->clone_settings($data, null, false);
            $new_output->add_attr('__cms_output_index', '__cms_output_index', true);
            $new_output->add_attr('__user_cms_parent_output_index', '__user_cms_parent_output_index', true);

            $new_output->__cms_connect_type = self::__cms_connect_type_OUTPUT;
            $new_output->add_tag(self::MODULE_TAG_VIEW);
            $new_output->__cms_php_class_name = $className;

            $parent->ADDM($new_output, $position);

            return $new_output;
        }

        return null;
    }

    /**
     *
     * @param $parent
     * @param $idTemplate
     * @param string $position
     * @return Module
     */
    public static function ADDMT($parent, $idTemplate, $position = 'modules')
    {
        self::$__templates_metr++;
        //////////////////////test/////////////////////

        $d    = self::get_module_cms_data_by_id($idTemplate);
        $name = $d['name'];

        if (GClass::autoLoad($name))
        {
            $new_template          = new $name();
            $new_template->CMSData = $d;
            $new_template->clone_settings($new_template->CMSData['settingsData'], [], false);
            $new_template->add_tag(self::MODULE_TAG_VIEW);

            if ($parent)
            {
                $new_template->__cms_parentidtemplate = isset($parent->CMSData['idTemplate']) ? $parent->CMSData['idTemplate'] : 'NONE!';

                $new_template->add_attr('__cms_parentidtemplate', 'parentidtemplate');
                $new_template->add_attr('__cms_template_index', '__cms_template_index', true);

                $parent->ADDM($new_template, $position);
            }


            return $new_template;
        }

        if ($parent)
        {
            $dp              = self::get_module_cms_data_by_id($parent->CMSData['idTemplate']);
            $dp['childrens'] = $dp['childrens'] ? $dp['childrens'] : [];
            foreach ($dp['childrens'] as $__idTemplate => $child)
            {
                if ($__idTemplate == $idTemplate)
                {
                    self::remove_template($parent->CMSData['idTemplate'], $__idTemplate);
                }
            }
        }


        return null;
    }

    /**
     * Удалить шаблон из структуры родительского шаблона
     *
     * @param $parentId
     * @param $idTemplate
     */
    public static function remove_template($parentId, $idTemplate)
    {
        $d = self::get_module_cms_data_by_id($parentId);

        $d['childrens'] = isset($d['childrens']) ? $d['childrens'] : [];

        foreach ($d['childrens'] as $__idTemplate => $template)
        {
            self::add_response('$indexm', $__idTemplate);
            self::add_response('$index', $idTemplate);

            if (strtoupper($__idTemplate) == strtoupper($idTemplate))
            {
                unset($d['childrens'][$__idTemplate]);
            }
        }

        self::save_templates_by_id($parentId, $d['childrens']);
    }

    private function __clear_non_templates()
    {
        if (!isset($this->CMSData))
        {
            return;
        }

        $d = self::get_module_cms_data_by_id($this->CMSData['idTemplate']);

        foreach ($d['childrens'] as $key => $children)
        {
            $className = self::STANDART_TEMPLATE_NAMESPACE . (isset($children['idTemplate']) ? $children['idTemplate'] : '');

            if (!GClass::autoLoad($className))
            {
                unset($d['childrens'][$key]);
            }
        }

        self::generate_php_template($this->CMSData['idTemplate'], $d);
    }

    public function __call($name, $arguments)
    {
        routes::not_ajax(__METHOD__);

        if ($name === 'set_id' && $this->__is_set_id == false)
        {
            $this->__is_set_id               = true;
            $this->THIS_CONNECTION_MODULE_ID = $arguments[0];
        }
    }

    /**
     * основная функция вызываемая в пользовательском модуле,
     * Предназначена для подключения модулей-потомков.
     * Возвращает массив полученный через \Modules::get();
     *
     * $modules = array(new Module());
     * return \Modules::get($modules);
     *
     * если этот модуль не подключает другие модули, просто возвращает пустой массив
     *
     * @return void
     */
    public function init()
    {
        routes::not_ajax(__METHOD__);

        $this->echo_module_class = get_class($this);
        $this->add_attr('echo_module_class', 'echo_module_class', (configuration::factory()->is_static_templates() === true ? true : null));

        if (!MBCMS\routes::is_admin())
        {
            return;
        }

        $this->echo_module_class = get_class($this);
        $this->add_attr('__cms_fast_edit', 'fast_edit', true);
        $this->add_attr('fixed_padlock', 'fixed_padlock', true);
        $this->set_identification_attrs($this);

        if (Modules::is_second_init())
        {
            $this->set_my_ids(0);
        }
    }

    /**
     *
     * @param $class_propperty - свойство класса
     * @param $attr_key - атрибут html элемента
     * @param bool $only_admin если true то только в админке, если false то только вне админки, если null то и там и там
     * @param $group
     * @return $this
     */
    protected function add_attr($class_propperty, $attr_key, $only_admin = null, $group = 'main')
    {
        if ($only_admin === routes::is_admin() || $only_admin === null)
        {
            $group = !$group ? 'main' : $group;
            if (isset($this->$class_propperty))
            {
                $this->__cms_attrs[$group][$attr_key] = attr($this->$class_propperty, str_replace('=', '', $attr_key) . '=', false);
            }
        }

        return $this;
    }

    /**
     *
     * @param Module $module
     */
    protected function set_identification_attrs(&$module)
    {
        $module->module_idTemplate = isset($module->CMSData['idTemplate']) ? $module->CMSData['idTemplate'] : null;
        $module->add_attr('module_idTemplate', 'idtemplate', true);

        $module->add_attr('echo_module_class', 'module_class', true);

        $module->module_template_title = isset($module->CMSData['title']) ? $module->CMSData['title'] : null;
        $module->add_attr('module_template_title', 'template_title', true);
    }

    protected function set_my_ids($my_id = null, $my_parent_id = -1)
    {
        if ($my_id !== null)
        {
            $this->THIS_CONNECTION_MODULE_ID = $my_id;
        }

        if ($my_parent_id !== -1)
        {
            $this->THIS_CONNECTION_PARENT_ID = $my_parent_id;
        }
    }

    /**
     * Вкладывает в статичный HTML код, вызовы модулей. Что запускает их жизненный цикл init-after_init-preview
     * и позволяет расчитывать только те элементы которые нуждаются в динамических данных
     *
     * Пример использования
     *
     * function static_nature()
     * {
     *    return $this->__static_nature();
     * }
     *
     * OR
     *
     * if (routes::is_static_status())
     * {
     *      unset($this->modules);
     *      return $this->__static_nature($this);
     * }
     * else
     * {
     *      return true;
     * }
     *
     *
     *
     * OR
     *
     * public function static_nature()
     * {
     * foreach (\Modules::get_all_modules() as $all_module)
     * {
     * if (get_class($all_module) == 'MBCMS\out')
     * {
     * $this->__mod = clone $all_module;
     * $this->ADDM($all_module, '$search_pos');
     * }
     * }
     *
     * return $this->__static_nature(['__mod' => isset($this->__mod) ? $this->__mod : '']);
     * }
     *
     * and after init
     *
     * and in init
     * if (isset($this->__mod) && $this->__mod)
     * {
     * $this->__mod->clear_id();
     * $this->ADDM($this->__mod, '$search_pos');
     * }
     *
     */
    public function static_nature()
    {
        routes::not_ajax(__METHOD__);

        return false;
    }

    /**
     * Вторая возможность добавить модули в структуру, но при этом есть уже полноценный
     * список старых модулей из init() значит родительские модули, могут оборачивать своих детей
     * либо удалять ненужные модули, обертки, а так же менять структуру модулей.
     *
     */
    public function after_init()
    {
        routes::not_ajax(__METHOD__);
    }

    /**
     * Все модули без позиции подключать здесь!!!
     *
     * Можно вернуть массив модулей из этого метода, тогда они подключаться на стадии сборки.
     *
     * Тут идет подключение файлов и игнорируется логика, что дает прирост в скорости при формировании статичного шаблона.
     * Иногда файлы не подключаются из init даже несмотря на отсутствие ветвлений. Потому что теперь все подключения
     * идут по этой ветве жизни.
     */
    public function init_files()
    {
        routes::not_ajax(__METHOD__);

        return null;
    }

    /**
     * Эта функция вызывается самой последней, после того как структура модулей готова,
     * и были выбраны все файлы принадлежащие этим модулям, тут можно получить списки
     * файлов, и поменять свойства модулей, но уже нельзя влиять на структуру.
     *
     * после этой функции будет формироваться финальный вид страницы.
     */
    public function preview()
    {
        routes::not_ajax(__METHOD__);
    }

    /**
     * Говорит о том что данный модуль является видом без привязки логики
     * его не нужно расчитывать
     * Полезно если нужно вывести много шаблонов подряд, просто подставляя туда данные.
     * Работает в 10-100 раз быстрее чем полное подключение модуля.
     *
     * Методы init; after_init; preview; не будут вызваны
     */
    public function ignore_logic()
    {
        routes::not_ajax(__METHOD__);
        GClass::getClassInfo(get_class($this));
        $this->__ignore_logic = GClass::$classInfo['view'];
    }

    public function get_prioritet_range()
    {
        routes::not_ajax(__METHOD__);

        return $this->__view_prioritet_range;
    }

    /**
     *
     * @param $module
     * @param array $filter фильтр свойств которые не нужно клонировать
     * @param bool $takeEmpty указывает стоит ли присваивать пустые значения
     * @return $this
     */
    public function clone_settings($module, $filter = [], $takeEmpty = true)
    {
        routes::not_ajax(__METHOD__);
        if (!is_object($module) && !is_array($module))
        {
            return $this;
        }

        $ar           = (array)$module;
        $publicmodule = (object)$ar;
        $filter[]     = 'THIS_CONNECTION_MODULE_ID';
        $filter[]     = 'THIS_CONNECTION_PARENT_ID';
        $filter[]     = 'position';

        foreach ($publicmodule as $key => $val)
        {
            $clone = true;
            foreach ($filter as $currentKey)
            {
                if ($currentKey === $key)
                {
                    $clone = false;
                    break;
                }
            }

            if (!$takeEmpty && is_string($val) && trim($val) === '')
            {
                $clone = false;
            }

            if ($clone)
            {
                $this->$key = is_string($val) ? $val : $val;
            }
        }

        return $this;
    }

    public function remove_tag($tag)
    {
        routes::not_ajax(__METHOD__);
        unset($this->__cms_module_tags[$tag]);
    }

    public function is_tag($tag)
    {
        routes::not_ajax(__METHOD__);

        return isset($this->__cms_module_tags[$tag]);
    }

    public function is_main()
    {
        routes::not_ajax(__METHOD__);

        return $this->THIS_CONNECTION_PARENT_ID === null;
    }

    public function get_my_parent_id()
    {
        routes::not_ajax(__METHOD__);

        return $this->THIS_CONNECTION_PARENT_ID;
    }

    /**
     * @return Module
     */
    public function get_my_parent()
    {
        routes::not_ajax(__METHOD__);

        return Modules::find_object_parent($this);
    }

    public function wrap_me($wrapperModule, $positionInWrapper)
    {
        routes::not_ajax(__METHOD__);
        $wrapperModule->wrap_around_target($this, $positionInWrapper);
    }

    public function wrap_around_target($targetModule, $position)
    {
        routes::not_ajax(__METHOD__);

        if ($this->get_my_id() === null)
        {
            $this->set_main_module();
        }

        $parentID = $targetModule->get_my_parent_id();
        $pose     = $targetModule->__cms_module_position;
        $this->ADDM($targetModule, $position);
        if ($parentID !== null)
        {
            $all = \Modules::get_all_modules();
            if (isset($all[$parentID]))
            {
                $all[$parentID]->ADDM($this, $pose);
            }
        }
    }

    public function get_my_id()
    {
        routes::not_ajax(__METHOD__);

        return $this->THIS_CONNECTION_MODULE_ID;
    }

    /**
     *
     * @param int $viewPrioritet слой отображения, чем выше тем выше приоритет главного отображения
     * @param int $range - допустимые элементы, у которых индекс меньше, например при (11, 10) все индексы < 1 не будут выводится,
     * при этом если 11 наивысший индекс, то он будет главным.
     */
    public function set_main_module($viewPrioritet = 1, $range = 1)
    {

        routes::not_ajax(__METHOD__);
        $this->view_prioritet_index($viewPrioritet, $range);
        $this->THIS_CONNECTION_PARENT_ID = null;
        $this->THIS_CONNECTION_MODULE_ID = null;
        \Modules::add_main_module($this);
    }

    public function view_prioritet_index($prioritet = null, $range = 9999)
    {
        routes::not_ajax(__METHOD__);
        if ($prioritet === null)
        {
            return $this->__view_prioritet_index;
        }

        $this->__view_prioritet_range = $range < 0 ? 0 : $range;
        $this->__view_prioritet_index = $prioritet;

        return null;
    }

    /**
     * Позволяет добавлять модули в позицию на лету, не через функцию init()
     * а скажем гдето в родительском модуле, в котором идет подключение этого модуля.
     *
     * @param \Module $module модуль который нужно добавить в вывод
     * @param string $pos позиция для вывода   Пример 'modules' | '$modules' $ - опускается, эти две записи идентичны
     * @param boolean $clear_old_position очистка привязки к старому родителю
     * @return $this
     */
    public function ADDM(\Module $module, $pos, $clear_old_position = false)
    {

        routes::not_ajax(__METHOD__);

        self::__infinite_controll();

        if (!is_object($module))
        {
            return null;
        }

        if ($this->get_my_id() === null)
        {
            $this->set_main_module();
        }

        if ($this === $module)
        {
            return null;
        }

        if ($clear_old_position)
        {
            $module->clear_id();
        }

        $pos                           = str_replace('$', '', $pos);
        $module->__cms_module_position = $pos;

        if ($pos === self::NO_POSITION || empty($pos))
        {
            $module->not_render();
        }


        $module->THIS_CONNECTION_PARENT_ID = $this->THIS_CONNECTION_MODULE_ID;

        $mc = new ModuleCreater();
        $mc->connect_module($module);

        return $this;
    }

    private static function __infinite_controll()
    {
        self::$IIIN--;

        if (!self::$IIIN)
        {
            $counts = [];

            foreach (Modules::get_all_modules() as $module)
            {
                $className          = get_class($module);
                $counts[$className] = isset($counts[$className]) ? $counts[$className] + 1 : 1;
            }

            Modules::form_structure_map();
            $map = Modules::get_structure_map();
            self::__set_map_names($map, Modules::get_all_modules());


            /* MDS */
            echo 'VAR DUMP 13:42 07.03.2017 Module.php 828 <br>';
            echo '<pre>';
            var_dump($counts);
            var_dump($map);
            echo '</pre>';
            echo '<br>';
            /* MDS */

            die('Вечный цикл подключений модуля!!!');
        }
    }

    private static function __set_map_names(&$map, $modules)
    {
        foreach ($map as $id => &$item)
        {
            if (is_array($item))
            {
                self::__set_map_names($item, $modules);
                $module       = isset($modules[$id]) ? get_class($modules[$id]) : 'none';
                $item['name'] = $module;
            }
        }
    }

    /**
     * @return $this
     */
    public function clear_id()
    {
        $this->THIS_CONNECTION_MODULE_ID = null;

        return $this;
    }

    public function not_render()
    {
        routes::not_ajax(__METHOD__);
        $this->__render = false;
    }

    public function destroy($forChildrens = false)
    {
        routes::not_ajax(__METHOD__);
        \Modules::destroy_module($this, $forChildrens);
    }

    public function take_alias($alias = null)
    {
        routes::not_ajax(__METHOD__);

        if ($alias === null)
        {
            return $this->__take_alias;
        }
        else
        {
            $this->__take_alias = $alias;
        }

        return null;
    }

    public function is_render()
    {
        routes::not_ajax(__METHOD__);

        return $this->__render;
    }

    function not_files()
    {
        $this->__not_files = true;
    }

    function is_not_files()
    {
        return $this->__not_files;
    }

    /**
     *
     * @param array $property_filter [property=>value or property=>null (isset)]
     * @param bool $recursive
     * @return type
     */
    public function find_my_childrens($property_filter = [], $recursive = false)
    {
        routes::not_ajax(__METHOD__);

        return Modules::find_object_childrens($this->get_my_id(), $property_filter, $recursive);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set_value($key, $value)
    {
        $this->{$key} = $value;

        return $this;
    }

    public function is_ignore_logic()
    {
        routes::not_ajax(__METHOD__);

        return $this->__ignore_logic;
    }

    /**
     * Принимает строковый параметр, для оборачивания его в выполняемый код
     * можно так же задавать зависимости методов, на данном уровне.
     *
     * Пример использования
     *
     * function static_nature()
     * {
     *    return $this->__static_nature($this|null); // если $this то будет переданы свойства (настройки) этого модуля от родительского шаблона!! установленные в CMS
     * }
     *
     * @param array|object $dop_data - массив данных которые были переданы родительским шаблоном например
     * данные которые необходимо передать в этот шаблон, при его вызове!
     *
     * @return string
     */
    protected function __static_nature($dop_data = [])
    {
        $method_name = get_class($this) . '::nature';

        if (is_callable($method_name))
        {
            $new = new $this;
            $new->clone_settings($dop_data);
            $params_array = serialize([$new]);

            return "\n\t<?php if (is_callable('$method_name'))  echo call_user_func_array('$method_name', unserialize('$params_array'));  ?>\n";
        }

        return false;
    }

    /**
     * Для переопределения родителя, нужно писать до parent::init();
     *
     * @param Module $module // $this php
     * @param callback_text
     * [
     *      "output", "out_deleter",
     * ]
     */
    protected function fast_edit($module, $list_array)
    {
        if (MBCMS\routes::is_admin())
        {
            $params = '[';
            for ($i = 0; $i < count($list_array); $i++)
            {
                $element = $list_array[$i];

                if (!is_object($element))
                {
                    continue;
                }

                $__class = str_replace('\\', '\\\\', get_class($element));

                if ($i == count($list_array) - 1)
                {
                    $params .= '"' . $__class . '"';
                }
                else
                {
                    $params .= '"' . $__class . '",';
                }
            }
            $params .= ']';


            $this->ADDM(new \MBCMS\js_connector_fast_edit($module, "mbcms.visual_fast_edit.fast_list(this, data, $params);"), 'modules');
        }
    }

    /**
     *
     * @param $css_class класс для иконки
     * @param $class $this
     * @param string $method имя метода для получения формы этой опции
     */
    protected function fast_edit_reg_option($css_class, $class, $method = 'view')
    {
        if (MBCMS\routes::is_admin())
        {
            $this->ADDM(new \MBCMS\js_connector_fast_edit_reg_option($css_class, $class, $method), 'modules');
        }
    }

    protected function remove_attr($attr_key, $only_admin = null)
    {
        if ($only_admin === routes::is_admin() || $only_admin === null)
        {
            foreach ($this->__cms_attrs as &$group)
            {
                foreach ($group as $key => $attr)
                {
                    if ($attr_key == $key)
                    {
                        unset($group[$key]);
                    }
                }
            }
        }
    }

}
