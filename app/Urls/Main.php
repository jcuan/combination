<?php

namespace App\Urls;


class Main
{
    /**
     * 返回路由函数
     *
     * @return \Closure
     */
    public static function getUrlFunc()
    {
        $func = function(\FastRoute\RouteCollector $r) {
            $r->get('/','Test/index'); //设置默认页
            Test::url($r);
        };

        return $func;
    }
}