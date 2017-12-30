<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>排行榜</title>
</head>
<body>

<table border="1">
    <tr>
        <th>编号</th>
        <th>姓名</th>
        <th>票数</th>
        <th>操作</th>
    </tr>
    <?php foreach($list as $obj):?>
    <tr>
        <td><?=$obj->oid?></td>
        <td><?=$obj->name?></td>
        <td><?=$obj->vote?></td>
        <td>
            <a href="/admin/delete/<?=$obj->oid?>">删除</a>
            <a href="/admin/edit/<?=$obj->oid?>">编辑</a>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<p>
    <?php if(isset($prePage)):?>
    <a href="<?=$prePage?>">上一页</a>
    <?php endif;?>
    <?php if(isset($nextPage)):?>
        <a href="<?=$nextPage?>">下一页</a>
    <?php endif;?>
</p>

</body>
</html>