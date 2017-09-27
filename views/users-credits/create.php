<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UsersCredits */

$this->title = Yii::t('admin', 'Добавить пользователя');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Users Credits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-credits-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'id' => $id,
        'stringHash' => $users, //['name']
    ]) ?>

</div>
