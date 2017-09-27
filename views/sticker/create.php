<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\Sticker */

$this->title = 'Новый стикер';
$this->params['breadcrumbs'][] = ['label' => 'Стикеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sticker-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
