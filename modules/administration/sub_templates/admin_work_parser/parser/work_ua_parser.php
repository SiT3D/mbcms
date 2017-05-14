<?php

use trud\site_metrics;

class work_ua_parser
{

    const URL_COMPANY     = 'https://www.work.ua/jobs/by-company/';
    const URL_VACANCY     = 'https://www.work.ua/jobs/';
    const RETTYPE_VACANCY = 2;
    const RETTYPE_COMPANY = 1;
    private static $__HOST = 'https://www.work.ua';
    private        $__url, $__document;
    private        $__comps_document;

    /**
     *
     * @param string $url страница с вакансией
     * @internal param string $count количество вакансий за раз
     */
    public function __construct($url)
    {
        $url .= '/';
        $url         = preg_replace_callback('~//$~', function ()
        {
            return '/';
        }, $url);
        $this->__url = $url;
    }

    /**
     * Получает id на work.ua из адресной строки переданной в параметре
     *
     * @param string $url
     * @return type
     */
    public static function get_out_id($url)
    {
        $match = [];
        preg_match('~([0-9]{1,10})~', $url, $match);

        return isset($match[1]) ? $match[1] : null;
    }

    public static function format_date_standart($date)
    {
        $year          = '';
        $month         = '';
        $day           = '';
        $standart_date = '01';

        $date_array = explode('.', $date);
        if (count($date_array) == 1)
        {
            $day   = $standart_date;
            $month = $standart_date;
            $year  = $date_array[0];
        }
        else if (count($date_array) == 2)
        {
            $day   = $standart_date;
            $month = $date_array[0];
            $year  = $date_array[1];
        }
        else if (count($date_array) == 3)
        {
            $day   = $date_array[0];
            $month = $date_array[1];
            $year  = $date_array[2];
        }

        return $year . '-' . $month . '-' . $day;
    }

    /**
     *
     * @param type $user_id
     * @return string
     */
    public static function get_current_url_site($user_id)
    {
        $import  = new import_links();
        $__check = $import->check_item(
            [
                import_links::DB_USER_ID      => $user_id,
                import_links::DB_DOMEN        => import_links::DOMEN_WORK_UA,
                import_links::DB_ELEMENT_TYPE => import_links::TYPE_COMPANY,
            ]
        );

        if ($__check)
        {
            $line   = $import->get_check_item();
            $out_id = isset($line->{import_links::DB_OUT_ID}) ? $line->{import_links::DB_OUT_ID} : null;

            if (!$out_id)
            {
                return '';
            }

            return self::$__HOST . '/jobs/by-company/' . $out_id . '/';
        }
    }

    /**
     *
     * @param array|type $companies
     */
    public static function write_in_db_in_page($companies = [])
    {
        $wxml = new work_ua_xml_vacancies();

        foreach ($companies as $company)
        {
            $wxml->add($company['name'], $company['count'], $company['url']);
        }
    }

    /**
     *
     * @param type $url
     * @param type $minimum
     * @return type
     */
    public static function get_vacansies_in_companies($url, $minimum)
    {
        $wp = new work_ua_parser($url);

        return $wp->__read_companies_in_page($minimum);
    }

    private function __read_companies_in_page($minimum)
    {
        $this->__minimum        = $minimum ? $minimum : 1;
        $html                   = file_get_contents($this->__url);
        $this->__comps_document = phpQuery::newDocument($html);
        response::add_response('next_page', $this->__get_next_page_in());

        return $this->__get_companies_in_page();
    }

    private function __get_next_page_in()
    {
        foreach ($this->__comps_document->find('nav ul.pagination li.active:first + li a') as $dom)
        {
            foreach ($dom->attributes as $attr)
            {
                if ($attr->name == 'href')
                {
                    return self::$__HOST . $attr->value;
                }
            }
        }
    }

    private function __get_companies_in_page()
    {
        $companies = [];

        foreach ($this->__comps_document->find('.card.card-hover') as $card)
        {
            $data  = [];
            $count = $this->__get_company_count_in_card($card);
            if ($this->__minimum <= $count)
            {
                $data['name']  = $this->__get_company_name_in_card($card);
                $data['url']   = self::$__HOST . $this->__get_company_url_in_card($card);
                $data['count'] = $this->__get_company_count_in_card($card);

                $companies[] = $data;
            }
        }


        return $companies;
    }

    private function __get_company_count_in_card($card)
    {
        foreach (pq($card)->find('.jobs-data a .h2') as $dom)
        {
            return $dom->nodeValue;
        }
    }

    private function __get_company_name_in_card($card)
    {
        foreach (pq($card)->find('h2 > a') as $dom)
        {
            return $dom->nodeValue;
        }
    }

