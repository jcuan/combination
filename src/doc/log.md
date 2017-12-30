## 日志

使用monolog

### 相关配置

```php
<?php
$config['log']=[
    'stream'=>[ //保存到文件
        'enable'=>true, //是否使用
        'path'=>BASE_PATH.'/logs/log',
        'level'=>'info',    //记录等级，或者warning、error
        'maxFile'=>30,  //日志保存的最长天数
    ],
    'mail'=>[
        'enable'=>true,
        'level'=>'error',   //记录等级
        'to'=>'',   //发到哪个邮箱
        'subject'=>'website error', //日志邮件主题
        'from'=>'',    //发件人昵称
        'info'=>[   //smtp相关配置
            'host'=>'smtp.163.com',
            'port'=>'465',
            'userName'=>'',
            'password'=>'',
            'ssl'=>true   //是否使用ssl加密
        ]
    ]
];
```

### 日志快速接口

```
Funcs::log($level,$message)
```
