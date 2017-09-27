<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\managment\models\ShopsStores;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\managment\models\ShopStoresTimetableSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Время работы склада ('.ShopsStores::find()->where(['id'=>$store_id])->One()->AddressStringTitle.')';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-stores-timetable-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if(\app\modules\managment\models\ShopStoresTimetable::find()->where(['store_id'=>$store_id])->count()<=7){?>
    <p>
        <?= Html::a('Добавить', ['create','store_id'=>$store_id], ['class' => 'btn btn-success']) ?>
    </p>
    <?php }?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            //'store_id',
            [
                    'attribute' => 'day',
                    'value' => function($data){
                        $days = [1=>'Понедельник',
                            2 => 'Вторник',
                            3 => 'Среда',
                            4 => 'Четверг',
                            5 => 'Пятница',
                            6 => 'Суббота',
                            7 => 'Воскресение'];
                        return $days[$data->day];
                    },
                    //'mergeHeader' => true,
                    'label' => 'День недели',
            ],
            'time_begin',
            'time_end',
            [
                'attribute' =>'status',
                'value' => function($data){
                    if($data->status == 1){
                        return 'Активно';
                    }else{
                        return 'Не активно';
                    }

                }
            ],
            ['class' => 'yii\grid\ActionColumn','template' => '{update}',],
        ],
    ]); ?>
</div>
