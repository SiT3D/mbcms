<?php

namespace trud\admin\templates;

use MBCMS\block;
use MBCMS\image_galary;
use trud\classes\model\partners;

class admin_partners_list extends block
{
    public function init()
    {
        parent::init();

        $this->partners = (new partners)->get_all()->get();

        foreach ($this->partners as $partner)
        {
            $partner->image_src = image_galary::factory()->get_image_src_by_tags(['partner_id' => $partner->id]);
        }

    }
}