<?php

namespace Star\Components;

use Star\Core\BaseComponent;
use Star\Core\Response;

/**
 * 验证码类，集成了通过session的验证
 */

class Captcha extends BaseComponent
{
    protected $width=80;

    protected $height=30;

    /**
     * 验证码的数目
     *
     * @var int
     */
    protected $codeNum=4;
    
    /**
     * 验证码中的文字
     *
     * @var string
     */
    protected $code;

    /**
     * 图像对象
     *
     * @var Resource
     */
    protected $im;

    /**
     * 字体文件位置
     *
     * @var string
     */
    protected $fontFile;


    static function requiredConfigAttributes()
    {
        return ['fontFile'];
    }

    static function optionalConfigAttributes()
    {
        return ['width','height','codeNum'];
    }

    /**
     * 生成文件
     *
     * @param string $type 验证码的种类，也就是用在网站的那个地方
     * @return void
     */
    public function showImg($type)
    {

        $this->createCode();
        //创建图片
        $this->createImg();
        //设置干扰元素
        $this->setDisturb();
        //设置验证码
        $this->setImageCode();

        //在session中保存验证码
        $session=$this->container['session'];
        $session->set('captcha',[$type=>$this->code]);

        //输出图片
        $this->outputImg();
    }


	/**
	 * check 检测验证码是否正确
	 * 
	 * @param string $code 传入的验证码
     * @param string $type 指明是网站的哪个地方使用了验证码
	 * @param bool $ajaxPreCheck 是否设置验证码通过的标志captchaPassed,用于ajax验证密码，有两次错误的机会
	 * @return bool
	 */
    public  function preCheck($code,  $type, $ajaxPreCheck=true){
        $session=$this->container['session'];
        $captchaInfo=$session->get('captcha');
        if (!$captchaInfo || !isset($captchaInfo[$type])) {
            return false;
        }
        $return=false;
		if(strtoupper($captchaInfo[$type])==strtoupper($code)){
			$return=true;
            if($ajaxPreCheck){
                $captchaInfo['captchaPassed'][$type]=true;    //标记本验证码已经通过                
            }
			unset($captchaInfo[$type]);
        }elseif ($ajaxPreCheck){      //输入了验证码，但是错误了，如果连续错误3次,销毁该验证码
            if (!isset($captchaInfo[$type]['wrongTimes'])) {
                $captchaInfo[$type]['wrongTimes'] = 1;
            }
            if ($captchaInfo[$type]['wrongTimes'] > 2) {
                unset($captchaInfo['captcha'][$type]);
            }
        }else{  //不是ajaxPreCheck,只有一次机会
            unset($captchaInfo['captcha'][$type]);
        }
        $session->set('captcha',$captchaInfo);
		return $return;
	}


    /**
     * 检测验证码是否通过
     *
     * @param string $code
     * @param string $type
     * @return bool
     */
    public  function check( $code, $type)
    {
        $session=$this->container['session'];
        $captchaInfo=$session->get('captcha');
        if(isset($captchaInfo['captchaPassed'][$type])){
            unset($captchaInfo['captchaPassed'][$type]);
            $return =  true;
        }elseif(isset($captchaInfo[$type])){
            return $this->preCheck($code,$type,false);   //不要设置验证通过的标志
        }else{
            $return = false;            
        }
        $session->set('captcha',$captchaInfo);
		return $return; 
    }

    /**
     * 设置字体文件位置
     *
     * @param string $file
     * @return void
     */
    public function setFontFile($file)
    {
        $this->fontFile=$file;
    }

    private function createImg()
    {
		$this->im = imagecreatetruecolor($this->width, $this->height);
		$bgColor = imagecolorallocate($this->im, 255, 255, 255);
		imagefill($this->im, 0, 0, $bgColor);
    }

    /**
     * 设置干扰
     *
     * @return void
     */
    protected function setDisturb()
    {
		$area = ($this->width * $this->height) / 40;
		$disturbNum = ($area > 250) ? 250 : $area;
            //加入点干扰
        for ($i = 0; $i < $disturbNum; $i++) {
            $color = imagecolorallocate($this->im, 30, 144, 255);
            imagesetpixel($this->im, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);
        }
            //加入弧线
        for ($i = 0; $i <= 5; $i++) {
            $color = imagecolorallocate($this->im, 30, 144, 255);
            imagearc($this->im, rand(0, $this->width), rand(0, $this->height), rand(30, 300), rand(20, 200), 50, 30, $color);
        }
    }

    /**
     * 生成需要验证的验证码
     *
     * @return void
     */
    protected function createCode()
    {
		$str = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ';
		$this->code='';
		$strLen=strlen($str);
        for ($i = 0; $i < $this->codeNum; $i++) {
            $this->code .= $str{rand(0, $strLen - 1)};
        }
    }

    /**
     * 将文字验证码填入图片
     *
     * @return void
     */
    protected function setImageCode()
    {
        for ($i = 0; $i < $this->codeNum; $i++) {
            $color = imagecolorallocate($this->im, 30, 144, 255);
            $x = floor($this->width / $this->codeNum) * $i +3;
            $y = $this->height/2+9;
            if ($this->fontFile) {
                $angle=rand(0, 30);	//角度
                imagettftext($this->im, 20, $angle, $x, $y, $color, $this->fontFile, $this->code{$i} );
            } else {
                imagechar($this->im,10, $x, $y, $this->code{$i}, $color);
            }
        }
    }

    /**
     * 输出图片
     *
     * @return bool
     */
    private function outputImg()
    {
        if (imagetypes() & IMG_JPG) {
            header('Content-type:image/jpeg');
            imagejpeg($this->im);
        } elseif (imagetypes() & IMG_GIF) {
            header('Content-type: image/gif');
            imagegif($this->im);
        } elseif (imagetypes() & IMG_PNG) {
            header('Content-type: image/png');
            imagepng($this->im);
        } else {
            return false;
        }
        Response::$send=false;
        return true;
    }

//class ed 
}
