<?php

namespace Star\Core;

/**
 * 基本的组件类，框架的组件都继承本类
 */

class BaseComponent
 {
     /**
      * 服务容器，可以在里边得到其他的服务
      *
      * @var \Pimple\Container
      */
     protected $container;

     /**
      * 将容器设置为属性
      */
    public function __construct()
    {
        $this->container = App::$container;
    }

    /**
     * 创建组件，
     * 会被加入服务容器，在加入服务容器的时候会检查是否已经存在服务，存在的话会报错
     *
     * @param array $config 配置数组
     * @param bool $addToContainer 是否需要加入到服务容器
     * @throws StarException
     * @return static
     */
    static function createService($config=[],$addToContainer=true)
    {
        //如果没有配置文件会在config中查找
        $className=substr(strrchr(static::class,'\\'),1);
        $className=strtolower($className);
        if(!$config){
            $app=App::getInstance();
            //首先获得当前需要创建的组件名称
            $defaultConfig=$app->getConfig($className);
            if($defaultConfig!==false){
                $config=$defaultConfig;
            }
        }
        $requiredKeys=static::requiredConfigAttributes();
        $optionKeys=static::optionalConfigAttributes();
        $keys=array_keys($config);
        if ( $keys && $invalidList=array_diff($keys,array_merge($requiredKeys,$optionKeys)) ) {    //检测是否有不允许的配置
            throw new StarException('the config contains invalid keys:'.implode($invalidList,','));
        } else if ( $lackKeys=array_diff($requiredKeys,$keys)){    //检测是否必需的配置已经传入
            throw new StarException('the config array lacks some required keys :'.implode($lackKeys,','));
        } else {
            $obj =  new static;
            if($config){
                $obj->config($config);
            }
            if($addToContainer){
                if(!isset(App::$container[$className])){
                    App::$container[$className]=$obj;
                }else{  //冲突
                    throw new StarException('Duplicated service name in container:'.$className);
                }
            }
            return $obj;
        }
     }

     /**
      * 返回本组件配置必须配置的属性
      *
      * @return array
      */
    static function requiredConfigAttributes()
    {
        return [];
    }

    /**
     * 可选的配置
     *
     * @return array
     */
    static function optionalConfigAttributes()
    {
        return [];
    }

    /**
     * 默认的配置函数，直接操作属性
     *
     * @param $config
     * @return void
     */
     public function config($config)
     {
         //跳过指明使用哪个类的函数
         foreach($config as $key => $value) {
             $this->$key = $value;
         }
     }

     
 }