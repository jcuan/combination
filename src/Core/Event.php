<?php

namespace Star\Core;

/**
 * Class Event
 *
 * 预定义事件：
 * BEFORE_CONTROLLER：在实例化controller之前
 * BEFORE_RESPONSE: 在发送response之前（注意发生异常的时候不会触发本事件）
 * AFTER_RESPONSE：在发送请求之后
 *
 * @package Star\Core
 */


class Event
{
    /**
     *
     * $handlers=[EVENT_NAME=>event obj]
     *
     * @var array 事件列表
     */
    static $events=[];

    /**
     * 已经触发的事件名称
     *
     * 是一个map，[EVENT_NAME=>true]
     *
     * @var array
     */
    static $triggeredEvents=[];


    /**
     * 触发一个事件
     *
     * @param string $eventName
     * @param bool $strict 是否严格：如果为true的话，当不存在这个事件对应的handler时，会触发异常
     * @throws StarException
     */
    public static function trigger($eventName, $strict=true)
    {
        if ( !isset(self::$events[$eventName])) {
            if ($strict===false){
                return;
            }
            throw new StarException('event:'.$eventName.' is not defined');
        }
        self::$events[$eventName]->handle(App::$container);
        self::$triggeredEvents[$eventName]=true;
    }

    /**
     * 注册一个事件
     *
     * @param EventInterface $event
     * @throws StarException
     */
    public static function register(EventInterface $event)
    {
        $eventName=$event->eventName();
        if ( isset(self::$events[$eventName])) {
            throw new StarException('duplicated event name:'.$eventName);
        }
        self::$events[$eventName]=$event;
    }

    /**
     * 判断一个事件是否被触发
     *
     * @param string $eventName
     * @return bool
     */
    public static function isTriggered($eventName)
    {
        return isset(self::$triggeredEvents[$eventName]);
    }

}