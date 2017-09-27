<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Useradmin */

$this->title = Yii::t('admin', 'Администрирование пользователей (редактирование)'). ': ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Администрирование пользователей'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

//var_dump($_SESSION['filter']);
//die();


?>

<div class="useradmin-update">
    <h1><?= Html::encode($model->id.": ".$model->name) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>