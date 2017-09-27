<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\Actions */

$this->title = Yii::t('actions', 'Create Actions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('actions', 'Actions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
