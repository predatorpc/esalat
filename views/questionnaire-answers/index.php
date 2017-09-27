<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\questionnaire\models\QuestionnaireAnswersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Анкета: ответы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="questionnaire-answers-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <?php /* <p>
        <?= Html::a('Create Questionnaire Answers', ['create'], ['class' => 'btn btn-success']) ?>
    </p> */?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'question_id',
                'value' => function($data){
                    $question = \app\modules\questionnaire\models\QuestionnaireQuestions::find()->where(['id'=>$data['question_id']])->One();
                    if($question != NULL){
                        return $question->question;
                    }else{
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'user_id',
                'value' => function($data){
                    $user = \app\modules\questionnaire\models\Users::find()->where(['id'=>$data['user_id']])->One();
                    if($user != NULL){
                        return $user->name;
                    }else{
                        return '';
                    }
                }
            ],
            'answer:ntext',
            'date',
            // 'viewed',
            // 'basket_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
