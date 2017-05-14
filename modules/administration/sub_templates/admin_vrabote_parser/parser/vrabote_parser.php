<?php

use trud\classes\auth;
use trud\classes\model\companies;
use trud\classes\model\import_site_links;
use trud\classes\model\user;
use trud\classes\model\vacancies;
use trud\site_metrics;

class vrabote_parser
{

    private static $__start_url = 'http://vrabote.ua';
    private        $__document;

    /**
     * @return array [$links, $alias]
     */
    public function get_all_categories()
    {
        $html             = file_get_contents(self::$__start_url . '/' . 'вакансии');
        $this->__document = phpQuery::newDocument($html);

        $links = [];
        $alias = [];

        foreach ($this->__document->find('.job-catalog:first a') as $dom)
        {
            $links[] = self::$__start_url . $this->__get_attr($dom, 'href');
            $alias[] = trim($dom->nodeValue);
        }

        return [$links, $alias];
    }

    private function __get_attr($dom, $name)
    {
        foreach ($dom->attributes as $attr)
        {
            if ($attr->name == $name)
            {
                return $attr->value;
            }
        }
    }

    /**
     * собирает вакансии с указанной страницы
     *
     * @param type $url
     * @return array [$links, $next_page]
     */
    public function get_page_vacancies($url)
    {
        $html             = file_get_contents($url);
        $this->__document = phpQuery::newDocument($html);

        $links = [];

        foreach ($this->__document->find('.vacancy') as $dom)
        {
            foreach (pq($dom)->find('a:first') as $link)
            {
                $uri = $this->__get_attr($link, 'href');

                if (!preg_match('~login~', $uri))
                {
                    $links[] = self::$__start_url . $uri;
                }
            }
        }


        return [$links, $this->__get_next_page()];
    }

    private function __get_next_page()
    {
        foreach ($this->__document->find('.pagination li.active + li a') as $dom)
        {
            return self::$__start_url . $this->__get_attr($dom, 'href');
        }
    }

    public function parse_vacancy($url, $category)
    {
        $html             = file_get_contents($url);
        $this->__document = phpQuery::newDocument($html);

        $vacancy_data = [];

        $vacancy_data['title']              = $this->__vac_get_title();
        $category_id                        = $this->__format_categorie($category);
        $vacancy_data['vacancydescription'] = $this->__vac_get_content();
        $contacts                           = $this->__vac_get_contacts();
        $city_id                            = $contacts['city'];
        $vacancy_data['workexperience']     = $contacts['exp'];
        $vacancy_data['educationlevel']     = $contacts['edc'];
        $vacancy_data['worktype']           = $contacts['chedule'];
        $vacancy_data['salary']             = $contacts['salary'];
        $vacancy_data['adddate']            = site_metrics::get_current_date();
        $vacancy_data['added']              = site_metrics::get_current_date(site_metrics::FORMAT_DATE_TIME);
        $vacancy_data['visible']            = 1;
        $vacancy_data['ifpublish']          = 1;

        $company_data['companyname']     = $this->__get_company_name();
        $company_data['phone']           = $contacts['phone'];
        $company_data['website']         = $contacts['site'];
        $company_data['adddate']         = site_metrics::get_current_date();
        $company_data['companyapproved'] = 1;
        $company_data['companytype']     = 1;

        $info['accesslevel'] = '2';
        $info['fullname']    = $contacts['contactp'];
        $info['phone']       = $contacts['phone'];

        $email = $contacts['email'];


        $user_id = isset(user::factory()->get_user_by_email($email)->get()->id) ? user::factory()->get_user_by_email($email)->get()->id : null;

        if (!$user_id)
        {
            $udat    = ['approved' => 1, 'regdate' => site_metrics::get_current_date()];
            $user_id = user::factory()->add_user($email, (new auth())->get_pass(rand(-10000000, 100000000) + 50000), $udat, $info, 0, user::ACCTYPE_EMPLOYER);
        }

        if ($user_id)
        {
            $company_id = null;

            $company_check = (new import_site_links())->check(import_site_links::TYPE_COMPANY,
                import_site_links::DOMEN_VRABOTE_UA, $this->__get_company_id(), $user_id)->get();


            if (!$company_check)
            {
                $company_id = companies::factory()->add_company($user_id, $company_data);

                (new import_site_links())->insert_link($this->__get_company_id(), $company_id,
                    import_site_links::TYPE_COMPANY, import_site_links::DOMEN_VRABOTE_UA, $user_id);
            }
            else
            {
                $company_id = $company_check->our_id;
            }


            $out_vacancy_id = str_replace('http://vrabote.ua/job/', '', $url);
            $vacancy_check  = (new import_site_links)->check(import_site_links::TYPE_VACANCY, import_site_links::DOMEN_VRABOTE_UA, $out_vacancy_id, $user_id)->get();

            if ($vacancy_check)
            {
                vacancies::factory()->update($vacancy_data, $vacancy_check->our_id, $company_id, $vacancy_data['title'], [$category_id], $city_id);
            }
            else
            {
                $vacancy_id = vacancies::factory()->add($vacancy_data, $user_id, $company_id, $vacancy_data['title'], [$category_id], $city_id);

                (new import_site_links())->insert_link($out_vacancy_id, $vacancy_id,
                    import_site_links::TYPE_VACANCY, import_site_links::DOMEN_VRABOTE_UA, $user_id);
            }

        }
    }

