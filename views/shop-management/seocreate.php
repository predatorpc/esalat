<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = Yii::t('admin', 'Добавить новую категорию');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Категории'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_seo_form', [
        'model' => $model,
        'parent' => $parent,
        'catMgr' => $catMgr,
        'seoMgr' => $seoMgr,
    ]) ?>

</div>
