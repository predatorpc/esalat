<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Lists */

$this->title = Yii::t('admin','Добавить список').' +';
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin','Списки'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lists-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_create', [
        'model' => $model,
    ]) ?>

</div>
