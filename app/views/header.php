<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$title?></title>
    <link href="/static/css/index.css" rel="stylesheet">
</head>
<body>
<nav class="nav">
    <li <?php if($nav=='index') echo 'class="nav-activate"'?>><a href="/">投票</a></li>
    <li <?php if($nav=='rank') echo 'class="nav-activate"'?>><a href="/home/rank">排名</a></li>
    <li <?php if($nav=='rules') echo 'class="nav-activate"'?>><a href="/home/rules">活动规则</a></li>
</nav>
<div class="header">
    <h3><?=$voteName?></h3>
    <img src="/static/img/header.jpg"/>
</div>
<div class="row header-data-box">
    <span class="header-data">
        投票总数: <?=$voteSum?>
    </span>
    <span class="header-data">
        点击量: <?=$clickSum?>
    </span>
</div>