<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\common\models\User;
use yii\bootstrap\Progress;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsCallbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Управление задачами';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .progress-bar-progress-yellow {
        background-color: #c3c309 !important;
    }
    .progress-bar{
        color: black !important;
    }
</style>
<div class="crm-tasks-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить задачу', ['createtask'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php

    $gridColumns = [
        [
            'class'=>'kartik\grid\SerialColumn',
            'width'=>'36px',
            'header'=>'',
        ],
        [
            'attribute' => 'name',
            'value'=>function($model){
                $count = \app\modules\crm\models\CrmTasksComments::find()->where(['read'=>0,'task_id'=>$model->id])->count();
                if($count>0){
                    $count = '<b style="color:red;">('.$count.')</b>';
                }else{
                    $count='';
                }
                return Html::a($model->name,"/crm/viewtask?id=".$model->id).$count;
            },
            'format'=>'raw',
        ],
        [
            'attribute' => 'creator',
            'format' => 'raw',
            'content' => function($data, $value){
                if(!empty($data->creator)){
                    $userName = User::find()->select(['name','phone'])->where('id = '.$data->creator)->asArray()->one();
                    return $userName['name'].'('.$userName['phone'].')';
                }
                else
                    return '';
            }
        ],
        [
            'attribute' => 'slave',
            'format' => 'raw',
            'content' => function($data, $value){
                if(!empty($data->slave)){
                    $userName = User::find()->select(['name','phone'])->where('id = '.$data->slave)->asArray()->one();
                    return $userName['name'].'('.$userName['phone'].')';
                }
                else
                    return '';
            }
        ],
        [
            'attribute' => 'description',
        ],
        [
            'attribute' => 'progress',
            'content' => function($data){
                if($data['progress'] <= 25){
                    $class = 'progress-bar-danger';
                }elseif($data['progress']>25 && $data['progress']<=50){
                    $class = 'progress-bar-progress-yellow';
                }elseif($data['progress']>50 && $data['progress']<=75){
                    $class = 'progress-bar-warning';
                }elseif($data['progress']>75 && $data['progress']<=100){
                    $class = 'progress-bar-success';
                }
                return Progress::widget([
                    'percent' => $data['progress'],
                    'label' => $data['progress'].'%',
                    'barOptions' => [
                    'class' => $class],
                    'options' => [
                        'class' => 'active progress-striped'
                    ]
            ]);
            }
        ],
        [
            'attribute' => 'start',
        ],
        [
            'attribute' => 'deadline',
        ],

        [
            'attribute' => 'date_create',

        ],
//        [
//            'attribute' => 'department',
//
//        ],
        [
            'attribute' => 'priority',
            'format' => 'raw',
            'value'=>function($model){
                $arPriority = [0 => 'Низкий', 1 => 'Средний ',2 => 'Высокий'];
                $arColor = [0 => '#5cb85c', 1 => '#f0ad4e ',2 => '#d9534f'];

                return '<span style="color:'.$arColor[$model->priority].'; font-weight:bold; ">'.$arPriority[$model->priority].'</span>';
            },

        ],

    ];

    echo GridView::widget([
        'id' => 'kv-grid-demo',
        'dataProvider'=>$dataProvider,
        'filterModel'=>$searchModel,
        'columns'=>$gridColumns,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>false, // pjax is set to always true for this demo
        // set your toolbar
        'toolbar'=> [
            '{export}',
            '{toggleData}',
        ],
        // set export properties
        'export'=>[
            'fontAwesome'=>true
        ],
        // parameters from the demo form
        'panel'=>[
            'type'=>GridView::TYPE_PRIMARY,
            'before'=>'{pager}',
        ],
        'persistResize'=>false,
        //'exportConfig'=>$exportConfig,
    ]);?>
</div>
