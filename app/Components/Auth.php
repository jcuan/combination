<?php

/**
 * 身份验证组件demo
 */

namespace App\Components;

use Star\Core\AuthTrait;
use Star\Core\BaseComponent;

class Auth extends BaseComponent
{
    use AuthTrait;

    static function optionalConfigAttributes()
    {
        return ['cookieId','cookieKey','user','timeLen','cookiePath','domain'];
    }

    /**
     * 返回可操作的session对象
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    protected function getSessionHandler()
    {
        return $this->container['session'];
    }

    /**
     * 验证cookie是否正确
     *
     * 注意cookieId是base64_decode过的
     *
     * @return object|bool 错误返回false 正确返回用户信息
     */
    protected function validateCookie()
    {
        if (isset($_COOKIE[$this->cookieId]) && isset($_COOKIE[$this->cookieKey])){
            $id=base64_decode($_COOKIE[$this->cookieId],true);
            $key=$_COOKIE[$this->cookieKey];
            if(!$id){
                $this->deleteCookie();
            }else{
                $db=$this->container['db'];
                $db=\Star\Core\App::getInstance()->db;
                $userInfo=$db->table('user')->select('uid','authTime')->where(['uid'=>$id,'authKey'=>$key])
                    ->get('user')->first();
                if(!$userInfo){
                    $this->deleteCookie();
                }else{
                    //检查authTime：cookie是否过期
                    if ( (int)$userInfo->authTime > time() ){
                        $this->deleteCookie();
                    }else{
                        return $userInfo;
                    }
                }
            }
        }
        return false;
    }
}