    private function __vac_get_title()
    {
        foreach ($this->__document->find('.main-info h1') as $dom)
        {
            return trim($dom->nodeValue);
        }
    }


    private function __format_categorie($category)
    {
        $category = trim($category);


        \Module::add_response('category_name', $category);

        $sort = [
            'IT-КОМПЬЮТЕРЫ, ИНТЕРНЕТ'                     => '392',
            'СТРОИТЕЛЬСТВО, АРХИТЕКТУРА'                  => '412',
            'ТРАНСПОРТ, СЕРВИСНОЕ ОБСЛУЖИВАНИЕ, ВОДИТЕЛИ' => '416',
            'ЛОГИСТИКА, СКЛАД, ВЭД'                       => '399',
            'МЕДИЦИНА, ФАРМАЦЕВТИКА'                      => '401',
            'ОХРАНА, БЕЗОПАСНОСТЬ'                        => '404',
            'ПРОДАЖИ, ЗАКУПКА'                            => '405',
            'ОТЕЛЬНО-РЕСТОРАННЫЙ БИЗНЕС, КУЛИНАРИЯ'       => '395',
            'РОЗНИЧНАЯ ТОРГОВЛЯ'                          => '428',
            'РАБОЧИЕ СПЕЦИАЛЬНОСТИ, ПРОИЗВОДСТВО'         => '406',
            'ОБРАЗОВАНИЕ, НАУКА, ВОСПИТАНИЕ'              => '403',
            'МАРКЕТИНГ, РЕКЛАМА, PR'                      => '400',
            'УПРАВЛЕНИЕ ПЕРСОНАЛОМ, HR'                   => '417',
            'ФИНАНСЫ, БАНК'                               => '418',
            'СФЕРА ОБСЛУЖИВАНИЯ'                          => '413',
            'АДМИНИСТРАЦИЯ, ДЕЛОВОДСТВО'                  => '423',
            'БУХГАЛТЕРИЯ, АУДИТ'                          => '394',
            'ДИЗАЙН, ТВОРЧЕСТВО'                          => '396',
            'КРАСОТА, ФИТНЕС, СПОРТ'                      => '397',
            'НЕДВИЖИМОСТЬ'                                => '425',
            'КУЛЬТУРА, МУЗЫКА, ШОУ-БИЗНЕС'                => '398',
            'ДРУГИЕ СФЕРЫ ДЕЯТЕЛЬНОСТИ'                   => '420',
            'СЕЛЬСКОЕ ХОЗЯЙСТВО, АГРОБИЗНЕС'              => '409',
            'СМИ, ИЗДАТЕЛЬСТВО, ПОЛИГРАФИЯ'               => '410',
            'ТЕЛЕКОММУНИКАЦИИ И СВЯЗЬ'                    => '414',
            'СТРАХОВАНИЕ'                                 => '411',
            'ЮРИСПРУДЕНЦИЯ'                               => '419',
            'ТОП-МЕНЕДЖМЕНТ, РУКОВОДСТВО'                 => '393',
            'СЕКРЕТАРИАТ, ДЕЛОПРОИЗВОДСТВО, АХО'          => '408',
        ];

        \Module::add_response('category_index', isset($sort[$category]) ? $sort[$category] : 'none!!!');

        return isset($sort[$category]) ? $sort[$category] : '420';
    }

