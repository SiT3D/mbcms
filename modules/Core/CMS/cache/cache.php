<?php

namespace MBCMS;

class cache
{

    const MINUTES = 60;
    const SECONDS = 1;
    const HOURS   = 3600;
    const DAYS    = 3600 * 24;
    const FOLDER  = 'cache';
    private static $__current_time = 0;
    /**
     *
     * @var type // секунды на 1 единицу времени
     */
    private $__multiplicator = 60;  // считать его по выполнениям кеша суммировать только а не все время с другой логикой
    private $__limit         = 0.035;
    private $__key, $__life_time, $__force;
    private $__file          = null;

    /**
     *
     * @param $key ключ для сохранения
     * @param $life_time - время жизни в минутах
     * @param $force - игнорировать дату создания и жизни
     */
    public function __construct($key, $life_time = 60, $force = false)
    {
        $this->__key       = $key;
        $this->__life_time = $life_time;
        $this->__force     = $force;
    }

    public static function get_files_dir()
    {
        return self::__dir();
    }

    private static function __dir()
    {
        $dir = HOME_PATH . self::FOLDER;

        if (!file_exists($dir))
        {
            mkdir($dir);
        }

        return $dir . DIRECTORY_SEPARATOR;
    }

    /**
     * Удаляет раз в 5 дней по умолчанию
     * Удаляет весь кеш!! Тоесть все пользователи будут разлогинены.
     */
    public static function delete()
    {
        if (self::timer('remove_all_caches_files', 5, self::DAYS))
        {
            files::remove_dir(self::__dir(), 3600 * 24 * 10); // удалить кеш старше 12 дней
        }
    }

    /**
     * @param $unical_key
     * @param $time
     * @param $multiplicator
     * @return bool
     */
    public static function timer($unical_key, $time, $multiplicator)
    {
        $cache = new cache($unical_key, $time);
        $cache->set_multiplicator($multiplicator);
        $rand = rand(0, 10000000000);
        $true = $cache->result(function () use ($rand)
        {
            return $rand;
        });

        return $true == $rand;
    }

    /**
     *
     * @param $val = 60 // это мин, показатель говорит о том сколько сек приходится на 1 ед указанного времени $life_time
     */
    public function set_multiplicator($val)
    {
        $this->__multiplicator = $val < 1 ? 1 : (int)$val;
    }

    /**
     *
     * @param function $callback - функция которая выполняется в случае смерти файла с кешем или его отсутствии
     * @return mixed|null|string
     */
    public function result($callback)
    {

        $work_timer = 0;

        if (self::$__current_time != 0)
        {
            $work_timer = microtime_float();
        }

        $filename = self::__filename();

        $result_data = null;

        if (!is_callable($callback))
        {
            $result_data = 'NOT CALLABLE!!';
        }
        else if ($this->__force)
        {
            if (self::$__current_time == 0)
            {
                $work_timer = microtime_float();
            }

            $data = call_user_func($callback);
            self::__write($filename, $data);
            $result_data = $data;
        }
        else if ($this->__check())
        {
            $result_data = self::__read($filename);
        }
        else
        {
            if (self::$__current_time == 0)
            {
                $work_timer = microtime_float();
            }

            $data = call_user_func($callback);
            self::__write($filename, $data);
            $result_data = $data;
        }

        if ($work_timer)
        {
            self::$__current_time += microtime_float($work_timer, 'title', false);
        }


        return $result_data;
    }

    private function __filename()
    {
        $cache_name   = md5($this->__key);
        $this->__file = $cache_name;
        $md5_path     = cache::get_md5_path($cache_name);
        files::create_path_dirs($md5_path, self::__dir());

        return str_replace(['//', '\\\\'], DIRECTORY_SEPARATOR, self::__dir() . $md5_path);
    }

