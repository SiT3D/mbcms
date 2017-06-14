<?php

namespace MBCMS;

class DB
{

    const M_USE_VALUE_BY_KEY = 'M_USE_VALUE_BY_KEY';
    protected static $conf;
    private static $TIMER   = 0;
    private static $QUERIES = 0;
    protected        $__mysqli      = null;
    protected        $__sql         = '';
    protected        $__last_sql    = '';
    protected        $__params      = [];
    protected        $__smtm        = null;
    protected        $__is_array    = false;
    protected        $__is_mono     = false;
    protected        $__limit       = null;
    protected        $__offset      = null;
    protected        $__select      = [];
    protected        $__update      = [];
    protected        $__joins       = [];
    protected        $__ljoins      = [];
    protected        $__whereSys    = [];
    protected        $__whereSys_wc = false;
    protected        $__order       = '';
    protected        $__group       = '';

    public function __construct($sql, $params_array = [])
    {
        self::set_config();
        $this->__sql    = $sql;
        $params_array   = is_array($params_array) ? $params_array : [$params_array];
        $this->__params = $params_array;
    }

    public static function set_config()
    {
        self::$conf = configuration::factory()->get_db_config();
    }

    /**
     * @param $table_name
     * @return DB
     */
    public static function d($table_name)
    {
        return new DB("DELETE FROM $table_name");
    }

    /**
     *
     * @param $table
     * @param $regularOn
     * @param array $values
     * @return \MBCMS\DB
     */
    public function lj($table, $regularOn, $values = [])
    {
        $this->__ljoins[] = [
            't' => $table,
            'r' => $regularOn,
            'v' => $values,
        ];

        return $this;
    }

    /**
     *
     * @param string $table
     * @param string $regularOn example user.id = vacancy.userid (без ON )
     * @param array $values
     * @return \MBCMS\DB
     */
    public function j($table, $regularOn, $values = [])
    {
        $this->__joins[] = [
            't' => $table,
            'r' => $regularOn,
            'v' => $values,
        ];

        return $this;
    }

    /**
     * Разновидность where тот же массив но другие параметры, и тип + другой мердж через ->m
     */
    public function in()
    {

    }

    /**
     * Использовать в сочетании с w() для взятия выражения в скобки. Пример id = 1 AND(a=1 OR a=2)
     *
     * ->w('id = 1')
     * ->wc('L', 'AND')
     * ->w('a=1')
     * ->w('a=2', 'OR')
     * ->wc('R', '')
     *
     * @param $pos = L or R
     * @param string $operator AND|OR
     * @return \MBCMS\DB
     */
    public function wc($pos, $operator)
    {
        if (strtoupper($pos) === 'L')
        {
            $this->__whereSys[]  = [$operator, '(', []];
            $this->__whereSys_wc = true;
        }
        else if (strtoupper($pos) === 'R')
        {
            $this->__whereSys[]  = [')', $operator, []];
            $this->__whereSys_wc = false;

            $prev = isset($this->__whereSys[count($this->__whereSys) - 2]) ? $this->__whereSys[count($this->__whereSys) - 2] : null;
            if (isset($prev[1]) && $prev[1] === '(')
            {
                unset($this->__whereSys[count($this->__whereSys) - 2]);
                unset($this->__whereSys[count($this->__whereSys)]);
            }
        }

        return $this;
    }

