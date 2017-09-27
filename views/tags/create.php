<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\Tags */

$this->title = Yii::t('admin', 'Добавить свойства');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Свойства'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tags-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'groups' => $groups,
    ]) ?>

</div>
