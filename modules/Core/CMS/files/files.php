<?php

namespace MBCMS;

class files
{

    const PATH_CLASSES = __DIR__ . DIRECTORY_SEPARATOR . 'jsons' . DIRECTORY_SEPARATOR . 'classes.json';
    const PATH_PAGES   = __DIR__ . DIRECTORY_SEPARATOR . 'jsons' . DIRECTORY_SEPARATOR . 'pages.json';
    const PATH_FOLDERS = __DIR__ . DIRECTORY_SEPARATOR . 'jsons' . DIRECTORY_SEPARATOR . 'folders.json';

    public static  $all_files     = [''];
    private static $upload_errors = ['ext' => [], 'size' => []];

    /**
     *
     * @param $src
     * @param $dst
     */
    static function copy_folder($src, $dst)
    {
        if (is_dir($src))
        {
            mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file)
            {
                if ($file != "." && $file != "..")
                {
                    self::copy_folder("$src/$file", "$dst/$file");
                }
            }
        }
        else if (file_exists($src))
        {
            copy($src, $dst);
        }
    }

    /**
     *
     * @param string $path
     * @param null $oldest_seconds удалять только файлы которые старше (time() - $oldest (seconds))
     * указывать в секундах.
     * Пример: 3600 * 24 * 10 - удалит файлы старше 10 дней
     */
    static function remove_dir($path, $oldest_seconds = null)
    {

        if (preg_match('~/CMS~', $path) || !$path)
        {
            return;
        }

        if ($path == HOME_PATH . 'modules')
        {
            return;
        }

        if ($path == HOME_PATH)
        {
            return;
        }

        $path = str_replace(['//', '\\\\'], ['/', '\\'], $path);

        if (file_exists($path))
        {
            if (is_file($path) && (!$oldest_seconds || ($oldest_seconds && filemtime($path) < time() - $oldest_seconds)))
            {
                unlink($path);
            }
            else
            {
                if ($__pathes = glob($path . '/*'))
                {
                    foreach ($__pathes as $item)
                    {
                        $times[] = $oldest_seconds;
                    }

                    array_map('MBCMS\\files::remove_dir', $__pathes, $times);
                    @rmdir($path);
                }
            }
        }
    }

    /**
     *
     * @param $filename
     * @param $assoc is array if true else object
     */
    static function get_json($filename, $assoc = false)
    {
        if (file_exists($filename))
        {
            return json_decode(file_get_contents($filename), $assoc);
        }

        return $assoc ? [] : new \stdClass();
    }

    /**
     *
     * @param $filename
     * @param $array
     */
    static function set_json($filename, $array)
    {
        if (file_exists($filename))
        {
            file_put_contents($filename, json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * this.form.find('[type=file]').replaceWith(this.form.find('[type=file]').clone());// как вариант очистки поля с файлами
     *
     *
     * @param string $path
     * @param string $key
     * @param array $white_extensions if [] that is true for all
     * @param int $max_size 100mb
     * @return array - пути к загруженным файлам
     */
    static function upload($path, $key = 'file', $white_extensions = [], $max_size = 1024 * 1024 * 100)
    {
        $pathes = [];
        $path   = preg_replace_callback('~/$~', function ()
        {
            return '';
        }, $path);

        if (!isset($_FILES[$key]))
        {
            return [];
        }


        if (is_array($_FILES[$key]["error"]))
        {
            foreach ($_FILES[$key]["error"] as $index => $error)
            {
                if ($error == UPLOAD_ERR_OK)
                {
                    $tmp_name = $_FILES[$key]["tmp_name"][$index];
                    $filename = trim(basename($_FILES[$key]["name"][$index]));
                    $ext      = self::__get_extension($filename);

                    if (!self::__valid_ext($ext, $white_extensions))
                    {
                        self::$upload_errors['ext'][$filename] = $white_extensions;
                        continue;
                    }

                    $filesize = filesize($tmp_name);

                    if ($filesize > $max_size)
                    {
                        self::$upload_errors['size'][$filename] = [$filesize, $max_size];
                        continue;
                    }


                    $global_path = $path . DIRECTORY_SEPARATOR . basename($_FILES[$key]["name"][$index]);
                    self::create_path_dirs($global_path);
                    move_uploaded_file($tmp_name, $global_path);
                    $pathes[] = $global_path;
                }
            }
        }
        else
        {
            if ($_FILES[$key]["error"] == UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES[$key]["tmp_name"];
                $filename = trim(basename($_FILES[$key]["name"]));
                $ext      = self::__get_extension(basename($_FILES[$key]["name"]));

                if (!self::__valid_ext($ext, $white_extensions))
                {
                    self::$upload_errors['ext'][$filename] = $white_extensions;

                    return [];
                }

                $filesize = filesize($tmp_name);

                if ($filesize > $max_size)
                {
                    self::$upload_errors['size'][$filename] = [$filesize, $max_size];

                    return [];
                }

                $global_path = $path . DIRECTORY_SEPARATOR . basename($_FILES[$key]["name"]);
                self::create_path_dirs($global_path);
                move_uploaded_file($tmp_name, $global_path);
                $pathes[] = $global_path;
            }
        }

        return $pathes;
    }

    private static function __get_extension($filename)
    {
        return files::get_extension($filename);
    }

    /**
     *
     * @param $filename
     */
    public
    static function get_extension($filename)
    {
        $info = new \SplFileInfo($filename);

        return $info->getExtension();
    }

    private static function __valid_ext($ext, $white_list)
    {
        if (count($white_list) == 0)
        {
            return true;
        }

        if (in_array($ext, $white_list))
        {
            return true;
        }

        return false;
    }

    /**
     *
     * @param string $global_path example: dir/dir2/file.txt - последняя часть всегда является файлом, и не будет учтена для создания папки!
     * @param string $start_path :: HOME_PATH . DIRECTORY_SEPARATOR . self::FOLDER_NAME
     */
    public static function create_path_dirs($global_path, $start_path = '')
    {
        $global_path = str_replace(HOME_PATH, '', $global_path);
        $pieces      = explode(DIRECTORY_SEPARATOR, $global_path);
        array_pop($pieces);
        $current_path = $start_path;

        foreach ($pieces as $piece)
        {
            $current_path .= DIRECTORY_SEPARATOR . $piece;
            $current_path = str_replace(['//', '\\\\'], DIRECTORY_SEPARATOR, $current_path);

            if (!file_exists($current_path))
            {
                @mkdir($current_path);
            }
        }
    }

    /**
     * @return array
     */
    public static function get_upload_errors()
    {
        return self::$upload_errors;
    }

    /**
     * @return bool
     */
    public static function is_upload_errors()
    {
        if (count(self::$upload_errors['ext']) || count(self::$upload_errors['size']))
        {
            return true;
        }

        return false;
    }

    public
    static function get_files_in_dir($dir, $etc = '')
    {
        if (!file_exists($dir))
        {
            return [];
        }

        $find = false;

        for ($i = 0; $i < count(self::$all_files); $i++)
        {
            if (self::$all_files[$i] == $dir)
            {
                $find = true;
                break;
            }
        }

        if (!$find)
        {
            self::$all_files[] = $dir;

            if ($handle = opendir($dir))
            {
                $a = [];

                while (($file = readdir($handle)) !== false)
                {

                    if ($file !== '.' && $file !== '..' && !is_dir($dir . DIRECTORY_SEPARATOR . $file) && preg_match('~\.' . $etc . '~i', $file) === 1)
                    {
                        $a[] = $file;
                    }
                }

                closedir($handle);
            }

            return $a;
        }
        else
        {
            return [];
        }
    }

}
