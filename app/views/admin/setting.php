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
    <p>活动名称：<input type="text" style="width:300px;" name="voteName" value="<?= $voteName ?>"></p>
    <p>规则：<textarea name="rule" rows="20" cols="50"><?= $rule?></textarea></p>
    <input type="submit" value="提交">
</form>
<?php if(isset($info)):?>
    <p><?=$info?></p>
<?php endif;?>
</body>
</html>