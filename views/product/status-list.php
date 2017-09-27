<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Управление продуктами');
$this->params['breadcrumbs'][] = $this->title;
//print Yii::$app->controller->uniqueId;
//print '<br>';
//print Yii::$app->controller->view->uniqueId;
?>
<style>
    table thead,table thead a,thead a:link, thead a:visited{color:#444;}
</style>

<div class="product-status-list">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Добавить новый продукт'). ' +', ['/product/create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    foreach (array(10, 20, 30, 50, 100) as $count){
        $params = array_replace($_GET, ['page-size' => $count]);
        if (isset($params['page'])) unset($params['page']);
        $params = array_merge(['/product/status-list'],$params);
        $links[] = Html::a($count,\yii\helpers\Url::toRoute($params));
    }?>

    <p>Выводить по: <?php echo implode(', ', $links); ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [

            [
                'attribute' => 'id',
                'format' => 'html',
                'content' => function($model){
                    return '<a href="/product/update?id='.$model->id.'">'.$model->id.'</a>';
                },
            ],
            [
                'attribute' => 'name',
                'format' => 'html',
                'content' => function($model){
                    return '<a href="'.$model->catalogPath.'">'.$model->name.'</a>';
                },
            ],
            [
                'attribute' => 'bonus',
                'format' => 'html',
                'content' => function($model){
                    //return Html::activeCheckbox($model,'bonus',['label' => '']);
                    return Html::checkbox('Goods[bonus]',$model->bonus ? true : false);
                },
            ],
            [
                'attribute' => 'new',
                'format' => 'html',
                'content' => function($model){
                    //return Html::input('checkbox','Goods[new]',$model->new);
                    return Html::checkbox('Goods[new]',$model->new ? true : false);
                },
            ],
            [
                'attribute' => 'sale',
                'format' => 'html',
                'content' => function($model){
                    return Html::checkbox('Goods[sale]',$model->sale ? true : false);
                },
            ],
            [
                'attribute' => 'position',
                'format' => 'html',
                'content' => function($model){
                    return Html::input('text','Goods[position]',$model->position);
                },
            ],
            [
                'attribute' => 'confirm',
                'format' => 'html',
                'content' => function($model){
                    return Html::checkbox('Goods[confirm]',$model->confirm ? true : false);
                },
            ],
            [
                'attribute' => 'show',
                'format' => 'html',
                'content' => function($model){
                    return Html::checkbox('Goods[show]',$model->show ? true : false);
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'content' => function($model){
                    return Html::checkbox('Goods[status]',$model->status ? true : false);
                },
            ],

            [
                'attribute' => 'shop',
                'value' => 'shop.name',
            ],
        ],
    ]); ?>

</div>
