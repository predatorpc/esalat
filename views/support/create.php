<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Messages */

$this->title = Yii::t('admin', 'Добавить сообщения');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Сообщения'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="messages-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="path">
        <a href="/shop-management/"><?= Yii::t('admin', 'Управление магазином') ?></a> / <a href="/support/"><?= Yii::t('admin', 'Техподдержка') ?></a> / <a href=" <?= $this->title; ?>"> <?= $this->title; ?></a> / <?= $this->title; ?></span>
    </div>    <!--Хлебная крошка-->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
