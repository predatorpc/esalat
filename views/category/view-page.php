<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model app\modules\pages\models\Pages */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin','Главное'), ['index-page'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin','Редактировать'), ['update-page', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'page_id',
            'url:url',
            'name',
            'text:ntext',
            'template',
            'unit',
            'level',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'status',
        ],
    ]) ?>

</div>