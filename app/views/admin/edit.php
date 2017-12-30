<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>修改外卖员</title>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <p>姓名：<input type="text" name="name" value="<?= $obj->name ?>"></p>
    <p>描述：<textarea name="description" rows="20" cols="50" placeholder="不要超过50字"><?= $obj->description?></textarea></p>
    <p>原来的照片：<img src="<?=$obj->image?>" width="250px" height="300px"></p>
    <p>照片(如果不修改照片，请保持本项为空)：<input type="file" name="file"></p>
    <input type="submit" value="提交">
</form>
<?php if(isset($info)):?>
    <p><?=$info?></p>
<?php endif;?>
<?php if(isset($error)):?>
    <p><?=$error?></p>
<?php endif;?>
</body>
</html>