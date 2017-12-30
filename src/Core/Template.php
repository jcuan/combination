<?php

namespace Star\Core;


class Template
{
    /**
     * @var string 保存模板的路径
     */
    private $path;

    public function __construct($path)
    {
        $this->path=$path;
    }

    /**
     * 转义成html实体字符
     *
     * @param string $text
     * @return string
     */
    public function e($text)
    {
        return htmlspecialchars($text,ENT_QUOTES,'utf-8');
    }

    /**
     * 渲染视图
     *
     * @param string $location 位置
     * @param array $params 参数
     */
    public function render($location, $params=[])
    {
        $path = $this->path.DIRECTORY_SEPARATOR.$location . '.php';
        extract($params);
        include $path;
    }

}