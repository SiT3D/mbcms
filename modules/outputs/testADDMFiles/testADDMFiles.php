<?php

namespace MBCMS;

configuration::factory()->is_superadmin_access_die();

class testADDMFiles extends out
{
    private $content              = '';
    private $__classes            = [];
    private $__current_class_name = '';
    private $__find_array         = [];

    public function static_nature()
    {
        return $this->__static_nature();
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new block(),
        ];
    }

    public function init()
    {
        parent::init();

        \logger::write('testADDMFiles', '', true);

        $classes = files::get_json(files::PATH_CLASSES);

        foreach ($classes as $class)
        {
            $this->__classes = [];
            $items           = explode('/', $class->script);

            $last    = array_pop($items);
            $prelast = array_pop($items);

            if (str_replace('.php', '', $last) == $prelast)
            {
                $this->__current_class_name = $class->class;
                if (file_exists($class->script))
                {
                    $this->content = file_get_contents($class->script);
                }

                if ($this->content)
                {
                    $this->__parse();
                    $this->__unsets_classes();
                    $this->__equales();
                }
            }
        }

        $this->ADDM(block::factoryLink('Без учета переменных', routes::link('testADDMfiles', '?v=1')), 'modules');
        $this->ADDM(block::factoryLink('C четом переменных', routes::link('testADDMfiles')), 'modules');
        $this->ADDM(block::factoryLink('Главная страница', routes::link('main_page')), 'modules');
        $this->__text = '<pre>' . \logger::read('testADDMFiles') . '</pre>';

    }

    private function __parse()
    {
        $this->__find_ADDM();
    }

    private function __find_ADDM()
    {
        preg_match_all('~ADDM.*~', $this->content, $m2);
        preg_match_all('~ADDM\s*\(+?(.*)\)+.*(;|,|$)*~U', $this->content, $m);

        foreach ($m[1] as $current)
        {
            if (!$current)
            {
                continue;
            }

            $this->__get_type($current);
        }
    }

    private function __get_type($current)
    {
        $current = trim($current);

        switch ($current[0])
        {
            case '$':
                $this->__get_class_by_variable($current);
                break;
            case 'n':
                $this->__get_newclass_name($current);
                break;
            default :
                $this->__get_static_class_name($current);
                break;
        }
    }

    private function __get_class_by_variable($current)
    {

        preg_match('~(\$.*)(\s|,)~U', $current, $m);
        $variable = isset($m[1]) ? $m[1] : '';
        $variable = str_replace(['$', '(', ')'], '.', $variable);

        if ($variable)
        {
            preg_match_all("~\\{$variable}\s*=.*\snew(.*)(\(|;|,)~U", $this->content, $m); // todo: тут чтото наверное

            foreach ($m[1] as $item)
            {
                if (isset($this->__classes[$item]))
                {
                    continue;
                }

                $item = trim($item);
                if (!strstr('new', $item))
                {
                    $this->__classes[$item] = true;
                }
            }

            preg_match_all("~\\{$variable}\s*=\s*(\w*)::~U", $this->content, $m);

            foreach ($m[1] as $item)
            {
                if (isset($this->__classes[$item]))
                {
                    continue;
                }

                $item                   = trim($item);
                $this->__classes[$item] = true;
            }
        }
    }

    private function __get_newclass_name($current)
    {
        preg_match('~(\s|^)*new(.*)(\(|;|,|\))*~', $current, $m);

        $item = isset($m[2]) ? $m[2] : '';

        preg_match('~[\w_]+~', $item, $m);

        $item = isset($m[0]) ? $m[0] : '';

        if ($item && !isset($this->__classes[$item]))
        {
            if (!strstr('new', $item))
            {
                $this->__classes[$item] = true;
            }
        }
    }

    private function __get_static_class_name($current)
    {
        preg_match('~(.*)::~U', $current, $m);

        $item = isset($m[1]) ? $m[1] : '';

        if ($item && !isset($this->__classes[$item]))
        {
            $this->__classes[$item] = true;
        }
    }

    private function __unsets_classes()
    {
        unset($this->__classes['routes']);
        unset($this->__classes['self']);
    }

    private function __equales()
    {
        if (!count($this->__classes))
        {
            return;
        }

        $class = null;

        try
        {
            if (\GClass::autoLoad($this->__current_class_name))
            {
                $class = new $this->__current_class_name(null, null, null, null, null, null, null, null, null);
            }
        }
        catch (\Exception $e)
        {

        }

        if (!$class)
        {
            return;
        }

        if (method_exists($class, 'init_files'))
        {
            $filesModules = $class->init_files();

            if (!$filesModules && count($this->__classes))
            {
                \logger::write('testADDMFiles',
                    [
                        'class' => $this->__current_class_name,
                        'needs' => '<br> new ' . implode('<br> new ', array_keys($this->__classes)) . '<br>',
                    ]);
            }
            else
            {
                $this->__find_array = [];
                $this->__get_find_array($filesModules);

                $need_elements = [];
                $classes_keys  = array_keys($this->__classes);

                foreach ($classes_keys as $current_class)
                {
                    $current_class = trim($current_class);
                    $current_class = $current_class[0] == '\\' ? substr($current_class, 1) : $current_class;

                    if (!in_array($current_class, $this->__find_array))
                    {
                        $need_elements[] = $current_class;
                    }
                }

                foreach ($need_elements as $i => $need_element)
                {
                    if (!$need_element || (\GetPost::uget('v') && preg_match('~\$~', $need_element)))
                    {
                        unset($need_elements[$i]);
                    }
                }

                if (count($need_elements))
                {
                    \logger::write('testADDMFiles', [
                        'class' => $this->__current_class_name,
                        'needs' => '<br> new ' . implode('<br> new ', $need_elements) . '<br>',
                    ]);
                }
            }

        }

    }

    private function __get_find_array($filesModules)
    {

        if (!$filesModules)
        {
            return;
        }

        $__alias = $this->__get_aliases();

        if ($__alias && count($__alias))
        {
            $this->__find_array = array_merge($this->__find_array, $__alias);
        }


        foreach ($filesModules as $filesModule)
        {
            if ($filesModule && $filesModule instanceof \Module)
            {
                $name = get_class($filesModule);

                if (method_exists($filesModule, 'init_files'))
                {
                    $this->__get_find_array($filesModule->init_files());
                }

                $this->__add_find_array($name);
                $ar  = explode('\\', $name);
                $ar2 = explode('\\', $name, 2);
                $ar3 = explode('\\', $name, 3);
                $ar4 = explode('\\', $name, 4);
                $ar5 = explode('\\', $name, 5);
                $this->__add_find_array(array_pop($ar));
                $this->__add_find_array(array_pop($ar2));
                $this->__add_find_array(array_pop($ar3));
                $this->__add_find_array(array_pop($ar4));
                $this->__add_find_array(array_pop($ar5));
            }

            if (is_array($filesModule))
            {
                $this->__get_find_array($filesModule);
            }
        }
    }

    private function __get_aliases()
    {
        preg_match_all("~use(.*)\sas\s(.*)(;|$)~", $this->content, $m);

        if (count($m[2]))
        {
            return $m[2];
        }

        return null;
    }

    private function __add_find_array($value)
    {
        if (is_string($value) && !in_array($value, $this->__find_array))
        {
            $this->__find_array[] = $value;
        }
    }
}














