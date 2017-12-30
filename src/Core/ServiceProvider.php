<?php

namespace Star\core;

/**
 * pimple的依赖注入容器中各种服务的提供者
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\HttpFoundation\Session\Session;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SwiftMailerHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


class ServiceProvider implements ServiceProviderInterface
{
    /**
     *  注册一些需要的服务 
     *
     * @param Container $container
     * @return void
     */
    public function register(Container $container)
    {
        //获得框架对象
        $app=App::getInstance();
        $bootstrapList=$app->getConfig('bootstrap');

        //日志
        $container['log']=function() use ($app) {
            $config=$app->getConfig('log');
            if(isset($config['class'])){
                return call_user_func($config['class'].'::create');
            }
            $logger = new Logger('main');
            //检查日志级别配置是否正确
            $checkLevelFunc = function ($level) {
                $level = strtolower($level);
                if ($level == 'error') {
                    $level = logger::ERROR;
                } elseif ($level == 'info') {
                    $level = logger::INFO;
                } elseif ($level == 'warning') {
                    $level = logger::WARNING;
                } else {
                    throw new StarException('日志级别只能选择error、warning或者info');
                }
                return $level;
            };
            //文件
            $streamConfig = $config['stream'];
            if ($streamConfig['enable']) {
                $streamConfig['level'] = strtolower($streamConfig['level']);
                $level = $checkLevelFunc($streamConfig['level']);
                $logger->pushHandler(new RotatingFileHandler($streamConfig['path'], $streamConfig['maxFile'], $level));
            }
            //邮箱
            if (isset($config['mail'])) {
                $mailConfig = $config['mail'];
                if ($mailConfig['enable']) {
                    if ($mailConfig['info']['ssl']) {
                        $transport = (new \Swift_SmtpTransport($mailConfig['info']['host'], $mailConfig['info']['port'], 'ssl'));
                    } else {
                        $transport = (new \Swift_SmtpTransport($mailConfig['info']['host'], $mailConfig['info']['port']));
                    }
                    $transport->setUsername($mailConfig['info']['userName'])->setPassword($mailConfig['info']['password']);
                    $mailer = new \Swift_Mailer($transport);

                    $message = (new \Swift_Message('Website Error'))
                        ->setFrom([$mailConfig['info']['userName']=>$mailConfig['from']])
                        ->setTo($mailConfig['to'])
                        ->setBody('body for replace');
                    $level = $checkLevelFunc($mailConfig['level']);
                    $logger->pushHandler(new SwiftMailerHandler($mailer, $message, $level));
                }
            }
            return $logger;
        };

        //session
        if(in_array('session',$bootstrapList)){
            $container['session']=function() use ($app) {
                $session=new Session();
                $session->start();
                return $session;
            };
        }

        //数据库
        if(in_array('db',$bootstrapList)){
            $container['db']=function() use ($app)  {
                $config=$app->getConfig('db');
                $capsule = new Capsule;
                $capsule->addConnection($config);
                $capsule->setAsGlobal();
                return $capsule;
            };
        }

        //请求对象
        if(in_array('request',$bootstrapList)){
            $container['request']=function(){
                return Request::createFromGlobals();
            };
        }

        //相应对象
        if (in_array('response',$bootstrapList)) {
            $container['response']=function(){
                return new Response('',200,['Content-Type'=>'text/html']);
            };
        }
    }

}
