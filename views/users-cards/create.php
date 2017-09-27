<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\common\models\UsersCards */

$this->title = 'Create Users Cards';
$this->params['breadcrumbs'][] = ['label' => 'Users Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-cards-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
