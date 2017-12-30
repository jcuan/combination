<?php

namespace Star\Core;

use Pimple\Container;

interface EventInterface
{

    /**
     * 得到事件名称
     *
     * @return string 事件名称
     */
    public function eventName();

    /**
     * 事件处理函数
     *
     * @param Container $container
     * @return mixed
     */
    public function handle(Container $container);
}