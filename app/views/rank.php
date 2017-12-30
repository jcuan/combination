<div class="rank-box">
    <?php
    $rank=1;
    foreach($list as $obj):?>
        <hr/>
    <li>
        <div class="rank-num">NO.<?=$rank?></div>编号<?=$obj->oid?>-<?=$obj->name?><span><?=$obj->vote?>票</span>
    </li>
    <?php $rank++;endforeach;?>
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