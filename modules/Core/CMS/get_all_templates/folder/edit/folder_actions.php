<?php

namespace MBCMS\GetAllTemplates;

class folder_actions implements \adminAjax
{

    public $action;
    public $id                              = '';
    public $name                            = '';
    public $parentPath                      = '';
    private static $all_file_folders        = null;
    private static $all_file_folders_update = false;
    private $__new_old_path                 = '';

    public function __construct()
    {
        self::get_all_folders();
    }

    public static function update_file()
    {
        if (self::$all_file_folders_update)
        {
            \MBCMS\files::set_json(\MBCMS\files::PATH_FOLDERS, self::$all_file_folders);
        }
    }

    /**
     * Добавляет новую папку в указанную папку
     *
     * @param string $name | \GetPost(name);
     * @param string $path | \GetPost(path);
     */
    public function add($name = null, $path = null)
    {
        $name = $name ? $name : \GetPost::get('name', '');
        $path = self::normalpath($path ? $path : \GetPost::get('path', ''));

        $fullPath = self::normalpath($path . DIRECTORY_SEPARATOR . $name);
        if (!isset(self::$all_file_folders->$fullPath))
        {
            self::$all_file_folders->$fullPath = ['path' => $path, 'name' => $name];
        }

        self::$all_file_folders_update = true;
    }

    public static function get_all_folders()
    {
        if (!self::$all_file_folders)
        {
            self::$all_file_folders = \MBCMS\files::get_json(\MBCMS\files::PATH_FOLDERS);
        }

        return self::$all_file_folders;
    }

    /**
     * Удаляет элемент по id
     *
     * @param string $fullPath | \GetPost(path);
     */
    public function del($fullPath = null)
    {
        $fullPath = $fullPath ? $fullPath : \GetPost::get('fullPath', '');
        $fullPath = self::normalpath($fullPath);

        if ($fullPath != '')
        {
            unset(self::$all_file_folders->$fullPath);
            $this->recursiveDel($fullPath);
        }

        self::$all_file_folders_update = true;
    }

    private function recursiveDel($fullPath = null)
    {
        if ($fullPath == '')
        {
            return;
        }

        $fullPath = self::normalpath($fullPath);

        foreach (self::$all_file_folders as $fpath => $folder)
        {
            if ($folder->path == $fullPath)
            {
                $this->del($fpath);
            }
        }

        $all_templates   = \MBCMS\template::get_all_templates();
        $templateDeleter = new \MBCMS\template;

        foreach ($all_templates as $template)
        {
            $d = \Module::get_module_cms_data_by_id($template);
            if ($d->path == $fullPath)
            {
                $templateDeleter->delete($template);
            }
        }
    }

    /**
     *
     * Проверяет существование пути
     *
     * @param $fullPath
     * @return boolean
     */
    public static function have_path($fullPath = null)
    {
        $fullPath = self::normalpath($fullPath);
        if ($fullPath == '/' || $fullPath == '')
        {
            return true;
        }

        self::$all_file_folders = self::$all_file_folders ? self::$all_file_folders : self::get_all_folders();

        return isset(self::$all_file_folders->$fullPath) ? true : false;
    }

    /**
     * Переносит папку в указанную папку
     *
     * @param string $oldFullPath | \GetPost(oldPath);
     * @param string $newPath | \GetPost(newPath);
     * @param string $newName | \GetPost(newName);
     */
    public function transfer($oldFullPath = null, $newPath = null, $newName = null)
    {
        // перебираем папки все, удаляем по фулпату и создаем по новому фулпату

        $oldFullPath = self::normalpath($oldFullPath ? $oldFullPath : \GetPost::get('oldFullPath', ''));
        $newPath     = self::normalpath($newPath ? $newPath : \GetPost::get('newPath', '/'));
        $newName     = $newName ? $newName : \GetPost::get('newName');
        $name        = $newName ? $newName : self::get_name($oldFullPath);

        if (!self::have_path($newPath))
        {
            die('false');
        }

        $newFullPath = self::normalpath($newPath . '/' . $name);
        if (!self::have_path($newFullPath))
        {
            $this->recursiveTransfer($oldFullPath, $newPath, $name);
        }
        else
        {
            die('false');
        }

        self::$all_file_folders_update = true;
    }

    /**
     * Добавляет новую папку в указанную папку
     *
     * @param string $this->id | \GetPost(id);
     * @param string $this->name | \GetPost(name);
     */
    public function rename()
    {
        $fullPath = self::normalpath(\GetPost::get('fullPath', ''));
        $name     = str_replace('/', '', \GetPost::get('name', ''));

        $this->transfer($fullPath, self::get_path($fullPath), $name);

        self::$all_file_folders_update = true;
    }

    private function recursiveTransfer($oldFullPath, $newPath, $newName = null)
    {
        $oldFullPath          = self::normalpath($oldFullPath);
        $newPath              = self::normalpath($newPath);
        $oldPath              = self::get_path($oldFullPath);
        $this->__new_old_path = $this->__new_old_path ? $this->__new_old_path : $oldFullPath;

        $oldName = self::get_name($oldFullPath);

        foreach (self::$all_file_folders as $fpath => $folder)
        {

            if ($fpath == $oldFullPath)
            {
                $name                             = $newName ? $newName : $folder->name;
                $folder->path                     = $newPath;
                $folder->name                     = $name;
                $__fpath                          = self::normalpath($newPath . '/' . $name);
                $this->__new_old_path             = $__fpath;
                self::$all_file_folders->$__fpath = $folder;
                unset(self::$all_file_folders->$fpath);
            }

            if ($oldFullPath == $folder->path)
            {
                $__name                           = $newName ? $newName : $oldName;
                $__new_path                       = self::normalpath($oldPath . '/' . $__name);
                $folder->path                     = self::normalpath($this->__new_old_path);
                $__fpath                          = self::normalpath($__new_path . '/' . $folder->name);
                self::$all_file_folders->$__fpath = $folder;
                $this->recursiveTransfer($fpath, $this->__new_old_path);
                unset(self::$all_file_folders->$fpath);
            }
        }
    }

    private static function normalpath($path)
    {
        if (trim($path) == '')
        {
            return '/';
        }

        return str_replace('//', '/', $path);
    }

    private static function get_path($fullPath)
    {
        $array = explode('/', $fullPath);
        array_pop($array);
        return self::normalpath(implode('/', $array));
    }

    private static function get_name($fullPath)
    {
        $array = explode('/', $fullPath);
        return array_pop($array);
    }

}
