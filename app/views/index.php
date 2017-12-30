<div class="search-box">
    <form action="" method="get">
        <div class="search-input">
            <input type="text" class="header-input" name="keyword" id="keyword" placeholder="搜索姓名">
            <input type="submit" class="button" value="搜索">
        </div>
    </form>
</div>
<div class="object-box">
    <?php
    $index=1;
    foreach($list as $obj):?>
    <div class="object-ele <?php if($index%2==0) echo 'ele-r'?>">
        <a href="/home/view/<?=$obj->oid?>"><img src="<?=$obj->image?>"></a>
        <div class="ele-text">
            编号-<?=$obj->oid?>
        </div>
        <div class="ele-text">
            <?=$obj->name?>
        </div>
        <div class="ele-text">
            票数-<?=$obj->vote?>
        </div>
        <div class="ele-button" data-id="<?=$obj->oid?>">
            给他投票
        </div>
    </div>
    <?php $index++; endforeach;?>
</div>
<div class="page-box">
    <?php if($prePage):?>
    <a href="<?=$prePage?>">上一页</a>
    <?php endif?>
    <?php if($nextPage):?>
    <a href="<?=$nextPage?>">下一页</a>
    <?php endif?>
    <span class="text">
        第<?=$page?>页,共<?=$pageSum?>页,跳转
    </span>
    <input type="text" id="input-page" data-go="<?=$go?>" placeholder="页数">
    <button class="page-button">确定</button>
</div>