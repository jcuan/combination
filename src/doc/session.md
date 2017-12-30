## session

session使用了symfony/httpFundation里的session组件，为的是后边可以快速切换session handler，
比如存储到redis或者数据库中，现在使用的是php自带的file handler，在使用file handler的时候需要注意：

```
1.不使用session的地方请不要试图获取session处理对象，获得session的那一刻已经session_start
2.使用session的地方，如果后边不需要再写session了，请使用session_write_close关闭session的写权限，这是出于单用户并发的考虑
3.在使用了session_write_close之后，再试图写session不会有任何效果也不会报错，所以依赖session对象的组件不要自己session_write_close
4.在组件中使用到session对象的时候，在不影响功能的情况下尽量往后靠，使得锁住session的时间更短
```

### 依赖session的组件

使用下列组件的时候会自动开启session

- Star\component\Captcha 验证码