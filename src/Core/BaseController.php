<?php

namespace Star\Core;

class BaseController
{
    //框架核心资源
    protected $app;

    public function __construct()
    {
        $this->app = App::getInstance();
    }

    /**
     * 渲染模板
     *
     * @param string $location 模板位置
     * @param array $params 传入参数
     * @return string 解析后的内容
     */
    public function render($location,$params=[])
    {
        $template=new Template(APP_PATH.DIRECTORY_SEPARATOR.'views');
        $template->render($location,$params);
        return ob_get_clean();
    }
}