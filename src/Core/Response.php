<?php

namespace Star\Core;


class Response
{
    /**
     * @var bool 是否还需要发送请求，如果自己已经发送了请求，请把本变量标记为false
     */
    static $send=true;

    /**
     * 设置response的内容为错误json信息
     *
     * @param int $errorCode 错误码
     * @param string $message   错误信息
     * @return void
     */
    public static function  makeWrongJson($errorCode, $message)
    {
        $info = ['errcode'=>$errorCode,'errmsg'=>$message];
        $msg=json_encode($info , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $response=App::getInstance()->response;
        $response->setContent($msg);
        $response->headers->set('Content-Type','application/json; charset=utf-8');
    }

    /**
     * 根据输出的数组设置返回json信息
     *
     * @param array $info 需要输出的信息
     * @param boolean|int $errorCode 如果errorCode不是false，会将errorCode设置为传入的值
     * @return void
     */
    public static function makeJson($info, $errorCode=false)
    {
        if($errorCode!==false){
            $info['errcode']=$errorCode;
        }
        $msg=json_encode($info , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $response=App::getInstance()->response;
        $response->setContent($msg);
        $response->headers->set('Content-Type','application/json; charset=utf-8');
    }

}