<?php

namespace trud\templates\email;

use MBCMS\image_galary;
use MBCMS\routes;

class email_template_serach_agents extends \Module
{

    public function init()
    {
        parent::init();

        $this->__resources = [];

        $data = $this->__data ? $this->__data : [];

        foreach ($data as $item)
        {
            $item->dop_title = isset($item->companyname) ? $item->companyname : (isset($item->lastname) ? $item->lastname . ' ' . $item->firstname : '');

            if (isset($item->companyname))
            {
                $item->src  = image_galary::factory()->get_image_src_by_tags(['company_id_mini' => $item->companyid]);
                $item->href = 'http:\\\\' . $_SERVER['HTTP_HOST'] . routes::link('vacancy', $item->id);
            }
            else if (isset($item->firstname))
            {
                $item->src  = image_galary::factory()->get_image_src_by_tags(['resume_id' => $item->id]);
                $item->href = 'http:\\\\' . $_SERVER['HTTP_HOST'] . routes::link('resume', $item->id);
            }

        }

        $this->__resources = $data;
    }

    /**
     * @param $resource - array of resumes or vacancies
     * @return $this
     */
    public function setData($resource)
    {
        $this->__data = $resource;

        return $this;
    }
}