<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\catalog\models\StickerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Стикеры';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sticker-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(' + Новый стикер', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            [
                'attribute' => 'iconFiles',
                'content' => function($model){
                    $img ='<img src="/files/sticker/'.$model->id.'.png"  width="40px"/>';
                    return $img;
                }
            ],

            'name',

            [
                    'attribute' => 'status',
                    'value' => function($model){
                        if($model->status == 1){
                            return 'Активно';
                        }else{
                            return 'Не активно';
                        }
                    }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
