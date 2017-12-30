## 路由

使用控制器和路由分离的方式

### 相关配置

```php
<?php
$config=['route']=[
    'maintain'=>true,   //是否开启维护界面,
    'cache'=>false, //是否缓存路由
    'func'=>'XXX/XXX/funcName',  //需要运行的controller函数
];
```

### 路由注册

简单的路由可以直接写在urls/Main里，但是比较多之后可以分成几个文件来写，
比如/test下的路由放在urls/Test里:

```php
<?php
namespace App\Urls;

class Test
{
    public static function url(\FastRoute\RouteCollector $r)
    {
        $r->addGroup('/test', function (\FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/model', 'Test/model');
            $r->addRoute('GET', '/captcha', 'Test/captcha');
            $r->addRoute('GET', '/getcaptcha', 'Test/getCaptcha');
            $r->addRoute('GET', '/show404', 'Test/show404');
            $r->addRoute('GET', '/json', 'Test/json');
            $r->addRoute('GET', '/view','Test/view');
            $r->addRoute('GET', '/session','Test/session');
            $r->addRoute('GET', '/getSession','Test/getSession');
            $r->addRoute('GET', '/twoParam/{param1}/{param2}', 'Test/twoParam');
        });

    }
}
```
卸载其他文件里的需要在main注册一下，在main里可以设置默认路由'/'
```php
<?php
namespace App\urls;

class Main
{
    /**
     * 返回路由函数
     *
     * @return \Closure
     */
    public static function getUrlFunc()
    {
        $func = function(\FastRoute\RouteCollector $r) {
            $r->get('/','Test/helloworld'); //设置默认页
            Test::url($r);  //注册其他controller的路由
        };

        return $func;
    }
}
```
