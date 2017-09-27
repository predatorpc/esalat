<?php

/*
 * @var array model
 * */

foreach($model as $menuItem){
    ?>
    <li>
        <a href="<?=$menuItem['url']?>" title="<?=$menuItem['title']?>">
            <?=$menuItem['title']?>
        </a>
    </li>
    <?=(!empty($menuItem['items']))? $this->render('_childrenCategory',['model' => $menuItem['items']]):''?>
    <?php
}

