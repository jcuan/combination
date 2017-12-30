<?php
//总的配置文件

//数据库的模块来自laravel，参考laravel的配置：http://laravelacademy.org/post/6947.html
$config['db']=[
'driver'    => 'mysql',
'host'      => 'localhost',
'database'  => '',
'username'  => '',
'password'  => '',
'charset'   => 'utf8',
'collation' => 'utf8_unicode_ci',
'prefix'    => '',
];

$config['bootstrap']=[
'db','session','request','response'
];

//路由
$config['setting']=[
'maintain'=>false,
'handler'=>'Test/index',
'routeCache'=>false,
];

//日志
//等级为false（boole）的时候拒绝使用
$config['log']=[
'stream'=>[
'enable'=>false,
'path'=>APP_PATH.'/logs/log',
'level'=>'info',
'maxFile'=>30,  //日志保存的最长天数
],
'mail'=>[
'enable'=>false,
'level'=>'error',
'to'=>'957551963@qq.com',
'info'=>[
'from'=>'',
'host'=>'',
'port'=>'',
'userName'=>'',
'password'=>'',
'ssl'=>true   //是否使用ssl加密
]
],
];

//身份验证使用的class
$config['auth']=[
'class'=>'Star/system/BaseAuth'
];


//验证码组件
$config['captcha']=[
'fontFile'=>BASE_PATH.'resource/ziti.ttf',
'witdh'=>80,
'height'=>30,
'codeNum'=>4,
];

//威信登录验证
$config['wechat'] = [
'debug'  => false,
'app_id' => 'your-app-id',
'secret' => 'you-secret',
'token'  => 'easywechat',
// 'aes_key' => null, // 可选
'log' => [
'level' => 'debug',
'file'  => APP_PATH.'easywechat.log', // XXX: 绝对路径！！！！
],
'oauth' => [
'scopes'   => ['snsapi_userinfo'],
'callback' => '/home/login',
],

];



return $config;