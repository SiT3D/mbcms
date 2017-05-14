<?php

namespace listners;

use event\image_galary\upload;
use MBCMS\image_galary;


class admin_companies_upload_listen
{

}

(new upload())->listen(function(upload $event)
{
    if (\GetPost::uget('is_admin_add'))
    {
        (new upload_mc_company_image())->go($event, \GetPost::uget('company_id'));
    }
});
