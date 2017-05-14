<?php

namespace MBCMS;

use GClass;
use MBCMS\Site\wrapper;
use Modules;

class template extends \Module implements \adminAjax
{

    const __STATIC__ = 'public';
    const __STATIC_VIEW = '__static_view';
    const __STATIC_RESOURCE_NAME = 'min_';
    private static $__current_folders = [];
    private $__php_class;

    public static function get_all_templates()
    {
        $path = MPATH . 'templates';

        $result = [];

        if (file_exists($path))
        {
            foreach (scandir($path) as $file)
            {
                if ($file != '.' && $file != '..' && is_dir($path . DIRECTORY_SEPARATOR . $file))
                {
                    $result[] = $file;
                }
            }
        }

        return $result;
    }

    public static function autogenerate_static($idTemplate = null)
    {
        $idTemplate = $idTemplate ? $idTemplate : \GetPost::uget('idTemplate');

        if (configuration::factory()->is_static_templates())
        {
            self::generate_static_view($idTemplate);
        }
        else
        {
            self::destroy_static_view($idTemplate);
        }

        die();
    }

    /**
     * @param null $idTemplate
     */
    public static function generate_static_view($idTemplate = null)
    {
        routes::set_admin_status();
        routes::set_gen_static_status();

        $idTemplate = $idTemplate ? $idTemplate : \GetPost::get('idTemplate');

        self::destroy_static_view($idTemplate);

        $new_template = \Module::ADDMT(null, $idTemplate);

        if (!$new_template)
        {
            return;
        }

        $childrens =  $new_template->find_my_childrens([], true);
        foreach ($childrens as $__ch_key => $child)
        {
            if (!isset($child->CMSData))
            {
                unset($childrens[$__ch_key]);
            }
        }
        Modules::set_all_modules_array($childrens);
        $new_template->set_main_module();

        ModuleCreater::call_after();
        ModuleCreater::call_preview();
        Modules::form_structure_map();
        ModuleCreater::form_views();

        $creater = new ModuleCreater();
        $creater->get_modules_files();
        $files = Modules::get_all_files();
        $files = $files ? $files : [];
        wrapper::set_prioritets($files);

        $d = self::get_module_cms_data_by_id($idTemplate);
        $className = $d['name'];
        GClass::getClassInfo($className);
        $path = GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . self::__STATIC_VIEW . '.php';

        if (!file_exists(GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . self::__STATIC__))
        {
            mkdir(GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . self::__STATIC__);
        }

        foreach ($files as $type => $tfiles)
        {
            $dir = GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . self::__STATIC__ . DIRECTORY_SEPARATOR . $type;

            if (!file_exists($dir) && count($files[$type]) > 0)
            {
                mkdir($dir);
            }


            $ext = str_replace(['top_', 'bottom_'], '', $type);
            $current_file_path = $dir . DIRECTORY_SEPARATOR . self::__STATIC_RESOURCE_NAME . $idTemplate .  '.' . $ext;
            file_put_contents($current_file_path, '');

            foreach ($tfiles as $file)
            {
                $current_file = $file['derictory'] . DIRECTORY_SEPARATOR . $file['name'];

                self::__copy_folders_in_static($file['derictory'], $type);

                if (file_exists($current_file))
                {
                    file_put_contents($current_file_path, self::__compress(file_get_contents($current_file)) , FILE_APPEND);
                }
            }
        }

        $view = Modules::get_main_views();
        file_put_contents($path, $view);
    }

    private static function __compress($code)
    {

        $code = preg_replace_callback('~/\*.*?\*/~s', function()
        {
            return '';
        }, $code);

        $code = preg_replace_callback('~\n\s*//.*\n~Us', function()
        {
            return "\n";
        }, $code);

        $code = preg_replace_callback('~\n\s*//.*\n~Us', function()
        {
            return "\n";
        }, $code);

        $code = preg_replace_callback('~\n\s*//.*\n~Us', function()
        {
            return "\n";
        }, $code);

        $code = preg_replace_callback('~\n\n~Us', function()
        {
            return ' ';
        }, $code);

        $code = preg_replace_callback('~\n\n~Us', function()
        {
            return ' ';
        }, $code);

        $code = preg_replace_callback('~\n\n~Us', function()
        {
            return ' ';
        }, $code);

        $code = preg_replace_callback('~\n\n~Us', function()
        {
            return ' ';
        }, $code);

        $code = preg_replace_callback('~\{\n~Us', function()
        {
            return '{ ';
        }, $code);

        $code = preg_replace_callback('~\n\{~Us', function()
        {
            return ' {';
        }, $code);

        $code = preg_replace_callback('~\t~s', function()
        {
            return ' ';
        }, $code);

        $code = preg_replace_callback('~,\n~s', function()
        {
            return ',';
        }, $code);

        $code = preg_replace_callback('~;\n~s', function()
        {
            return ';';
        }, $code);

        $code = preg_replace_callback('~\}\n~s', function()
        {
            return '}';
        }, $code);


        $code = str_replace('  ', ' ', $code);
        $code = str_replace('  ', ' ', $code);
        $code = str_replace('  ', ' ', $code);

        return $code;
    }

