<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\actions\models\ActionsParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('actions', 'Actions Params');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actions-params-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('actions', 'Create Actions Params'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php //Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            //'type',
            //'area',
            //'currency',
            [
                'attribute'=>'type',
                'label' => Yii::t('actions', 'type'),
                'value' => function($model){
                    $type = [1=>'Скидка', 2=>'Cashback'];
                    return $type[$model->type];
                }
            ],
            [
                'attribute'=>'area',
                'label' => Yii::t('actions', 'area'),
                'value' => function($model){
                    $area = [1=>'Вариация', 2=>'Продукт', 3=>'Категория', 4=>'Тип', 5=>'Доставка', 6=>'Корзина', 7=>'Группа'];
                    return $area[$model->type];
                }
            ],
            [
                'attribute'=>'currency',
                'label' => Yii::t('actions', 'currency'),
                'value' => function($model){
                    $currency = [1=>'Рубль', 2=>'Процент', 3=>'Брнус'];
                    return $currency[$model->type];
                }
            ],
            'status',
            // 'created_at',
            // 'updated_at',
            // 'created_user',
            // 'updated_user',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php //Pjax::end(); ?></div>
