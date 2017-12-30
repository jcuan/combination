<?php

namespace App\Urls;

class Home
{
    public static function url(\FastRoute\RouteCollector $r)
    {
        $r->addGroup('/admin', function (\FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/welcome/{title}', 'Admin/welcome');
        });
    }
}
