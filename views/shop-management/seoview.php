<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['seoupdate', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php //= Html::a('Вернуться к списку', ['seo', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php   echo Html::a('Удалить', ['seodelete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Точно удалить?',
        //        'method' => 'post',
            ],
        ])

        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
//            'parent_id',
//            'googl_id',
            'level',
           'title',
//            'seo_title',
//            'seo_description',
//            'seo_keywords',
            'description:ntext',
            'alias',
//            'sort',
//            'active',
        ],
    ]) ?>

</div>
