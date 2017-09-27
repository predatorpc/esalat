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
        <?= Html::a(Yii::t('admin','Редактировать'), ['/category/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php //= Html::a('Вернуться к списку', ['seo', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php   echo Html::a(Yii::t('admin','Удалить'), ['/category/delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('admin','Точно удалить?'),
        //        'method' => 'post',
            ],
        ])

        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'parent_id',
//            'googl_id',
            'level',
           'title',
            'anon',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'description:ntext',
            'alias',
            'sort',
            'active',
        ],
    ]) ?>

</div>
