<div class="content-box">
    <img src="<?=$obj->image?>">
    <hr>
    <h3>个人简介-<?=$obj->name?></h3>
    <hr>
    <div>
        <?=$obj->description?>
    </div>
    <hr>
    <div class="content-button" data-id="<?=$obj->oid?>">
        给他投票
    </div>

</div>