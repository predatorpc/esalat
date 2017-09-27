<?php
use app\modules\common\models\ModFunctions;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
$this->title = Yii::t('admin', 'Сообщение об ошибке');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Управление магазином'), 'url' => ['shop-management'],'template' => "/ {link} /\n",];
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Техподдержка'), 'url' => [' '],'template' => "{link} /\n",];
$this->params['breadcrumbs'][] = ['label' => $this->title,'template' => "{link} \n",];

?>
<h1><?=$this->title?></h1>
<div id="cms-feedback">
    <!--Хлебная крошка-->
    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [], ]);?> </a>
    <!--Хлебная крошка-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions'=>function ($model, $key, $index, $grid){
            $class = $model->status == 0?'warning':'';
            return [
                'key'=>$key,
                'index'=>$index,
                'class'=>$class
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'date',
                'label' => Yii::t('admin', 'Дата'),
                'format' => ['date', 'php:d.m.Y'],
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('admin', 'Имя (ФИО)'),
            ],
            [
                'attribute' => 'text',
                'label' => Yii::t('admin', 'Текст'),
            ],
//            'user_id',
//            'date',
//            'order',
//            'name',
//            'topic',
//            'answer:ntext',
//            'text:ntext',

            // 'answer:ntext',
            // 'topic',
            // 'order',
            // 'phone',
            // 'text:ntext',
            // 'answer:ntext',
            // 'date',
            // 'show',
            // 'status',
            [
                'attribute' => 'status',
                'label' => Yii::t('admin', 'Статус'),
                'content' => function($data){
                    if($data['status'] == 1)
                        return Yii::t('admin', 'Активная');
                    else
                        return Yii::t('admin', 'Не активная');

                }

            ],

        ],

    ]); ?>


</div>


