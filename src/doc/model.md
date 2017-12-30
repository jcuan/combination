## 模型

所用的模型都位于app/models目录下，需要继承自`Star\core\BaseModel`

BaseModel里含有$app对象，通过它使用框架资源

```php
<?php
namespace App\models;
use Star\core\BaseModel;

class User extends BaseModel
{
    public function word()
    {
        //$this->app->db->;
    }
}
```

db对象的使用参见laravel5.4数据库文档