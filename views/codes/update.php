<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\codes */

$this->title = Yii::t('admin', 'Редактирование кода').' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Редактирование');
?>
<div class="codes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'id' => $id,
        'model' => $model,
        'types' => $types,
        'users' => $users,
    ]) ?>

</div>
