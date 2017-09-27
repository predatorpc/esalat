<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Просмотр заказов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);

    ?>

    <p>
        <?= Html::a('Добавить заказ', ['create'], ['class' => 'btn btn-success'])?>
    </p>
<!--    'user_id' => 'User ID',-->
<!--    'code_id' => 'Code ID',-->
<!--    'type' => 'Type',-->
<!--    'extremefitness' => 'Extremefitness',-->
<!--    'comments' => 'Comments',-->
<!--    'comments_call_center' => 'Comments Call Center',-->
<!--    'date' => 'Date',-->
<!--    'call_status' => 'Call Status',-->
<!--    'status' => 'Status',-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'id',
            [
                'label' => 'Покупатель',
                'attribute' => 'user_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return $data->user->name;
                },
            ],

            [
                'attribute'=>'code_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return !empty($data->promoCode) ? $data->promoCode->code : '';
                },
            ],

            [
                'attribute'=>'type',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return $data->type;
                },
            ],

            [
                'attribute'=>'date',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return $data->date;
                },
            ],

            [
                'attribute'=>'status',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return $data->status == 1 ? '<span class="btn btn-info">Да</span>' : '<span class="btn-danger btn">Нет</span>';
                },
            ],

            [
                'label'=>'__Сумма__',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return '<div style="word-wrap: break-word">'.\app\modules\common\models\ModFunctions::money($data->money).'</div>';
                },
            ],

            [
                'label'=>'Бонусы',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return $data->bonus;
                },
            ],

            [
               'class' => 'yii\grid\ActionColumn',
               'header'=>'',
               'template' => '{update}',
            ],

        ],
    ]); ?>

</div>

