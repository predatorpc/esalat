<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\models\UserShop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список товаров';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);

    ?>


    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,

        'itemView' => function ($model){
            return \app\widgets\WShopProductList::widget([
                'model' => $model,
            ]);
        },
    ]);
    ?>

</div>