    private function __get_company_url_in_card($card)
    {
        foreach (pq($card)->find('h2 > a') as $dom)
        {
            foreach ($dom->attributes as $attr)
            {
                if ($attr->name == 'href')
                {
                    return $attr->value;
                }
            }
        }
    }

    /**
     * Считывает данные с указанной страницы <br>
     *  // <b>Компания</b> <br>
     *  //#about-company <br>
     *  //<dd> - телефон и сайт <br>
     *  //[name="about-company"] > h3 - это заголовок статьи <br>
     *  //.row.row-print p - это описание компании <br>
     *  // h2 a - ссылки с вакансиями компании <br>
     *  // <b>Вакансия</b> <br>
     *  // .add-top .text-muted - дата добавления 14.07.2016 d.m.Y переформатировать есть ф-ия <br>
     *  // #h1-name - заголовок вакансии <br>
     *  // <dd> - вип, город, вид занятости, опыт работы <br>
     *  // .overflow.wordwrap - текст описание вакансии <br>
     *
     * @return array массив с информацией, компания - вакансия, 'type'=>RETTYPE_VACANCY|RETTYPE_COMPANY<br>
     *  <b>["vacancies"]</b> - массив с вакансиями
     */
    public function read()
    {

        if (!trim($this->__url))
        {
            return;
        }

        try
        {
            $html = $this->__html = file_get_contents($this->__url);
        }
        catch (Exception $e)
        {
            Module::add_response('errors[]', 'Страница 404, не найдена!');
        }

        if (!$html)
        {
            return false;
        }

        $this->__document = \phpQuery::newDocument($html);

        if ($this->__is_not_vacancy())
        {
            return false;
        }

        if ($this->__is_company_page())
        {
            $return = $this->__parse_company();

            $return['type'] = self::RETTYPE_COMPANY;

            return $return;
        }
        else
        {
            $return = $this->__parse_vacancy();

            $return['type'] = self::RETTYPE_VACANCY;

            return $return;
        }
    }

    private function __is_not_vacancy()
    {
        if (preg_match('~' . 'вакансия не найдена' . '~', $this->__html))
        {
            return true;
        }

        return false;
    }

    /**
     * /jobs/by-company - company or /jobs - vac
     *
     * @return type
     */
    private function __is_company_page()
    {

        return preg_match('~by\-company~', $this->__url);
    }

    /**
     * // <b>Компания</b> <br>
     *  //#about-company <br>
     *  //<dd> - телефон и сайт <br>
     *  //[name="about-company"] > h3 - это заголовок статьи <br>
     *  //.row.row-print p - это описание компании <br>
     *  // h2 a - ссылки с вакансиями компании <br>
     *
     * @return array поля для компании и "vacancies" - массив с вакансиями
     */
    private function __parse_company()
    {
        $result = [];

        $result['companyname']        = $this->__get_company_name();
        $contacts                     = $this->__get_contacts();
        $result['phone']              = $contacts['phone'];
        $result['website']            = $contacts['site'];
        $result['companydescription'] = $this->__get_content();
        $result['adddate']            = site_metrics::get_current_date();
        $result['companyapproved']    = 1;
        $result['companytype']        = 1;

        return [$result, [], []];
    }

    private function __get_company_name()
    {
        foreach ($this->__document->find('#about-company') as $dom)
        {
            return $company_name = $dom->nodeValue;
        }
    }

    private function __get_contacts()
    {
        $result = [];
        foreach ($this->__document->find('dd') as $dom)
        {
            $result[] = $dom->nodeValue;
        }

        return $this->__is($result);
    }

    /**
     * Функция определяет, что за данные получены в контактах. Телефон, образование и т.д.
     *
     * @param array $contacts массив с данными "контакты"
     * @return array
     */
    private function __is($contacts)
    {
        $return = [
            'phone'   => null,
            'chedule' => null,
            'exp'     => null,
            'edc'     => null,
            'site'    => null,
            'city'    => null,
        ];

        foreach ($contacts as $string)
        {
            if (preg_match('~[\d\s-()]{5,}~', $string))
            {
                $return['phone'] = $string;
            }

            if (preg_match('~занят|работа|студ~iu', $string))
            {
                $return['chedule'] = $this->__get_chedule_id($string);
            }

            if (preg_match('~опыт|опыт.*раб~iu', $string))
            {
                $mage = [];
                preg_match('~(\d{1,2})~', $string, $mage);
                $return['exp'] = isset($mage[1]) ? $mage[1] : 0;
            }

            if (preg_match('~образование|высшее|среднее~iu', $string))
            {
                $return['edc'] = self::get_education_type($string);
            }

            if (preg_match('~\w\.\w~iu', $string))
            {
                $return['site'] = $string;
            }


            if ($city = site_metrics::g__deep_city_finder($string))
            {
                $return['city'] = $city;
            }
        }


        return $return;
    }

