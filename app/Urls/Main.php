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
            $r->get('/','Home/index'); //设置默认页
            $r->post('/home/vote','Home/vote');
            $r->get('/home/rank','Home/rank');
            $r->get('/home/rules','Home/rules');
            $r->get('/home/view/{id}','Home/view');


            $r->addRoute(['POST','GET'],'/admin/login','Admin/login');
            $r->get('/admin','Admin/index');
            $r->get('/admin/rank','Admin/rank');
            $r->get('/admin/delete/{id}','Admin/delete');
            $r->addRoute(['POST','GET'],'/admin/add','Admin/add');
            $r->addRoute(['POST','GET'],'/admin/edit/{id}','Admin/edit');
            $r->addRoute(['POST','GET'],'/admin/setting','Admin/setting');

            //Test::url($r);  //注册其他controller的路由
            $r->addRoute('GET','/test','Test/hello');
        };

        return $func;
    }
}