<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = Yii::t('admin', 'Редактирование категории') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Категории'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Редактирование');
?>
<div class="category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_seo_form', [
        'model' => $model,
        'parent' => $parent,
        'catMgr' => $catMgr,
        'seoMgr' => $seoMgr,

    ]) ?>

</div>
