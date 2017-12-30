<div class="note">
    每天都可以投票喔~
</div>
</body>
<?php if(isset($scripts)):?>
    <?php foreach($scripts as $script):?>
        <script src="<?=$script?>"></script>
    <?php endforeach;?>
<?php endif?>
</html>