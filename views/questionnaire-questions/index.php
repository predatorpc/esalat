<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\questionnaire\models\QuestionnaireQuestionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Анкета: вопросы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="questionnaire-questions-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'question:ntext',
            'active',
            [
                    'header' => 'Количество ответов',
                    'value' => function($data){
                        return \app\modules\questionnaire\models\QuestionnaireAnswers::find()->where(['question_id' => $data->id])->count();
                    }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
