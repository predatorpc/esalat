<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CategoryLinks */

$this->title = 'Create Category Links';
$this->params['breadcrumbs'][] = ['label' => 'Category Links', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-links-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
