<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Каталог товаров';
$this->params['breadcrumbs'][] = $this->title;

print Yii::$app->controller->uniqueId;
print '<br>';
print Yii::$app->controller->route;
?>
<div class="catalog-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    if(isset(Yii::$app->controller->catalogMenu) && !empty((Yii::$app->controller->catalogMenu))){
        foreach((Yii::$app->controller->catalogMenu) as $menuItem){
            print $this->render('_generalCategory',['model' => $menuItem]);
        }
    }
    ?>
</div>
