<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UsersCredits */


$user = \app\modules\common\models\User::find()->where('id = '.$model->user_id)->one()->name;

$this->title = $user;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users Credits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-credits-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Назад'), ['/users-credits/index'], ['class' => 'btn btn-primary']) ?>
  
              <?= Html::a(Yii::t('admin', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('admin', 'Уверены?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [ 'attribute' => 'user_id',
             'value' => $user,
             'label' => Yii::t('admin','ФИО')],
            ['attribute' =>'amount',
            'label' => Yii::t('admin','Сумма'),
            ],
            'status',
        ],
    ]) ?>

</div>
