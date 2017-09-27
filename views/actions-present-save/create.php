<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ActionsPresentSave */

$this->title = 'Create Actions Present Save';
$this->params['breadcrumbs'][] = ['label' => 'Actions Present Saves', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actions-present-save-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
