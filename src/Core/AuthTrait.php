<?php
namespace Star\Core;

 trait AuthTrait
{

    /**
     * 含有身份信息的cookie
     *
     * @var string
     */
    protected $cookieId='star1';

    /**
     * 含有身份验证信息的cookie
     *
     * @var string
     */
    protected $cookieKey='star2';

    /**
     * 保存身份信息的session的key
     *
     * @var string
     */
    protected $sessionKey='user';

    /**
     * cookie有效的时间
     *
     * @var integer
     */
    protected $timeLen=1800;    //半个小时

     /**
      * @var string 设定cookie的path
      */
    protected $cookiePath='/';

     /**
      * @var string cookie的domain
      */
    protected $domain='';

    /**
     * 检查用户是否登录
     *
     * @return bool|array 如果登录了就返回用户信息
     */
    public function check()
    {
        $session=$this->getSessionHandler();
        $userInfo=$session->get($this->sessionKey);

        $return=false;
        if($userInfo){
            $return=$userInfo;
        }elseif(isset($_COOKIE[$this->cookieId]) && isset($_COOKIE[$this->cookieKey])){
            $userInfo = $this->validateCookie();
            if($userInfo){
                $session->set($this->sessionKey,$userInfo);
                $return=$userInfo;
            }
        }
        return $return;
    }

    /**
     * 验证cookie是否正确 抽象方法
     *
     * @return object|bool 错误返回false 正确返回用户信息
     */
    abstract public function validateCookie();

     /**
      * 返回可操作的session对象
      *
      * @return \Symfony\Component\HttpFoundation\Session\Session
      */
    abstract protected function getSessionHandler();


    protected function deleteCookie()
    {
        setcookie($this->cookieId,' ',time()-1,$this->cookiePath,$this->domain,false,true);
        setcookie($this->cookieKey,' ',time()-1,$this->cookiePath,$this->domain,false,true);
    }

    protected function setCookie($userName, $authKey)
    {
        setcookie($this->cookieId,base64_encode($userName),time()+$this->timeLen,$this->cookiePath,$this->domain,false,true);
        setcookie($this->cookieKey,$authKey,time()+$this->timeLen,$this->cookiePath,$this->domain,false,true);
    }

    public static function passwordHash($password)
    {
        return password_hash($password,PASSWORD_BCRYPT);//为了兼容没办法只能用md5
    }


    public function logout()
    {
        $session=$this->getSessionHandler();

        $this->deleteCookie();
        setcookie($session->getName(),' ',time() -1);//删除浏览器端cookie
        $session->invalidate();
    }
}