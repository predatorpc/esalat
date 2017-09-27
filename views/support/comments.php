<?php
use app\modules\common\models\ModFunctions;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use yii\data\ActiveDataProvider;
use app\modules\catalog\models\GoodsComments;
use app\modules\catalog\models\Goods;
$this->title = Yii::t('admin', 'Комментарии');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Управление магазином'), 'url' => ['shop-management'],'template' => "/ {link} /\n",];
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Техподдержка'), 'url' => [' '],'template' => "{link} /\n",];
$this->params['breadcrumbs'][] = ['label' => $this->title,'template' => "{link} \n",];

?>
<script type="application/javascript">
    // Пометить статус;
    function status_check_comments(id) {
        loading('show');
        var status = $("#cms-feedback tr[data-key='"+ id + "'] input").filter('input:checked').length;
        $.post(location.href, {'comments':true, 'status': status, 'id': id}, function (response) {
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
    <?php
    $dataProvider = new ActiveDataProvider([
        'query' => GoodsComments::find()->orderBy('date DESC'),
        'pagination' => [
            'pageSize' => 20,
        ],
    ]);
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions'=>function ($model, $key, $index, $grid){
            $class = $model->status == 0?'warning':'';
            // print_arr($model);
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
                'attribute' => 'good_id',
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('admin', 'Имя'),
            ],
            [
                'attribute' => 'text',
                'label' => Yii::t('admin', 'Отзыв'),
            ],
            [
                'attribute' => 'rating',
                'label' => Yii::t('admin', 'рейтинг'),
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('admin', 'Модерирование'),
                'format' => 'raw',
                'value' => function ($model, $index, $widget) {
                    return Html::checkbox('status[]', $model->status, ['onclick'=>'status_check_comments('.$index.');', 'checked' => true]);
                },
            ],
        ],
    ]);?>
</div>