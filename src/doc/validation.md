
## 数据验证和处理

借助了composer组件GUMP，增加了错误码和自定义错误信息功能

### overview

```php

$patterns=[
    [
        'name',
        '姓名',
        'required|min_len,1|max_len,15',
        [
            'min_len'=>'11_',
        ]
    ]
]

```

