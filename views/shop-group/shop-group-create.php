<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Shops */

$this->title = Yii::t('admin', 'Добавить группу магазинов');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Группы магазинов'), 'url' => ['shop-groups']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shops-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_shop_group', [
        'model' => $model,
        'create' => true,
        'error' => '',
    ]) ?>

</div>
