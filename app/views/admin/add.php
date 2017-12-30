<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>添加外卖员</title>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <p>姓名：<input type="text" name="name"></p>
    <p>描述：<textarea name="description" rows="20" cols="50" placeholder="不要超过50字"></textarea></p>
    <p>照片：<input type="file" name="file"></p>
    <input type="submit" value="提交">
</form>
<?php if(isset($id)):?>
添加成功，<a href="<?='/admin/edit/'.$id?>">修改他的信息</a>
<?php endif;?>
<p><?php if(isset($error)) echo $error?></p>
</body>
</html>