<?php

namespace Star\Core;

use Symfony\Component\HttpFoundation\Response;
use FastRoute\Dispatcher;

class Route
{
    /**
     * 路由
     *
     * @param callable $func \App\Urls\Main::getUrlFunc()返回的函数
     * @return void
     */
    public static function init($func)
    {
        //查看配置是否设置了默认路由
        $app=App::getInstance();
        $routeConfig=$app->getConfig('route');
        if($routeConfig && $routeConfig['maintain']===true){
            $className='\App\Controllers\\'.str_replace('/','\\',dirname($routeConfig['handler']));
            $funcName=basename($routeConfig['handler']);
            $content=(new $className)->$funcName();
            if($content!==NULL){
                $response=new Response(
                    $content,
                    Response::HTTP_OK,
                    array('content-type' => 'text/html')
                );
                $response->send();
            }
            return;
        }

        $dispatcher = \FastRoute\cachedDispatcher($func, [
            'cacheFile' => BASE_PATH. '/runtime/.route.cache', /* required */
            'cacheDisabled' => !$routeConfig['cache'],     /* optional, enabled by default */
        ]);
        
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                Funcs::show404();
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response = new Response(
                    'METHOD_NOT_ALLOWED',
                    Response::HTTP_METHOD_NOT_ALLOWED,
                    ['Content-Type'=>'text/html']
                );
                $response->send();
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];   //handler是controllerName/funcName这种形式
                $vars = $routeInfo[2];
                $handlerDetail = explode('/',$handler);
                $className = '\App\Controllers\\'.$handlerDetail[0];
                $funcName = $handlerDetail[1];
                Event::trigger('BEFORE_CONTROLLER',false);
                $obj = new $className();
                if(!$vars){
                    $content=$obj->$funcName();
                }else{
                    $content=$obj->$funcName($vars);
                }

                //如果标记不需要发送请求的，不发送请求
                if (\Star\Core\Response::$send) {
                    $response=$app->response;
                    if(is_string($content)){
                        $response->setContent($content);
                    }
                    $app->endRequest();
                }
                break;
        }

    }    
}