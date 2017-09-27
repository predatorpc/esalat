<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\questionnaire\models\QuestionnaireAnswers */

$this->title = 'Create Questionnaire Answers';
$this->params['breadcrumbs'][] = ['label' => 'Questionnaire Answers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="questionnaire-answers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
