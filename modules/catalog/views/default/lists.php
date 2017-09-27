<?php
use app\components\WProductItemOne;
use yii\widgets\Breadcrumbs;

$this->title = 'Списки товаров - корзина одним кликом';

$this->params['breadcrumbs'][] = ['label' => 'Каталог','url' => '/catalog/','template' => "{link}/ \n",];
$this->params['breadcrumbs'][] = ['label' => $this->title,'template' => "{link} \n",];

?>
<div class="content">
    <!--Хлебная крошка-->
    <?= Breadcrumbs::widget(['options' => ['class' => 'path'],'tag' => 'div','homeLink' => ['label' => 'Главная', 'url' => '/','template' => "{link}/ \n"],'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]);?>
    <!--Хлебная крошка-->
    <div class="row">
        <div class="goods">
            <div class="module___images"><?php

                if(!empty($lists)){
                    foreach ($lists as $list) {
                        print \app\components\WListInCatalog::widget([
                            'model' => $list,
                        ]);
                    }
                }?>

                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>