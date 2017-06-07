<?php

class logger extends Autoload
{

    /**
     * 
     * @param sting $name имя файла не путь, все логи в корне
     * @param string|array|object|int|bool $data
     * @param boolean $clear = false или размер после которого файл будет очищаться
     */
    public static function write($name, $data, $clear = false)
    {
        $path = self::__get_path($name);

        if (!file_exists(HOME_PATH . 'logs'))
        {
            mkdir(HOME_PATH . 'logs');
        }

        if (file_exists($path) && filesize($path) > 20 * 8 * 1024 * 100)
        {
            file_put_contents($path, self::__encode($data));
        }

        if (!is_bool($clear))
        {
            if (filesize($path) > $clear)
            {
                $clear = true;
            }
        }

        if (!file_exists($path) || (is_bool($clear) && $clear))
        {
            file_put_contents($path, self::__encode($data));
        }
        else
        {
            file_put_contents($path, "\n" . self::__encode($data), FILE_APPEND);
        }
    }

    private static function __encode($data)
    {
        if (is_string($data))
        {
            return $data;
        }
        else
        {
            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    public static function delete($name)
    {
        unlink(self::__get_path($name));
    }

    private static function __get_path($name)
    {
        $name = str_replace(['/', '\\'], '', $name);
        return HOME_PATH . 'logs' . DIRECTORY_SEPARATOR . $name . '.log.txt';
    }

    public static function read($name)
    {
        $path = self::__get_path($name);
        if (file_exists($path))
        {
            return file_get_contents($path);
        }
    }

}
