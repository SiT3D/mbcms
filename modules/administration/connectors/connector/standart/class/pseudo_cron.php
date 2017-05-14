<?php


namespace trud\admin;

use MBCMS\cache;
use MBCMS\DB;
use trud\classes\mailer;
use trud\classes\model\cities;
use trud\classes\model\companies;
use trud\classes\model\search_agents;
use trud\classes\model\user;
use trud\classes\model\vacancies;
use trud\classes\vacancy_search;
use trud\site_metrics;
use trud\site_xml_map;
use trud\templates\email\email_template_serach_agents;

class pseudo_cron implements \adminAjax
{

    /**
     * Перед расчетами нужно заходиь на страницу по городам, чтобы пересчитать по городам (через js).
     * А затем уже делать расчеты синонимов
     */
    public function by_professions()
    {
        cache::delete();


        $profs = DB::q("SELECT * FROM t_static_professions")
            ->r("WHERE hidden = 0")
            ->get();

        foreach ($profs as $prof)
        {
            $prof->count = vacancy_search::find($prof->title, null, null, true);

            DB::q("UPDATE t_static_professions SET count = ? WHERE id = ? ", [$prof->count, $prof->id])
                ->get();
        }
    }

    public function ajax_recount_one()
    {
        $all_syns = DB::q()->s(['DISTINCT synword_morphy'], 't_synonyms')->get();

        foreach ($all_syns as $syn)
        {
            $write_word = site_metrics::morph($syn->synword);
            $count      = vacancy_search::find($syn->synword, null, null, true);
            DB::q(" INSERT INTO t_synonyms_count  (word, count) VALUES (?, ?) ON DUPLICATE KEY UPDATE count = ? ", [$write_word, $count, $count])->get();
        }

        \Module::response();
    }

    public function get_cities()
    {
        if (cache::timer('pseudocron_get_all_cities-from-synonyms', 30, cache::MINUTES))
        {
            \Module::add_response('cities', cities::factory()->get_all()->get());
        }
        else
        {
            \Module::add_response('cities', []);
        }

        \Module::response();
    }

    public function ajax_recount_two()
    {
        $current_city_id = \GetPost::uget('city_id');

        $cache      = new cache('all_db_obls');
        $all_cities = $cache->read();
        $all_syns   = DB::q()->s(['DISTINCT synword_morphy'], 't_synonyms')
            ->j('t_static_professions', 't_static_professions.morph = t_synonyms.synword_morphy 
            AND t_static_professions.hidden = 0')
            ->get();

        if (!$all_cities)
        {
            (new by_cities())->__set_obls();
        }

        $all_cities = $cache->read();


        foreach ($all_cities as $obl)
        {
            foreach ($obl->__my_cities as $city)
            {
                if ($city->count < 30 || $city->id != $current_city_id)
                {
                    continue;
                }


                foreach ($all_syns as $syn)
                {
                    $write_word = $syn->synword_morphy;

                    if (!$write_word)
                    {
                        continue;
                    }

                    $have = DB::q("SELECT * FROM t_synonyms_count_city WHERE word=? AND city_id=? LIMIT 1 ", [$write_word, $city->id])->is_mono()->get();


                    $cache = new cache('synonyms key' . $syn->synword_morphy . 'ct=' . $city->id, rand(20, 80));
                    $cache->set_multiplicator(cache::HOURS);
                    $count = $cache->result(function () use ($syn, $city)
                    {
                        return vacancy_search::find($syn->synword_morphy, null, $city->id, true);
                    });


                    if (!$have)
                    {
                        DB::q("INSERT INTO t_synonyms_count_city (word, city_id,count) VALUES (?, ?, ?)", [$write_word, $city->id, $count])->get();
                    }
                    else
                    {
                        DB::q(" UPDATE t_synonyms_count_city SET count=? WHERE id=? ", [$count, $have->id])->get();
                    }
                }
            }

        }

        \Module::response();
    }

    /**
     * скрываем вакансии старше 90 дней
     */
    public function delete_old_vacancies()
    {
        /**
         * раз в 16 часов
         */
        if (cache::timer('timer-from-new-emails-delete-old', 16, cache::HOURS))
        {
            vacancies::factory()->remove_old_vacancies(null, 3600 * 24 * 90);
            companies::factory()->update_employer_resume_views();
        }

        \Module::response();
    }

    public function remove_full_deleted_vacancies()
    {
        vacancies::factory()->remove_full_delted_vacancies();
        \Module::response();
    }


    public function search_agents_get_all()
    {
        $result = [];

        if (cache::timer('timer-from-new-emails-list', 16, cache::HOURS))
        {
            $result = (new search_agents)->get_all()->get();
        }

        $this->__remove_tmp_and_cache();

        \Module::add_response('emails', $result);
        \Module::response();
    }

    private function __remove_tmp_and_cache()
    {
        if (!file_exists(HOME_PATH . 'tmp'))
        {
            mkdir(HOME_PATH . 'tmp');
        }

        $files = scandir(HOME_PATH . 'tmp');

        foreach ($files as $file)
        {
            if (file_exists($file) && $file != '.' && $file != '..')
            {
                unlink(HOME_PATH . 'tmp' . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

    /**
     * Рассылка поисковых ботов
     */
    public function search_agents_send()
    {
        $agent_id = \GetPost::uget('agent_id');

        $user_agent = (new search_agents)->get_with_user($agent_id)->get();

        if ($user_agent->acctype == user::ACCTYPE_CANDIDATE)
        {
            $data    = (new \trud\templates\vacancies)->get_vacancies($user_agent->query, $user_agent->city_id, $user_agent->cat_id);
            $subject = 'Пследние вакансии на trud.net "' . $user_agent->query . '"';
        }
        else
        {
            $data    = (new \trud\templates\resumes_page())->get_resumes($user_agent->query, $user_agent->city_id, $user_agent->cat_id)->get();
            $subject = 'Пследние резюме на trud.net "' . $user_agent->query . '"';
        }

        if (count($data))
        {
            mailer::send($user_agent->uname, $subject, (new email_template_serach_agents())->setData($data));
            \Module::response();
        }

    }

    public function sitemap()
    {
        (new site_xml_map)->create();
        \Module::response();
    }
}