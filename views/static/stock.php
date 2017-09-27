<?php
$this->title = $pages['name'];
?>
<!--Content-->
<div class="content">
    <div class="path">
        <a href="/"><?=\Yii::t('app','Главная');?></a>
    </div>
    <div class="row">
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">
            <h1 class="title"><?=$pages['name']; ?></h1>
            <div class="text">
                <?=$pages['text']; ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div><!--/Content-->