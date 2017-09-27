<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UsersCredits */

$this->title = Yii::t('admin', 'Обновить пользователя: ') . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users Credits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="users-credits-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'id' => $id,
        'stringHash' => $users, //['name']
    ]) ?>

</div>
