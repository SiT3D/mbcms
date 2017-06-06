<?php

/**
 * Class GetPost
 */
class GetPost
{

    /**
     * получение без преобразования html
     *
     * @param string $s
     * @param mixed $standartValue
     * @return mixed
     */
    static function get($s, $standartValue = null)
    {
        $ret = isset($_POST[$s]) ? $_POST[$s] : null;

        if ($ret === null)
        {
            $ret =  isset($_GET[$s]) ? $_GET[$s] : null;
        }

        return $ret ? $ret : $standartValue;
    }

    /**
     *
     * @param array $keys ['key','key', 'key' => standart_value]
     * @param bool $is_indexis_array = false  вернет индексный массив, значений
     * @param array $get_array - массив ключей которые пойдут чистыми с тегами и php скриптами
     * @return array - ассоциативный массив значений
     */
    static function ar($keys, $is_indexis_array = false, $get_array = [])
    {
        $ret = [];

        foreach ($keys as $index => $key)
        {

            $standart_value = null;

            if (is_string($index))
            {
                $standart_value = $key;
                $key = $index;
            }

            if (!$is_indexis_array)
            {
                if (in_array($key, $get_array))
                {
                    $ret[$key] = self::get($key, $standart_value);
                }
                else
                {
                    $ret[$key] = self::uget($key, $standart_value);
                }
            }
            else
            {
                if (in_array($key, $get_array))
                {
                    $ret[] = self::get($key, $standart_value);
                }
                else
                {
                    $ret[] = self::uget($key, $standart_value);
                }
            }
        }

        return $ret;
    }

    /**
     * Безопасное получение без инъекций
     *
     * @param string $s
     * @param mixed $standartValue
     * @return mixed
     */
    static function uget($s, $standartValue = null)
    {
        $ret =  isset($_POST[$s]) ? $_POST[$s] : null;
        if ($ret == null)
        {
            $ret = isset($_GET[$s]) ? $_GET[$s] : null;
        }

        if ($ret != null && !is_array($ret) && !is_object($ret))
        {
            $ret = htmlentities($ret);
        }
        else if (!is_array($ret) && !is_object($ret) && !is_array($standartValue) && !is_object($standartValue))
        {
            $ret = htmlentities($standartValue);
        }

        return $ret ? $ret : $standartValue;
    }

    static function isset_key($s)
    {
        if (isset($_GET[$s]))
        {
            return true;
        }

        if (isset($_POST[$s]))
        {
            return true;
        }

        return false;
    }

}