    /**
     * Сопаставляет наши коды занятости с тем что есть у них<br>
     * полная занятость, неполная занятость, готовы взять студента, удаленная работа
     *
     * @param type $string
     * @return int
     */
    private function __get_chedule_id($string)
    {
        $cats = [];

        if (preg_match('~полная занятость~i', $string))
        {
            $cats[] = 1;
        }

        if (preg_match('~неполная занятость~i', $string))
        {
            $cats[] = 2;
        }

        if (preg_match('~удаленная работа~i', $string))
        {
            $cats[] = 3;
        }

        if (count($cats) == 0)
        {
            return 1;
        }

        return implode(',', $cats);
    }

    /**
     *
     * @param type $string
     * @return int
     */
    public static function get_education_type($string)
    {
        if (preg_match('~' . 'незак.*выс' . '~usi', $string))
        {
            return 2;
        }
        else if (preg_match('~' . 'науч|высшее' . '~usi', $string))
        {
            return 1;
        }
        else if (preg_match('~' . 'среднее.*спец' . '~usi', $string))
        {
            return 3;
        }


        return 4;
    }

    private function __get_content()
    {
        $text = '';

        foreach ($this->__document->find('[name=about-company] + h3') as $dom)
        {
            $text .= $dom->textContent;
        }

        foreach (pq($this->__document->find('[name=about-company]'))->nextAll('*') as $dom)
        {
            if ($dom->tagName == 'p' || $dom->tagName == 'ul')
            {
                $text .= "<{$dom->tagName}>" . strip_tags(pq($dom)->html(), '<ul><li><p><b><strong>') . "</{$dom->tagName}>";
            }
        }

        return $text;
    }

    /**
     * // .add-top .text-muted - дата добавления 14.07.2016 d.m.Y переформатировать есть ф-ия <br>
     *  // #h1-name - заголовок вакансии <br>
     *  // <dd> - вип, город, вид занятости, опыт работы <br>
     *  // .overflow.wordwrap - текст описание вакансии <br>
     *
     * @return array
     */
    private function __parse_vacancy()
    {
        $result = [];

        $result['title']              = $this->__vac_get_title();
        $result['vacancydescription'] = $this->__vac_get_content();
        $contacts                     = $this->__vac_get_contacts();
        $result['workexperience']     = $contacts['exp'];
        $result['educationlevel']     = $contacts['edc'];
        $result['visible']            = 1;
        $result['ifpublish']          = 1;
        $result['adddate']            = site_metrics::get_current_date();
        $result['added']              = site_metrics::get_current_date(site_metrics::FORMAT_DATE_TIME);
        $result['worktype']           = $contacts['chedule'];
        $result['salary']             = $this->__vac_get_salary();


        return [$result, $this->__get_work_category(), $contacts['city']];
    }

    private function __vac_get_title()
    {
        foreach ($this->__document->find('#h1-name') as $dom)
        {
            return $dom->nodeValue;
        }
    }

    private function __vac_get_content()
    {
        $text = '';


        foreach ($this->__document->find('.overflow.wordwrap *') as $dom)
        {
            if ($dom->tagName == 'p' || $dom->tagName == 'ul')
            {
                $text .= "<{$dom->tagName}>" . strip_tags(pq($dom)->html(), '<ul><li><p><b><strong>') . "</{$dom->tagName}>";
            }
        }

        return $text;
    }

    /**
     * Получает контактные данные, данные об образовании, телефоны, тип работы, опыт
     * в общем требования и контакты
     *
     * @return type
     */
    private function __vac_get_contacts()
    {
        $return = [];

        foreach ($this->__document->find('dd') as $dom)
        {
            $return[] = $dom->nodeValue;
        }

        return $this->__is($return);
    }

    private function __vac_get_salary()
    {
        foreach ($this->__document->find('.card *') as $dom)
        {
            $match = [];
            preg_match('~(\d*.{0,6}грн)~iu', $dom->textContent, $match);

            if (isset($match[1]))
            {
                return $match[1];
            }
        }

        return '';
    }

    private function __get_work_category()
    {
        $categories = [];

        $text = 'Вакансии в категориях';
        foreach ($this->__document->find('.col-md-6 h5:contains("' . $text . '") + ul li a') as $dom)
        {
            $categories[] = $this->__format_categorie($dom->nodeValue);
        }

        $text = 'Вакансии в категории';
        foreach ($this->__document->find('.col-md-6 h5:contains("' . $text . '") + ul li a') as $dom)
        {
            $categories[] = $this->__format_categorie($dom->nodeValue);
        }

        return $categories;
    }

