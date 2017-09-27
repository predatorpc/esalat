<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\questionnaire\models\QuestionnaireQuestions */

$this->title = 'Обновить вопрос: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Questionnaire Questions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="questionnaire-questions-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
