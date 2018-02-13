## 控制器、请求和响应

所有的控制器都位于app/controllers目录下，需要继承自`\Star\core\BaseController`，
控制器内含有$app对象，通过它使用框架资源

请求对象通过$app->request得到

### 请求数据获取

```php
<?php
$app=App::getInstance();
//通过$app获得request对象
$app->request->query->get('bar', 'baz');    //获得url参数
```
### 响应

- 借助默认的请求相应  
  ```php
  <?php
    //controller的函数返回的信息会默认以http状态码200,content-type为text/html发送
    public function helloworld()
    {
      return 'helloworld';
    }
  ```
  
- 使用了view的情况
  ```php
  <?php
      //load的信息都存在输出缓存中，通过return输出缓存完成输出
      //controller的getCache就是ob_get_clean
      public function view()
      {
          return $this->render('view',['xxx'=>'xxx']);
      }    
  ```


request对象更多细节参见symfony/http-foundation文档