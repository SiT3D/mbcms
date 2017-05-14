<?php

namespace MBCMS\Site;

use MBCMS\mbcms_assets;
use MBCMS\View;

class wrapper extends \Module
{

    protected static $__page_title = '';
    protected static $__page_description = '';
    protected static $__page_metakeywords = '';
    public $charset = 'utf8';
    public $_css = [];
    public $top_js = [];
    public $bottom_js = [];
    public $__cms_favicon = '/favicon.ico';

    public function __construct()
    {
        parent::__construct();

        $this->page_title = '';
        $this->page_description = '';
        $this->page_metakeywords = '';
    }

    public static function set_prioritets(&$files)
    {
        $wr = new wrapper();
        $wr->setFilePrioritet($files['css']);
        $wr->setFilePrioritet($files['top_js']);
        $wr->setFilePrioritet($files['bottom_js']);
    }

    private function setFilePrioritet(&$files)
    {
        $hightPriory = [];
        $hightPriorys = [];
        $lowPriory = [];


        foreach ($files as $file)
        {
            $count = 0;
            foreach (str_split($file['name']) as $word)
            {
                if ($word === '!')
                {
                    $count++;
                }
            }

            if ($count === 0)
            {
                $lowPriory[] = $file;
            }
            else
            {
                $hightPriorys[$count][] = $file;
            }
        }

        krsort($hightPriorys);

        foreach ($hightPriorys as $hightPrioryA)
        {
            $hightPriory = array_merge($hightPrioryA, $hightPriory);
        }

        $files = array_merge($hightPriory, $lowPriory);
    }

    /**
     * Тут мы задаем псевдовсплытие, при котором заменяются метатеги, согласно установленным параметрам.
     * Дело в том что это первый способ который мне пришел в голову, как объединить второй цикл сборки из static_nature
     * с уже отрисованным модулем wrapper для формирования динамической шапки, но тут приходится парсить страницу...
     * Поэтому этот способ будет медленным, и нежелательным, при большом количестве заменяемой информации.
     *
     * В будущем возможно я придумаю как сделать это более правильно.
     *
     * @param $html
     * @return mixed
     */
    public static function propagation($html)
    {
        if (!$html instanceof View)
        {
            return $html;
        }

        $html = $html->render();

        $html = str_replace('{{page_title}}', self::getPageTitle(), $html);
        $html = str_replace('{{page_description}}', self::getPageDescription(), $html);
        $html = str_replace('{{page_metakeywords}}', self::getPageMetawords(), $html);


        return $html;
    }

    /**
     * @return string
     */
    public static function getPageTitle()
    {
        return self::$__page_title;
    }

    /**
     * @param string $value
     */
    public static function setPageTitle($value)
    {
        self::$__page_title = $value;
    }

    /**
     * @return string
     */
    public static function getPageDescription()
    {
        return self::$__page_description;
    }

    /**
     * @param string $value
     */
    public static function setPageDescription($value)
    {
        self::$__page_description = $value;
    }

    /**
     * @return string
     */
    public static function getPageMetawords()
    {
        return self::$__page_metakeywords;
    }

    /**
     * @param string $value
     */
    public static function setPageMetawords($value)
    {
        self::$__page_metakeywords = $value;
    }

    public function preview()
    {
        $files = \Modules::get_all_files();

        $this->setFilePrioritet($files['css']);
        foreach ($files['css'] as $file)
        {
            $this->_css[] = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $file['metapath']);
        }

        $this->setFilePrioritet($files['top_js']);
        foreach ($files['top_js'] as $file)
        {
            $name = $file['derictory'] . DIRECTORY_SEPARATOR . $file['name'];
            $this->top_js[$name] = $file['metapath'];
        }

        $this->setFilePrioritet($files['bottom_js']);
        foreach ($files['bottom_js'] as $file)
        {
            $name = $file['derictory'] . DIRECTORY_SEPARATOR . $file['name'];
            $this->bottom_js[$name] = $file['metapath'];
        }
    }
}
