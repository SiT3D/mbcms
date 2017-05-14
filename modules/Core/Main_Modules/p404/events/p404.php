<?php

namespace event;

class p404 extends event
{

    /**
     * @var модуль для замены 404 страницы
     *
     *
     * (new \event\p404())->listen(function(\event\p404 $event)
     * {
     * $event->trg = new \trud\p404;
     *
     * });
     *
     */
    public $trg;

}
