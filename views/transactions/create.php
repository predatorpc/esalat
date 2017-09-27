<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\common\models\UsersPays */

$this->title = Yii::t('admin', 'Добавить денежную транзакцию');
$this->params['breadcrumbs'][] = ['label' => 'Транзакции', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-pays-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'user_id' => $id,
    ]) ?>

</div>
