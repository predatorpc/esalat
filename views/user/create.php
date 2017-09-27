<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Useradmin */

$this->title = Yii::t('admin', 'Добавить пользователя');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Администрирование пользователей'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="useradmin-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_create', [
        'model' => $model,
    ]) ?>

</div>
