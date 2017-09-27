<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\actions\models\ActionsParamsValue */

$this->title = Yii::t('app', 'Create Actions Params Value');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Actions Params Values'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actions-params-value-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
