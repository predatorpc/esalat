<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Shops */

$this->title = Yii::t('admin', 'Добавить магазин');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Магазины'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shops-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'create' => true,
        'error' => '',
    ]) ?>

</div>