    /**
     * @param string $md5_string
     * @param int $deep
     * @return string
     */
    public static function get_md5_path($md5_string, $deep = 7)
    {
        $md5_string = str_replace(['//', '\\'], DIRECTORY_SEPARATOR, $md5_string);

        if (preg_match('~/+|\\+~', $md5_string))
        {
            return $md5_string;
        }


        $all    = str_split($md5_string, 2);
        $result = '';

        for ($i = 0; $i < $deep; $i++)
        {
            $result .= isset($all[$i]) ? DIRECTORY_SEPARATOR . $all[$i] : '';
        }

        for ($i = $deep; $i < count($all); $i++)
        {
            $result .= isset($all[$i]) ? $all[$i] : '';
        }

        return $result;
    }

    private static function __write($filename, $data)
    {
        $data = serialize($data);
        if (!file_exists($filename))
        {
            file_put_contents($filename, $data, FILE_APPEND);
        }
        else
        {
            file_put_contents($filename, $data);
        }
    }

    /**
     * Проверяет наличие файла и срок его жизни, если файла нет или он должен был погибнуть, то вернет false
     * @return type
     * @internal param $key
     * @internal param $life_time
     */
    private function __check()
    {
        $filename = self::__filename();

        if (self::$__current_time >= $this->__limit && file_exists($filename))
        {
            return true;
        }
        else if (!file_exists($filename))
        {
            return false;
        }
        else if (filemtime($filename) + $this->__life_time * $this->__multiplicator <= time())
        {
            return false;
        }

        return true;
    }

    private static function __read($filename)
    {
        $fp = fopen($filename, "a+");
        flock($fp, LOCK_EX); //блокировка файла
        $content = fread($fp, filesize($filename));
        flock($fp, LOCK_UN); //снятие блокировки
        fclose($fp);

        return unserialize($content);
    }

    /**
     *
     * @param $file только имя файла без пути!
     * @return type
     */
    public static function extends_file_by_name($file)
    {
        return file_exists(self::__dir() . $file);
    }

    /**
     *
     * @param $file только имя файла без пути!
     */
    public static function delete_file_by_name($file)
    {
        $path = self::__remove_double_sleshes(self::__dir() . cache::get_md5_path($file));

        if (file_exists($path) && is_file($path))
        {
            unlink($path);
        }
    }

    private static function __remove_double_sleshes($path)
    {
        return str_replace(['//', '\\\\'], DIRECTORY_SEPARATOR, $path);
    }

    /**
     *
     * @param $file только имя файла без пути!
     * @return mixed
     */
    public static function read_file_by_name($file)
    {
        $path = self::__remove_double_sleshes(self::__dir() . cache::get_md5_path($file));

        if (file_exists($path))
        {
            return unserialize(file_get_contents($path));
        }

        return null;
    }

    /**
     *
     * @param $limit = 0.05; // в секундах
     */
    public function set_limit($limit)
    {
        $this->__limit = $limit;
    }

    public function get_file()
    {
        if (!$this->__file)
        {
            $this->__filename();
        }

        return str_replace(HOME_PATH, '', $this->__file);
    }

    public function read()
    {
        $filename = self::__filename();

        if (file_exists($filename))
        {
            return self::__read($filename);
        }

        return null;
    }

    /**
     * Удаляет файл кеша, по ключу
     */
    public function delete_my_file()
    {
        $filename = $this->__filename();
        $dir      = self::__dir();
        $path     = $dir . $filename;

        if (file_exists($path))
        {
            unlink($path);
        }
    }

    /**
     * Перезаписывает файл кеша, можно подставить новый массив.
     *
     *
     * @param array|bool|object $data = false. Если false то не меняет дату, а перезаписывает файл, меняя его дату создания
     */
    public function rewrite($data = false)
    {
        $filename = $this->__filename();

        if ($data === false)
        {
            $data = $this->__read($filename);
            $this->__write($filename, $data);
        }
        else
        {
            $this->__write($filename, $data);
        }
    }

}
