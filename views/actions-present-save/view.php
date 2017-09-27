<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ActionsPresentSave */

$this->title = $model->basket_id;
$this->params['breadcrumbs'][] = ['label' => 'Actions Present Saves', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actions-present-save-view">

    <h1>Корзина № <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php //= Html::a('Delete', ['delete', 'id' => $model->id], [            'class' => 'btn btn-danger',            'data' => [                'confirm' => 'Are you sure you want to delete this item?',                'method' => 'post',            ],        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
//            'user_id',
            'basket_id',
//            'present',
//            'card_number',
//            'create_date',
//            'update_date',
//          'bought_date',
            'status',
        ],
    ]) ?>

</div>
