## 目录结构

大写开头的目录对应了命名空间

```
config  配置，prod目录是环境变量为production下的配置
app / 
    - Controllers 控制器
    - Models 模型
    - views 视图
    - lang 语言包
    - Urls 路由定义
logs 日志
migration / 数据库迁移日志
runtime /   日志、路由缓存等运行时候产生的文件
resource / 如字体等资源文件
src /   本框架使用的目录，目前为止有点乱（不想再）
    Core 框架核心
    Components 框架组件（非standlone，继承自Star\Core\BaseComponent）
    Commands 命令行操作
    Libs 框架中不继承自BaseComponent的一些非核心类
    lang 语言包文件
    doc 项目文档
    tests codeception / 框架测试文件夹
        - _data 数据库
        - _output 输出
        - acceptance 验收测试
        - functional 功能测试
        - unit 单元测试
vendor /    composer安装的第三方库
web / 公开访问的目录
    - index.php 项目入口文件
init.php 初始化框架的基本信息，单独抽出来主要是方便命令行脚本使用框架资源
star    命令行工具
```

## 功能

完善中

### 路由

使用路由和controller分开的方式，路由使用了nikic/fast-route，[路由详情](src/doc/route.md)

### 控制器、请求和响应

使用symfony/http-foundation组件，[控制器、请求和相应详情](src/doc/request-response.md)

### 模型

使用了laravel的illuminate/database，[模型](src/doc/model.md)

### session

同样是使用symfony/http-foundation的session，[session详情](src/doc/session.md)

### 日志

使用momolog，[日志详情](log.md)

### 数据库版本变迁database migration

### 图片处理

### 登录接口

结合session+cookie实现

### 视图
