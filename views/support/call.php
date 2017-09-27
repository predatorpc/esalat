<?php
use app\modules\common\models\ModFunctions;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;

$this->title = Yii::t('admin', 'Заказать звонок');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Управление магазином'), 'url' => ['shop-management'],'template' => "/ {link} /\n",];
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Техподдержка'), 'url' => [' '],'template' => "{link} /\n",];
$this->params['breadcrumbs'][] = ['label' => $this->title,'template' => "{link} \n",];

?>
<script type="application/javascript">
    // Пометить статус;
    function status_check_call(id) {
        loading('show');
        var status = $("#cms-feedback tr[data-key='"+ id + "'] input").filter('input:checked').length;
        $.post(location.href, {'call':true, 'status': status, 'id': id}, function (response) {
            $('.table','#cms-feedback').html($(response).find('.table ','#cms-feedback').html());
            loading('hide');
        });
    }
</script>
<h1><?=$this->title?></h1>

<div id="cms-feedback">
    <!--Хлебная крошка-->
    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [], ]);?> </a>
    <!--Хлебная крошка-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions'=>function ($model, $key, $index, $grid){
            $class = $model->status == 0 ? 'warning' : '';
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
                'attribute' => 'phone',
                'label' => Yii::t('admin', 'Телефон'),
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('admin', 'Имя (ФИО)'),
            ],
//            [
//                'attribute' => 'value',
//                'label' => 'Значение',
//            ],

            // 'answer:ntext',
            // 'topic',
            // 'order',
            // 'phone',
            // 'text:ntext',
            // 'answer:ntext',
            // 'date',
            // 'show',
            // 'status',
            /*
            [
                'attribute' => 'status',
                'label' => 'Статус',
                'content' => function($data){
                    if($data['status'] == 1)
                        return 'Активная';
                    else
                        return 'Не активная';
                }
            ],*/
            [
                'attribute' => 'status',
                'label' => Yii::t('admin', 'Статус'),
                'format' => 'raw',
                'value' => function ($model, $index, $widget) {
                    return Html::checkbox('status[]', $model->status, ['onclick'=>'status_check_call('.$index.');', 'checked' => true]);
                },
            ],


        ],
    ]); ?>
</div>