    public static function destroy_static_view($idTemplate = null)
    {

        $idTemplate = $idTemplate ? $idTemplate : \GetPost::get('idTemplate');


        $d = self::get_module_cms_data_by_id($idTemplate);
        $className = $d['name'];
        GClass::getClassInfo($className);
        $path = GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . self::__STATIC_VIEW . '.php';
        $__static_path = GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . self::__STATIC__;

        files::remove_dir($__static_path);

        if (file_exists($path))
        {
            @unlink($path);
        }
    }

    /**
     * Тут так же учитывать папку назначения не только css!!
     *
     * @param string $folder
     * @param $type
     */
    private static function __copy_folders_in_static($folder, $type)
    {
        if (!isset(self::$__current_folders[$folder]))
        {
            if (file_exists($folder))
            {
                $files = scandir($folder);

                foreach ($files as $file)
                {
                    if ($file == '.' || $file == '..')
                    {
                        continue;
                    }

                    $path = $folder . DIRECTORY_SEPARATOR . $file;
                    $new_file = GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . self::__STATIC__ . "/$type/" . $file;
                    if (is_dir($path) && !file_exists($new_file))
                    {
                        files::copy_folder($path, $new_file);
                    }
                }
            }

            self::$__current_folders[$folder] = true;
        }
    }

    /**
     *
     * @param string $new_idTemplate
     * @param string $path
     * @return string
     */
    public static function create_new_template($new_idTemplate, $path = '')
    {
        $path = $path ? $path : \GetPost::get('path', $path);

        $name = get_all_templates::create_new_module_php($new_idTemplate);
        if ($name)
        {
            self::__create_new_template($name, $path, $new_idTemplate);
        }

        return $new_idTemplate;
    }

    private static function __create_new_template($name, $path, $new_idTemplate)
    {
        $settingsData = ['__cms_template_index' => $new_idTemplate, '__user_cms_class' => 'this'];
        $tData = [
            'idTemplate' => $new_idTemplate,
            'childrens' => '[]',
            'name' => $name,
            'settingsData' => json_encode($settingsData),
            'outputs' => '[]',
            'title' => '',
            'description' => '',
            'path' => $path,
        ];

        \Module::generate_php_template($new_idTemplate, $tData);

        return $new_idTemplate;
    }

    public static function get_view($idTemplate = null)
    {
        $idTemplate = $idTemplate ? $idTemplate : \GetPost::get('idTemplate');

        $ret = \Module::ADDMT(null, $idTemplate);

        $ret->set_main_module();
    }

    /**
     *
     * @param $idTemplate
     * @return string
     */
    public static function get_static_view($idTemplate)
    {
        if (self::autoload($idTemplate))
        {
            $path = GClass::$classInfo['folder'] . DIRECTORY_SEPARATOR . self::__STATIC_VIEW . '.php';
            if (file_exists($path))
            {
                $file = file_get_contents($path);

                return $file;
            }
            else
            {
                return 'Шаблон не является статичным!';
            }
        }

        return '';
    }

    /**
     * @param $idTemplate
     * @return bool
     */
    public static function autoload($idTemplate)
    {
        if ($idTemplate)
        {
            $className = 'User\\' . $idTemplate;
            return GClass::autoLoad($className);
        }

        return false;
    }

