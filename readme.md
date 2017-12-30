# Combination

借助于一些php组件组合出了一个符合自己要求的小框架出来

会持续更新，将自己学到新的原则或者是理念应用进来

## 安装

下载项目之后在项目根目录文件运行下列命令安装组件
```
composer install
```
将config/prod/config.php复制到config目录下（开发环境）

## 特性

- 组合各种优秀的组件
- 拒绝过度的面向对象封装
- 通过注释和一些工具提供IDE的良好支持（强烈反对“用记事本写代码才最牛逼”的观点，当然仅限后台）

## 问题

- 处于不稳定状态
- 这种框架的东西不知道怎么写测试，所以现在基本是没什么测试的
- 使用到的组件因为自己水平原因，没有明确的接口定义，更换组件的时候不是很方便，需要参见使用的组件的接口定义
- 身份认证目前是基于cookie+session的（免登录只支持一个），对完全cookie+加密的方式需要参考一些良好实践，所以现在没有实现无状态化
- 没有任何缓存支持的组件
- 暂时没有create-project这种功能，先差不多了再说吧

## 文档

参见[项目文档](src/doc/doc.md)

## License

[MIT license](https://opensource.org/licenses/MIT)