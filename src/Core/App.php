<?php

namespace Star\Core;

use Pimple\Container;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\DebugClassLoader;

/**
 * 框架的核心
 *
 * @property \Illuminate\Database\Capsule\Manager $db
 * @property  \Symfony\Component\HttpFoundation\Request $request
 * @property  \Symfony\Component\HttpFoundation\Response $response
 * @property  \Symfony\Component\HttpFoundation\Session\Session $session
 * @property  \Monolog\Logger $log
 */

class App
{
    /**
     *  配置信息
     *
     * @var array
     */
    private static $config;

    /**
     * 容器实例
     *
     * @var \Pimple\Container
     */
    static $container = [];

    /**
     * 自己的实例
     *
     * @var App
     */
    private static $instance = null;


    /**
     * 私有构造函数，防止外界实例化对象
     */
    private function __construct()
    {
    }

    /**
     * 私有克隆函数，防止外部克隆对象
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * 静态方法，单例统一访问入口
     *
     * @return App
     */
    static public function getInstance()
    {
        if (is_null(self::$instance) || isset (self::$instance)) {
            self::$instance = new self ();
        }
        return self::$instance;
    }

    /**
     * 初始化函数
     *
     * @return void
     */
    public function init()
    {
        //配置
        if (ENV == 'prod') {
            $path = BASE_PATH . '/config/prod/config.php';
        } else {
            $path = BASE_PATH . '/config/config.php';
        }
        self::$config = require($path);

        self::$container=new Container();

        //注册服务
        self::$container->register(new ServiceProvider());

        $otherProvider=$this->getConfig('serviceProvider');
        if($otherProvider){
            foreach($otherProvider as $provider){
                self::$container->register(new $provider());
            }
        }

        //设置错误处理函数,处于开发环境的时候，使用symfony的debug组件
        if (ENV != 'dev') {
            set_error_handler('\Star\Core\Funcs::errorHandler');
            set_exception_handler('\Star\Core\Funcs::exceptionHandler');
        } elseif (ENV == 'dev' && php_sapi_name() !=='cli') {
            Debug::enable();
            ErrorHandler::register();
            ExceptionHandler::register();
            DebugClassLoader::enable();
        }
        ob_start();
    }


    /**
     * 获得配置文件
     *
     * @param string $type 哪一种配置，比如db
     * @param string $key option 某个配置里面的内容  比如db里面的host 没有的时候返回某个type下的全部的内容
     * @return mixed 没有该配置的时候返回false
     */
    public function getConfig($type, $key = '')
    {
        if (isset(self::$config[$type])) {
            if ($key === '') {
                return self::$config[$type];
            } elseif (isset(self::$config[$type][$key])) {
                return self::$config[$type][$key];
            }
        }
        return false;
    }

    /**
     * 用来获得日志、DB等常用组件的方法
     *
     * @param string $name 组件的名称，可以是以下值db、session、request
     * @throws StarException
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array($name,['db','session','request','log','response']) && isset(self::$container[$name])) {
            return self::$container[$name];
        } else {
            throw new StarException('can not get APP property :' . $name);
        }
    }

    /**
     * 结束请求，触发BEFORE_RESPONSE事件
     */
    public function endRequest()
    {
        Event::trigger('BEFORE_RESPONSE',false);
        $this->response->send();
    }

}