    private function __vac_get_content()
    {
        $text = '';

        foreach ($this->__document->find('.key-info *') as $dom)
        {
            if ($dom->tagName == 'p' || $dom->tagName == 'ul' || $dom->tagName == 'ol')
            {
                $text .= "<{$dom->tagName}>" . strip_tags(pq($dom)->html(), '<ul><li><p><b><strong>') . "</{$dom->tagName}>";
            }
        }

        return $text;
    }

    private function __vac_get_contacts()
    {
        $result = [
            'phone'    => null,
            'chedule'  => null,
            'exp'      => null,
            'edc'      => null,
            'site'     => null,
            'city'     => null,
            'salary'   => null,
            'contactp' => null,
            'email'    => null,
        ];

        $match = [];

        foreach ($this->__document->find('.content') as $dom)
        {
            $string = $dom->nodeValue;


            preg_match('~(\d*.{0,6}грн)~iu', $string, $match);
            $result['salary'] = isset($match[1]) ? $match[1] : '';

            preg_match('~' . 'регион' . '\s(.*)~iu', $string, $match);
            $result['city'] = isset($match[1]) ? site_metrics::g__deep_city_finder($match[1]) : '';

            $result['city'] = preg_replace('~,$~', '', $result['city']);

            $result['chedule'] = $this->__get_chedule_id($string);

            $mage = [];
            if (preg_match('~опыт.*(\d{1,2})|опыт.*раб.*(\d{1,2})~iu', $string, $mage))
            {
                $result['exp'] = isset($mage[1]) ? $mage[1] : 0;
            }

            $result['edc'] = self::get_education_type($string);

            foreach (pq($dom)->find('div:contains("' . 'Контактное лицо' . '") + div ') as $domIN)
            {
                $result['contactp'] = $domIN->nodeValue;
            }

            foreach (pq($dom)->find('div:contains("' . 'Телефон' . '") + div ') as $domIN)
            {
                $result['phone'] = $domIN->nodeValue;
            }

            foreach (pq($dom)->find('div:contains("' . 'E-mail' . '") + div ') as $domIN)
            {
                $result['email'] = $domIN->nodeValue;
            }

            foreach (pq($dom)->find('div:contains("' . 'Cайт' . '") + div ') as $domIN)
            {
                $result['site'] = $domIN->nodeValue;
            }
        }

        return $result;
    }

    private function __get_chedule_id($string)
    {
        $cats = [];

        if (preg_match('~' . 'полная занятость' . '~i', $string))
        {
            $cats[] = 1;
        }

        if (preg_match('~' . 'неполная занятость|частичная' . '~i', $string))
        {
            $cats[] = 2;
        }

        if (preg_match('~' . 'удаленная работа' . '~i', $string))
        {
            $cats[] = 3;
        }

        if (count($cats) == 0)
        {
            return 1;
        }

        return implode(',', $cats);
    }

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

    private function __get_company_name()
    {
        foreach ($this->__document->find('.company-name') as $dom)
        {
            return trim($dom->nodeValue);
        }
    }

    private function __get_company_id()
    {
        foreach ($this->__document->find('.company-name') as $dom)
        {
            $ma = [];
            preg_match('~(\d{1,8})~', $this->__get_attr($dom, 'href'), $ma);

            return isset($ma[1]) ? $ma[1] : null;
        }
    }

}
