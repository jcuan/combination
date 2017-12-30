<?php

namespace Star\Core;

class BaseModel
{
    /**
     * 框架对象 $app
     *
     * @var App
     */
    protected $app;

    public function __construct()
    {
        $this->app=App::getInstance();
    }

}