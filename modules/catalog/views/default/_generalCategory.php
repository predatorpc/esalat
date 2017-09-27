<?php

/*
 * @var array model
 * */

?>

<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
    <span class="general-category-icon">
        <img src="" title="<?=$model['title']?>" alt="<?=$model['title']?>"/>
    </span>
    <span class="general-category-title">
        <a href="<?=$model['url']?>" title="<?=$model['title']?>">
            <?=$model['title']?>
        </a>
    </span>
    <ul>
        <?=(!empty($model['items']))? $this->render('_childrenCategory',['model' => $model['items']]):''?>
    </ul>
</div>
