<?php

namespace Star\Core;


class Template
{

    /**
     * 转义成html实体字符
     *
     * @param string $text
     * @return string
     */
    public static function e($text)
    {
        return htmlspecialchars($text,ENT_QUOTES,'utf-8');
    }

    /**
     * 渲染视图
     *
     * @param $location
     * @param array $params
     * @return string
     */
    public static function render($location, $params=[])
    {
        self::include($location,$params);
        return ob_get_clean();
    }

    /**
     * 包含其他的模板
     *
     * @param $location
     * @param array $params
     */
    public static function include($location,$params=[])
    {
        $path = APP_PATH.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$location . '.php';
        extract($params);
        include $path;
    }


}