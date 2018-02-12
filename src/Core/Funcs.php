<?php

namespace Star\Core;

use Symfony\Component\HttpFoundation\Response;

class Funcs
{
    /**
     * 错误处理函数：处理php语法等的错误。在组件内由用户检测到的错误通过抛出异常交给ExceptioHandler处理
     *
     * @param int $errorLevel
     * @param string $errorMsg
     * @param string $errorFile
     * @param int $errorLine
     * @return void
     */
    public static function errorHandler(/** @noinspection PhpUnusedParameterInspection */ $errorLevel , $errorMsg, $errorFile, $errorLine )
    {
        if(error_reporting()==0){   //正常情况下不会是0的，这个时候是使用了@错误屏蔽符
            return; 
        }
        self::showError('error',$errorMsg.'--file:'.$errorFile.'--line:'.$errorLine);
    }

    /**
     * 捕获没有被捕获的异常
     *
     * @param \Throwable $ex
     * @return void
     */            
    public static function exceptionHandler(\Throwable  $ex) {
        self::showError('error',$ex->getMessage().'--file:'.$ex->getFile().'--line'.$ex->getLine());
    }

    /**
     * 通过页面展示错误
     * 
     * 依赖ENV环境变量，prod环境下不会显示错误详细信息
     *
     * @param string $level 三种等级：error、info、warning
     * @param string $content
     * @param string $title
     * @return void
     */
    public static function showError($level, $content, $title='Error')
    {
        if($title=='Error'){
            self::log($level,$content);
        }else{
            self::log($level,$title.'----'.$content);
        }
        if(ENV != 'dev') {
            $content = 'Sorry，服务器产生了某个错误 )：';
            $title = '服务器错误';
        }
        self::loadView('core/error',['content'=>$content,'title'=>$title]);
        $response=App::getInstance()->response;
        $response->setContent(ob_get_clean());
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->headers->set('Content-Type','text/html');
        $response->send();
        exit(1);
    }

    /**
     * 记录日志，采用了一个logger实例的方式
     *
     * @param string $level
     * @param string $info
     * @return void
     */
    public static function log($level,$info)
    {
        $app = App::getInstance();
        $level=strtoupper($level);
        if(!in_array($level,['ERROR','INFO','WARNING'])){
            $app->log->error('unknown log level : '.$level);
            $level='error';
        }
        if($level=='ERROR'){
            $app->log->addError($info);
        }elseif($level=='WARNING'){
            $app->log->addWarning($info);
        }else{
            $app->log->addInfo($info);
        }
    }

    public static function show404()
    {
        self::loadView('core/404');
        $response=App::getInstance()->response;
        $response->setContent(ob_get_clean());
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->headers->set('Content-Type','text/html');
        $response->send();
        exit(1);
    }

    /**
     * 生成随机英文字母字符串
     *
     * @param int $len
     * @return string
     */
    public static function randomChars($len)
    {
        $return='';
        for($i=0;$i<$len;$i++){
            $return.= chr(mt_rand(33, 126)); 
        }
        return $return;
    }


    /**
     * 载入单个视图
     *
     * @param string $location 相对于APP_PATH./views的位置
     * @param array $params 参数列表
     * @return void
     */
    public static function loadView($location, $params = [])
    {
        $path = APP_PATH . '/views/' . $location . '.php';
        extract($params);
        include $path;
    }
    
}