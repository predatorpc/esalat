<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\managment\models\ShopsCallback */

$this->title = 'Update Shops Callback: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shops Callbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shops-callback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
