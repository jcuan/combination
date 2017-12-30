<!doctype html>
<html lang="Zh_CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>后台管理登录</title>
</head>
<body>

<form action="" method="post">
    <input type="password" name="password" placeholder="请输入密码">
    <input type="submit" value="提交">
</form>
<?php if(isset($error)) echo $error;?>
</body>
</html>