    /**
     *
     * @param $sql NO WORDS GROUP BY
     * @return \MBCMS\DB
     */
    public function g($sql)
    {
        $this->__group = $sql;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function is_array($value = true)
    {
        $this->__is_array = $value;

        return $this;
    }

    /**
     * Можно получить после сборки запроса ->get() или ->get_result_sql
     *
     * @return array
     */
    public function get_params_array()
    {
        return $this->__params;
    }

    public function count($is_mono = true)
    {
        $count_query = clone $this;
        $count_query->s(['count(*) as count'])->is_mono($is_mono)->limit(null)->offset(null)->o('');
        $qc = $count_query->get();

        return isset($qc->count) ? $qc->count : (is_array($qc) ? count($qc) : 0);
    }

    /**
     *
     * @param $sql NO WORDS ORDER BY
     * @return \MBCMS\DB
     */
    public function o($sql)
    {
        $this->__order = $sql;

        return $this;
    }

    /**
     *
     * @param int $value ->offset(),->limit()
     * @return $this
     */
    public function offset($value)
    {
        $this->__offset = $value;

        return $this;
    }

    /**
     *
     * @param int $value ->offset(),->limit()
     * @return $this
     */
    public function limit($value)
    {
        $this->__limit = $value;

        return $this;
    }

    /**
     * Если результатом запроса является массив с 1 записью, то вернет не массив а только эту запись.
     * Но даже если указан этот параметр, но массив вернется с несколькими записями, то данное действие будет проигнорировано.
     * Для верного результата можно использовать в сочетвнии с limit(1)
     * @param bool $value
     * @return $this or null
     */
    public function is_mono($value = true)
    {
        $this->__is_mono = $value;

        return $this;
    }

    /**
     *
     * // Добавить метод, добавляющий в селект group_concat(t_images_tags.value separator ',') as mytags
     *
     * @param array $columns индексный массив строк
     * @param string $table_name
     * @param bool $is_merge_columns = false
     * @return \MBCMS\DB
     */
    public function s(array $columns, $table_name = null, $is_merge_columns = false)
    {
        $this->__select[0] = isset($this->__select[0]) && $this->__select[0] ? $this->__select[0] : $table_name;
        $this->__select[1] = $is_merge_columns ? array_merge($columns, (isset($this->__select[1]) ? $this->__select[1] : [])) : $columns;

        return $this;
    }

    /**
     * @return object|null|array
     */
    public function get()
    {

        $this->__merged();
        $result = $this->__query();
        $this->__clear();

        return $result;
    }

    /**
     * @return mixed
     */
    private function __query()
    {
        $ttttttttttt = microtime_float();


        $this->__mysqli();

        if ($this->__smtm = $this->__mysqli->prepare($this->__sql))
        {

            $this->__last_sql = $this->__sql;

            $params = [];
            $idents = null;

            if ($idents = $this->__get_idents())
            {
                $params = [$idents];
            }

            $params = array_merge($params, $this->__params);

            if ($params && $idents)
            {
                $refs = [];
                foreach ($params as $key => $value)
                {
                    if ($value === null || is_array($value) || is_object($value))
                    {
                        $params[$key] = '';
                    }

                    $refs[] = &$params[$key];
                }

                call_user_func_array([$this->__smtm, 'bind_param'], $refs);
            }

            $this->__smtm->execute();

            $results = $this->__get_result();

            if (isset($this->__mysqli->insert_id) && $this->__mysqli->insert_id)
            {
                $results = $this->__mysqli->insert_id;
            }

            $this->__smtm->close();
            $this->__mysqli->close();

            unset($this->__smtm);
            unset($this->__mysqli);

            self::$TIMER += microtime_float($ttttttttttt, 'time', false);
            self::$QUERIES .= $this->get_result_sql() . "\n\n\n";

            return $results;
        }
        else
        {
            $er = $this->__mysqli->error ? $this->__mysqli->error : 'NO CONNECT. See Database config';

            if (configuration::factory()->is_static_templates() !== true)
            {
                /* MDS */
                echo '<pre class="btn-inverse">';
                echo 'SQL error: ' . $er;
                echo '<br/>';
                echo $this->__sql;
                echo '<br/>';
                echo '======================================================================== ';
                echo '</pre>'; /* MDS */

            }
        }

        return null;
    }

    /**
     * @return array times and queries
     */
    public static function get_info()
    {
        return [self::$TIMER, self::$QUERIES];
    }

    private function __mysqli()
    {
        $this->__mysqli = new \mysqli(self::$conf['host'], self::$conf['username'], self::$conf['password'], self::$conf['database']);
    }

    private function __get_idents()
    {
        $idents = '';

        foreach ($this->__params as $param)
        {

            if (is_string($param))
            {
                $idents .= 's';
            }
            else if (is_int($param))
            {

                $idents .= 'i';
            }
            else if (is_float($param))
            {

                $idents .= 'd';
            }
            else
            {
                $idents .= 's';
            }
        }

        return $idents;
    }

    private function __get_result()
    {
        $meta       = $this->__smtm->result_metadata();
        $results    = [];
        $parameters = [];

        if (method_exists($meta, 'fetch_field'))
        {
            while ($field = $meta->fetch_field())
            {
                $parameters[] = &$row[$field->name];
            }
        }

        if ($parameters && count($parameters))
        {
            call_user_func_array([$this->__smtm, 'bind_result'], $parameters);
        }

        while ($this->__smtm->fetch())
        {
            $x = [];

            foreach ($row as $key => $val)
            {
                $x[$key] = $val;
            }

            if (!$this->__is_array)
            {
                $x = (object)$x;
            }

            $results[] = $x;
        }

        return $this->__mono($results);
    }

    private function __mono($results)
    {
        if ($this->__is_mono && count($results) == 1)
        {
            return array_shift($results);
        }
        else if (count($results) > 1 && $this->__is_mono)
        {
            return $results;
        }
        else if ($this->__is_mono && !count($results))
        {
            return null;
        }

        return $results;
    }

    /**
     * @return string
     */
    public function get_result_sql()
    {
        if ($this->__sql)
        {
            return $this->__sql;
        }
        $this->__clear();
        $this->__merged();

        return $this->__sql;
    }

    private function __clear()
    {
        $this->__sql    = '';
        $this->__params = [];
    }

    private function __merged()
    {
        $this->__merge_select();
        $this->__merge_update_step1();
        $this->__merge_joins();
        $this->__merge_update_step2();
        $this->__merge_where();
        $this->__merge_group();
        $this->__merge_order();
        $this->__merge_limit();
    }

    private function __merge_select()
    {
        if (!count($this->__select))
        {
            return;
        }

        list($table_name, $columns) = $this->__select;
        $columns = implode(",\n", $columns);
        $this->r("SELECT $columns FROM $table_name");
    }

    /**
     * Добавляет запись к запросу
     *
     * @param $sql
     * @param array|type $params_array
     * @return $this
     */
    public function r($sql, $params_array = [])
    {
        $this->__merge($sql, $params_array);

        return $this;
    }

    /**
     * @param $sql
     * @param array $params_array
     */
    private function __merge($sql, $params_array = [])
    {
        $params_array = is_array($params_array) ? $params_array : [$params_array];

        $this->__sql .= "\n" . $sql;
        $this->__params = array_merge($this->__params, $params_array);
    }

    private function __merge_update_step1()
    {
        if (!count($this->__update))
        {
            return;
        }

        list($table_name,) = $this->__update;
        $this->r("UPDATE $table_name");
    }

    private function __merge_joins()
    {
        foreach ($this->__joins as $j)
        {
            $this->r("JOIN {$j['t']} ON {$j['r']}", $j['v']);
        }

        foreach ($this->__ljoins as $j)
        {
            $this->r("LEFT JOIN {$j['t']} ON {$j['r']}", $j['v']);
        }
    }

    private function __merge_update_step2()
    {
        if (!count($this->__update))
        {
            return;
        }

        list(, $set_array) = $this->__update;
        $this->m('SET', ',', $set_array, true);
    }

    /**
     *
     *  => true                 = key=? DELIMITR key=?  <----- по умолчанию <br>
     *  => false                = (?,?,?)<br>
     *  => M_USE_VALUE_BY_KEY   = (key,key,key,key)<br>
     *
     * Позволяет формировать сложные запросы с перечислением через разделитель.
     * Если $use_indexis = false то всегда будет генерироваться структура типа $first_word (?<DELIMITR>?<DELIMITR>?<DELIMITR>?<DELIMITR>?)
     * Примеры:<br>
     *
     * WHERE a=? AND b=? AND c=?, array_values <br>
     * VALUES (?,?,?,?), array_values <br>
     * IN (?,?,?,?), array_values <br>
     * и так далее<br>
     *
     * @param string $first_word пример WHERE | SET | VALUES
     * @param string $delimitr пример AND | OR | , etc
     * @param array $values массив значений для подстановки
     * @param bool $use_indexis
     * @return DB
     * @internal param bool|M_USE_VALUE_BY_KEY $mixed $use_indexis = true использовать индексы значений в виде ключей $key = ? , $key = , [$val1, $val2]
     * если $use_indexis = M_USE_VALUE_BY_KEY тогда формируется строка вида (<VALUE1><DELIMITR><VALUE2>) без знака вопроса и передачи параметров!
     *
     *  foreach ($ids as $id)
     * {
     * $values['static_prof_id'][] = $id;
     * }
     *
     * $values['test'] = 'none';
     *
     * ->m('WHERE', 'OR', $values)
     * result
     *
     * WHERE static_prof_id = ?
     * OR static_prof_id = ?
     * OR static_prof_id = ?
     * OR static_prof_id = ?
     * OR static_prof_id = ?
     * OR static_prof_id = ?
     * OR static_prof_id = ?
     * OR static_prof_id = ?
     * OR static_prof_id = ?
     * OR static_prof_id = ?
     * OR test = ?"
     *
     * => true                 = key=? DELIMITR key=?<br>
     *  => false                = (?,?,?)<br>
     *  => M_USE_VALUE_BY_KEY   = (key,key,key,key)<br>
     *
     */
    public function m($first_word, $delimitr, $values, $use_indexis = true)
    {
        $this->__mylti_questions($first_word, $delimitr, $values, $use_indexis);

        return $this;
    }

    /**
     *
     * @param $first_word
     * @param $delimitr
     * @param $values
     * @param $use_indexis
     */
    private function __mylti_questions($first_word, $delimitr, $values, $use_indexis)
    {
        $count = count($values) - 1;
        $count = $count <= 0 ? 0 : $count;

        if ($use_indexis === self::M_USE_VALUE_BY_KEY)
        {
            $questions = implode($delimitr, $values);
            $questions = str_replace('!', '', $questions);
            $this->r("$first_word ($questions)");
        }
        else if ($use_indexis)
        {
            foreach ($values as $key => $value)
            {
                $this->__multy_q_values($first_word, $delimitr, $key, $value);
                break;
            }

            array_shift($values);

            foreach ($values as $key => $value)
            {
                $this->r("$delimitr $key = ?", $value);
            }
        }
        else
        {
            $questions = str_repeat("$delimitr?", $count);
            $this->r("$first_word (?$questions)", $values);
        }
    }

    /**
     *
     * @param $first_word
     * @param $delimitr
     * @param $key
     * @param $value
     * @return bool
     */
    private function __multy_q_values($first_word, $delimitr, $key, $value)
    {
        if (!is_array($value))
        {
            $this->r("$first_word $key = ?", $value);
        }
        else if (is_array($value))
        {
            $this->r("$first_word $key = ?", $value[0]);
            array_shift($value);
            foreach ($value as $val)
            {
                $this->r("$delimitr $key = ?", $val);
            }

            return true;
        }

        return null;
    }

    private function __merge_where()
    {
        if (count($this->__whereSys))
        {
            $this->r('WHERE');

            $first = true;

            foreach ($this->__whereSys as $w)
            {
                list($operator, $regular, $values) = $w;
                $operator = $first ? '' : $operator;
                $this->r("$operator $regular", $values);
                $first = false;
            }
        }
    }

    private function __merge_group()
    {
        if ($this->__group)
        {
            $this->r("GROUP BY " . $this->__group);
        }
    }

    private function __merge_order()
    {
        if ($this->__order)
        {
            $this->r("ORDER BY " . $this->__order);
        }
    }

    private function __merge_limit()
    {
        if ($this->__limit !== null && $this->__offset !== null)
        {
            $this->r("LIMIT ?,?", [(int)$this->__offset, (int)$this->__limit]);
        }
        else if ($this->__limit !== null)
        {
            $this->r("LIMIT ?", [(int)$this->__limit]);
        }
    }

    /**
     * DB::q()->w('uname = ?', 'lol2')->save(['uname' => 'admin2'], 't_admins');
     * Нельзя сохранять null! поля со значением нулл будут удалены! И просто не будут перезаписаны;
     *
     * DB::q()->save([], 't_all_cities'); - add new
     *
     * @param $data [table_key => value]
     * @param $table_name
     * @return array|null|object
     */
    public function save($data, $table_name)
    {

        if (!count($this->__whereSys))
        {
            $have = null;
        }
        else
        {
            $have = DB::q()
                ->merge($this)
                ->s(['*'], $table_name)
                ->get();
        }

        if (is_object($have) || (is_array($have) && count($have)))
        {
            $this->u($data, $table_name)->get();
        }
        else
        {
            $this->__whereSys = [];

            return self::i($table_name, $data)->get();
        }
    }

    /**
     *
     * @param \MBCMS\DB $query
     */
    public function merge(DB $query)
    {
        self::__merge_queries($this, $query);

        return $this;
    }

    private static function __merge_queries(DB &$query1, DB $query2)
    {
        $query1->__update   = array_merge($query1->__update, $query2->__update);
        $query1->__joins    = array_merge($query1->__joins, $query2->__joins);
        $query1->__ljoins   = array_merge($query1->__ljoins, $query2->__ljoins);
        $query1->__whereSys = array_merge($query1->__whereSys, $query2->__whereSys);
        $query1->__params   = array_merge($query1->__params, $query2->__params);
        $query1->__group    = $query2->__group;
        $query1->__order    = $query2->__order;
        $query1->__limit    = $query2->__limit;
        $query1->__offset   = $query2->__offset;
    }

    /**
     * @param string $sql
     * @param array $params_array
     * @return DB
     */
    public static function q($sql = '', $params_array = [])
    {
        return new DB($sql, $params_array);
    }

    /**
     *
     * @param array $set_array ['column_name' => value]
     * @param $table_name
     * @param bool|type $is_merge_columns
     * @return DB
     */
    public function u(array $set_array, $table_name = null, $is_merge_columns = false)
    {
        $this->__update[0] = isset($this->__update[0]) ? $this->__update[0] : $table_name;
        $this->__update[1] = $is_merge_columns ? array_merge($set_array, (isset($this->__update[1]) ? $this->__update[1] : [])) : $set_array;

        return $this;
    }

    /**
     * Вставка записи в таблицу, формирует запрос, чтобы его выполнить нужно сделать ->get()
     *
     * Кстати! добавить такое!
     * INSERT INTO `trudOe`.`t_images_tags` (`id`, `name`, `value`, `image_id`) VALUES (NULL, 'tag', 'tree2', '7'), (NULL, 'tag', 'tree', '7');
     *
     * @param string $table_name
     * @param array $params_array ['key' => value, 'key' => value]
     * @return DB
     */
    public static function i($table_name, array $params_array)
    {
        $db   = new DB("INSERT INTO $table_name");
        $keys = array_keys($params_array);
        $db->m('', ',', $keys, DB::M_USE_VALUE_BY_KEY)
            ->m('VALUES', ',', $params_array, false);

        $db->is_mono();

        return $db;
    }

    /**
     *
     * @param string $regular
     * @param array $values
     * @param string $operator
     * @return \MBCMS\DB
     */
    public function w($regular, $values = [], $operator = 'AND')
    {
        $operator            = $this->__whereSys_wc ? '' : $operator;
        $this->__whereSys_wc = false;
        $this->__whereSys[]  = [$operator, $regular, $values];

        return $this;
    }
}
