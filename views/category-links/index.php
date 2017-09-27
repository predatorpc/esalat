<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategoryLinksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Category Links';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-links-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Category Links', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'category_id',
            'product_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