    function resort()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $indexis = \GetPost::get('indexis');
        \Module::resort_templates($idTemplate, $indexis);
    }

    function add_new()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $new_idTemplate_chd = \GetPost::get('new_idTemplate');
        $new_idTemplate = \Module::add_new_template($idTemplate, 't' . $new_idTemplate_chd);

        self::add_response('idTemplate', $new_idTemplate);
        self::response();
    }

    function add()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $children_idTemplate = \GetPost::get('children_idTemplate');
        \Module::add_template($idTemplate, $children_idTemplate);
    }

    function tclone()
    {
        $idTemplate = \GetPost::get('parent_idTemplate');
        $children_idTemplate = \GetPost::get('children_idTemplate');
        $count = \GetPost::get('count');

        if ($idTemplate)
        {
            self::clone_template($idTemplate, $children_idTemplate, $count);
        }

        self::response();
    }

    function remove()
    {
        $parentidtemplate = \GetPost::get('parentidtemplate');
        $idTemplate = \GetPost::get('idTemplate');
        \Module::remove_template($parentidtemplate, $idTemplate);

        self::add_response('id', $parentidtemplate);
        self::add_response('index', $idTemplate);

        self::response();
    }

    function get_meta()
    {
        $idTemplate = \GetPost::get('idTemplate');
        echo json_encode(self::get_module_cms_data_by_id($idTemplate));
        $this->set_main_module(1, 1);
        $this->not_render();
    }

    public function save_meta()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $info = \GetPost::get('info');

        $d = self::get_module_cms_data_by_id($idTemplate);
        $d = array_merge($d, $info);
        self::generate_php_template($idTemplate, $d);
    }

    public function update_settings()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $settings = \GetPost::get('settings', []);

        self::update_settings_by_id($idTemplate, $settings);
    }

    public function get_css_list()
    {
        $idTemplate = \GetPost::get('idTemplate');

        self::add_response('$idTemplate', $idTemplate);
        $d = self::get_module_cms_data_by_id($idTemplate);
        $className = $d['name'];
        self::add_response('name', $d['name']);

        if (GClass::autoLoad($className))
        {
            self::add_response('autoload', true);
            $folder = GClass::$classInfo['folder'] . '/css';
            $files = [];

            if (file_exists($folder))
            {
                $files = scandir($folder);
            }

            foreach ($files as $file)
            {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'css')
                {
                    self::add_response('values[]', str_replace('.css', '', $file));
                }
            }
        }

        self::response();
    }

    public function copy_css_list()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $idTemplate_paste = \GetPost::get('idTemplate_paste');
        $list = \GetPost::get('list');

        get_all_templates::copy_css_files($idTemplate, $idTemplate_paste, $list);
    }

    public function delete_css_list()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $list = \GetPost::get('list');

        get_all_templates::delete_css_files($idTemplate, $list);
    }

    public function clone_css_list()
    {
        $idTemplate = \GetPost::get('idTemplate');
        $list = \GetPost::get('list');

        get_all_templates::clone_css_files($idTemplate, $list);
    }

    public function delete($idTemplate = null)
    {
        $idTemplate = $idTemplate ? $idTemplate : \GetPost::get('idTemplate');

        $d = self::get_module_cms_data_by_id($idTemplate);

        if (GClass::getClassInfo($d['name']))
        {
            $this->__remove_dir(GClass::$classInfo['folder']);
        }
    }

    private function __remove_dir($path) // есть метов в файлс!
    {
        if (is_file($path))
        {
            @unlink($path);
        }
        else
        {
            array_map([$this, '__remove_dir'], glob($path . '/*')) == rmdir($path);
            if (file_exists($path))
            {
                rmdir($path);
            }
        }
    }

    public function rename_css()
    {
        $idTemplate = \GetPost::get('idTemplate');
        /** @var string $name */
        $name = \GetPost::get('name');
        /** @var string $new_name */
        $new_name = \GetPost::get('new_name');

        $d = self::get_module_cms_data_by_id($idTemplate);
        if (GClass::autoLoad($d['name']))
        {
            $file = GClass::$classInfo['folder'] . '/css/' . $name . '.css';
            $new_file = GClass::$classInfo['folder'] . '/css/' . $new_name . '.css';
            if (file_exists($file))
            {
                $content = file_get_contents($file);
                $content = str_replace('.' . $name, '.' . $new_name, $content);
                file_put_contents($file, $content);
                rename($file, $new_file);
            }
        }
    }

    public function transfer()
    {
        $path = \GetPost::get('path');
        $idTemplate = \GetPost::get('id');

        if (GetAllTemplates\folder_actions::have_path($path))
        {
            self::add_response('transfer', true);
            $path = $path == DIRECTORY_SEPARATOR ? '' : $path;
            $d = self::get_module_cms_data_by_id($idTemplate);
            $d['path'] = $path;
            self::generate_php_template($idTemplate, $d);
        }
        else
        {
            self::add_response('transfer', false);
        }

        self::response();
    }

    /**
     *
     * @param $idTemplate
     * @param $current_class
     * @param $parent_class
     * @param array $options_data
     * @param string $media_screen_size
     */
    public function update_styles($idTemplate = null, $current_class = null, $parent_class = null, $options_data = [], $media_screen_size = 'standart_screen_size_fulscreen')
    {
        // если приходит значение destroy то не сохранять, а удалять значение!
        $this->idTemplate = \GetPost::get('idTemplate', $idTemplate);
        $this->current_class = \GetPost::get('current_class', $current_class);
        $this->parent_class = \GetPost::get('parent_class', $parent_class);
        $this->options_data = \GetPost::get('options_data', $options_data);
        $this->media_screen_size = \GetPost::get('media_screen_size', $media_screen_size);

        $styles_data = $this->__load_styles($this->idTemplate, $this->current_class);

        if (isset($styles_data[$this->media_screen_size]['options']))
        {
            $styles = isset($styles_data[$this->media_screen_size]['options']) ? $styles_data[$this->media_screen_size]['options'] : [];
            $styles = array_merge($styles, $this->options_data);

            foreach ($styles as $k => $stl)
            {
                if (empty($stl))
                {
                    unset($styles[$k]);
                }
            }

            $styles_data[$this->media_screen_size]['options'] = $styles;
        }
        else
        {
            $styles_data[$this->media_screen_size] = [];
            $styles_data[$this->media_screen_size]['options'] = $this->options_data;
        }

        if ($this->idTemplate)
        {
            $data = \Module::get_module_cms_data_by_id($this->idTemplate);

            $className = $data['name'];
            $this->module_name = '.' . str_replace('\\', '__', $className);
            GClass::getClassInfo($className);
            $this->templateFolder = GClass::$classInfo['folder'];
            $this->__array_to_css($styles_data);
        }
    }

    /**
     * @param $idTemplate
     * @param $current_class
     * @return array
     */
    private function __load_styles($idTemplate, $current_class)
    {
        $data = \Module::get_module_cms_data_by_id($idTemplate);
        $className = $data['name'];
        GClass::getClassInfo($className);
        $this->templateFolder = GClass::$classInfo['folder'];


        $current_class = str_replace(' ', '.', trim(str_replace('  ', ' ', $current_class)));

        $filepath = $this->templateFolder . '/css/' . $current_class . '.css';
        $css = '';


        if (file_exists($filepath))
        {
            $css = file_get_contents($this->templateFolder . '/css/' . $current_class . '.css');
        }

        if (isset($current_class) && !empty($current_class))
        {
            return $this->__preparse_find_media_screens($css, $current_class);
        }

        return [];
    }

    private function __preparse_find_media_screens($css, $current_class)
    {
        $sizes = [];
        $result = [];
        $screen_css = [];

        $current_class = '.' . str_replace(' ', '.', str_replace('  ', ' ', $current_class));
        preg_match_all('~@media screen and \((.*)\)~Usm', $css, $sizes);

        foreach ($sizes[1] as $size)
        {
            preg_match_all('~@media screen and \(' . $size . '\).*\{(.*)\}~Usm', $css, $screen_css);
            foreach ($screen_css[1] as $css_string)
            {
                $result[$size] = $this->__parse_in_options($css_string . '}', $current_class);
            }
        }

        $result['standart_screen_size_fulscreen'] = $this->__parse_in_options($css, $current_class);

        return $result;
    }

    private function __parse_in_options($css, $current_class)
    {
        $result = [];

        preg_match('~\\' . $current_class . '.*\{(.*)\}~Usm', $css, $result);

        $moduleOptions = isset($result[1]) ? $result[1] : '';
        $this->__php_class = trim(str_replace(isset($result[0]) ? $result[0] : '', '', $css));

        preg_match_all('~(.*):(.*);~Usm', $moduleOptions, $result);

        $this->options = [];

        if (isset($result[1]))
        {
            foreach ($result[1] as $index => $key)
            {
                $key = str_replace("\n", '', str_replace(' ', '', str_replace('-', '_', $key)));
                $this->options[$key] = trim($result[2][$index]);
            }
        }

        return ['options' => $this->options, 'styles' => $this->__php_class, 'class' => $current_class];
    }

    //load styles

    /**
     * Генерирует css стили в файл css
     *
     * @param $css_styles массив стилей для генерации
     */
    private function __array_to_css($css_styles)
    {
        $css = '';

        if (isset($this->current_class) && $this->current_class && $this->parent_class !== $this->current_class)
        {
            $this->module_name .= ' ';
        }

        if ($this->current_class) // если указаны размеры, то это вписывается в этот же файл, но обрамляется screen
        {
            $css_styles = $this->__resort_sizes($css_styles);
            foreach ($css_styles as $size => $css_array)
            {
                $__options = isset($css_array['options']) ? $css_array['options'] : [];
                if ($size == 'standart_screen_size_fulscreen')
                {
                    $css = $this->__generate_css_string($__options) . "\n\n" . $css;
                }
                else
                {
                    $css .= $this->__media_screen_generate($size, $this->__generate_css_string($__options));
                }
            }

            if (!file_exists($this->templateFolder . '/css'))
            {
                mkdir($this->templateFolder . '/css');
            }


            file_put_contents($this->templateFolder . '/css/' . self::__standart_reform_css($this->current_class) . '.css', $css);
            // как сюда в current class попало assets\bootstrap
        }
        else
        {
            echo json_encode(['error' => 'NO css class', 'error_code' => 0]);
        }
    }

    private function __resort_sizes($array)
    {
        $new_sizes = [];
        $new_keys = [];
        $min = 1000000;
        $max = -1000000;
        $integer = [];

        foreach ($array as $size => $ar)
        {
            if ($size != 'standart_screen_size_fulscreen')
            {
                preg_match('~:(.*)px~', $size, $integer);
                $sizei = isset($integer[1]) ? (int)$integer[1] : 0;

                if ($sizei <= $min)
                {
                    $min = $sizei;
                    array_push($new_sizes, $ar);
                    array_push($new_keys, $size);
                }
                else if ($sizei > $max)
                {
                    $max = $sizei;
                    array_unshift($new_sizes, $ar);
                    array_unshift($new_keys, $size);
                }
            }
        }

        if (isset($array['standart_screen_size_fulscreen']))
        {
            array_unshift($new_sizes, $array['standart_screen_size_fulscreen']);
            array_unshift($new_keys, 'standart_screen_size_fulscreen');
        }

        $new_sizes = array_combine($new_keys, $new_sizes);

        return $new_sizes;
    }

    private function __generate_css_string($css_styles)
    {
        $children_selector = $this->parent_class !== $this->current_class ? '[parentidtemplate="' . $this->idTemplate . '"]' : '';
        $css = $this->module_name . '.' . self::__standart_reform_css($this->current_class) . $children_selector . "\n{\n";

        foreach ($css_styles as $key => $style)
        {
            if ($style != 'destroy')
            {
                $css .= "   " . str_replace('_', '-', str_replace('style_', '', $key)) . ': ' . $style . ";\n";
            }
        }

        $css .= "}\n\n";

        return $css;
    }

    private static function __standart_reform_css($css_class_name)
    {
        $css_class_name = str_replace('\\', '__', $css_class_name);
        $css_class_name = str_replace('  ', ' ', $css_class_name);
        $css_class_name = str_replace(' ', '.', $css_class_name);
        $css_class_name = str_replace('..', '.', $css_class_name);

        return $css_class_name;
    }

    /**
     *
     * @param string $size
     * @param string $css_string
     * @return string
     */
    private function __media_screen_generate($size, $css_string)
    {
        return "\n\n@media screen and (" . $size . ")\n{\n" . $css_string . "\n}";
    }

    /**
     * Получение весх опций по шаблону - модулю
     *
     * @param $return
     * @return array
     */
    public function load_styles($return = false)
    {
        $this->idTemplate = \GetPost::get('idTemplate');
        $this->current_class = \GetPost::get('current_class');
        $this->__mini = \GetPost::get('mini', false);
        $this->media_screen_size = \GetPost::get('media_screen_size', 'standart_screen_size_fulscreen');

        if ($this->idTemplate)
        {
            $ret = [];
            $styles = $this->__load_styles($this->idTemplate, $this->current_class);

            if ($this->__mini)
            {
                $ret['styles'] = isset($styles[$this->media_screen_size]['options']) ? $styles[$this->media_screen_size]['options'] : [];
            }
            else
            {
                $ret['styles'] = isset($styles[$this->media_screen_size]) ? $styles[$this->media_screen_size] : [];
            }

            $ret['CMSData'] = self::get_module_cms_data_by_id($this->idTemplate);

            if ($return)
            {
                return $ret;
            }
            else
            {
                echo json_encode($ret);
                die();
            }
        }

        if (routes::is_ajax())
        {
            $this->set_main_module();
            $this->not_render();
        }

        return [];
    }

}