    /**
     * Приводит к формату нашего сайта их категории
     *
     * <option value="1">IT, компьютеры, интернет</option>                  <br>
     * <option value="2">Администрация, руководство среднего звена</option> <br>
     * <option value="3">Бухгалтерия, аудит</option>                       <br>
     * <option value="4">Гостинично-ресторанный бизнес, туризм</option>     <br>
     * <option value="5">Дизайн, творчество</option>                        <br>
     * <option value="6">Красота, фитнес, спорт</option>                    <br>
     * <option value="7">Культура, музыка, шоу-бизнес</option>              <br>
     * <option value="8">Логистика, склад, ВЭД</option>                    <br>
     * <option value="9">Маркетинг, реклама, PR</option>                    <br>
     * <option value="10">Медицина, фармацевтика</option>                  <br>
     * <option value="11">Недвижимость</option>                            <br>
     * <option value="12">Образование, наука</option>                      <br>
     * <option value="13">Охрана, безопасность</option>                     <br>
     * <option value="22">Продажи, закупки</option>                        <br>
     * <option value="14">Рабочие специальности, производство</option>      <br>
     * <option value="23">Розничная торговля</option>                       <br>
     * <option value="15">Секретариат, делопроизводство, АХО</option>       <br>
     * <option value="30">Сельское хозяйство, агробизнес</option>           <br>
     * <option value="17">СМИ, издательство, полиграфия</option>            <br>
     * <option value="18">Страхование</option>                              <br>
     * <option value="19">Строительство, архитектура</option>               <br>
     * <option value="20">Сфера обслуживания</option>                       <br>
     * <option value="6792">Телекоммуникации и связь</option>               <br>
     * <option value="21">Топ-менеджмент, руководство высшего звена</option><br>
     * <option value="24">Транспорт, автобизнес</option>                    <br>
     * <option value="25">Управление персоналом, HR</option>                <br>
     * <option value="26">Финансы, банк</option>                           <br>
     * <option value="27">Юриспруденция</option>                            <br>
     * <option value="32">Другие сферы деятельности</option>               <br>
     * @param $category
     * @return mixed|string
     */
    private function __format_categorie($category)
    {
        $sort = [
            'IT, компьютеры, интернет'                  => '392', // IT
            'Строительство, архитектура'                => '412', // строительство
            'Транспорт, автобизнес'                     => '416', // Транспорт, водители
            'Логистика, склад, ВЭД'                     => '399', // Логистика, склад, таможня
            'Медицина, фармацевтика'                    => '401', // Медицина, фармацевтика, здравоохранение
            'Охрана, безопасность'                      => '404', // Охрана, безопасность
            'Продажи, закупки'                          => '405', // Торговля
            'Гостинично-ресторанный бизнес, туризм'     => '395', // Гостиночно-ресторанный бизнес
            'Розничная торговля'                        => '428', // Розничная торговля
            'Рабочие специальности, производство'       => '406', // Рабочие специальности
            'Образование, наука'                        => '403', // Сфера образования
            'Маркетинг, реклама, PR'                    => '400', // Реклама, маркетинг
            'Управление персоналом, HR'                 => '417', // Секретари, офис-менеджеры
            'Финансы, банк'                             => '418', // Банковская и финансовая сфера
            'Сфера обслуживания'                        => '413', // Сфера услуг
            'Администрация, руководство среднего звена' => '423',
            'Бухгалтерия, аудит'                        => '394',
            'Дизайн, творчество'                        => '396',
            'Красота, фитнес, спорт'                    => '397',
            'Недвижимость'                              => '425',
            'Культура, музыка, шоу-бизнес'              => '398',
            'Другие сферы деятельности'                 => '420',
            'Сельское хозяйство, агробизнес'            => '409',
            'СМИ, издательство, полиграфия'             => '410',
            'Телекоммуникации и связь'                  => '414',
            'Страхование'                               => '411',
            'Юриспруденция'                             => '419',
            'Топ-менеджмент, руководство высшего звена' => '393',
            'Секретариат, делопроизводство, АХО'        => '408',
        ];

        return isset($sort[$category]) ? $sort[$category] : '420';
    }

    /**
     * Получает все ссылки на вакансии, из ссылки на фирму
     *
     * @return type
     */
    public function get_all_vacancy_links()
    {
        if ($this->__url)
        {
            $html             = file_get_contents($this->__url);
            $this->__document = \phpQuery::newDocument($html);

            return $this->__get_all_vacancy_links();
        }
    }

    private function __get_all_vacancy_links()
    {
        $links = [];


        foreach ($this->__document->find('h2 a') as $dom)
        {
            foreach ($dom->attributes as $attr)
            {
                if ($attr->name == 'href')
                {
                    $links[] = self::$__HOST . $attr->value;
                }
            }
        }

        return $links;
    }

}
