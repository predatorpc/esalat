<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lists */

$this->title = Yii::t('admin','Редактировать список ') . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Списки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin','Редактировать');
?>
<div class="lists-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

    //var_dump($id);die();

    ?>

    <?= $this->render('_form', [
        'id' => $id,
        'model' => $model,
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
        'stringHash' => $stringHash,//$error,
    ]) ?>

</div>
