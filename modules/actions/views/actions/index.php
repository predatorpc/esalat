<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\actions\models\ActionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('actions', 'Actions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actions-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('actions', 'Create Actions'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
  <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            [
                'attribute' => 'title',
                'format' => 'html',/*
                'value' => function($model){
                    return Html::a($model->title,'/catalog/actions/' . $model->id);
                }*/
            ],
            'description:ntext',
            [
                'attribute' => 'photo',
                'format' => 'html',
                'value' => function($model){
                    return $model->file_type ? Html::img(Yii::$app->params['actionsImagePath']. $model->id.'.'.$model->file_type,['width' => '75px']) : Yii::t('actions', 'Empty');
                }
            ],
            /*[
                'label' => 'period',
                'attribute' => 'date_start',
                'format' => 'html',
                'value' => function($model){
                    return date('Y-m-d',$model->date_start) . ' - ' . date('Y-m-d',$model->date_end);
                }
            ],*/
            [
                'attribute' => 'date_start',
                'format' => 'html',
                'value' => function($model){
                    return date('d.m.Y',$model->date_start);
                }
            ],
            [
                'attribute' => 'date_end',
                'format' => 'html',
                'value' => function($model){
                    return date('d.m.Y',$model->date_end);
                },
            ],
            [
                'attribute' => 'accumulation',
                'format' => 'html',
                'value' => function($model){
                    return $model->periodic ? '<span class="btn btn-success">Да</span>' : '<span class="btn btn-danger">Нет</span>';
                }
            ],
            [
                'attribute' => 'priority',
                'format' => 'html',
                'value' => function($model){
                    return $model->priority;
                }
            ],
            [
                'attribute' => 'block',
                'format' => 'html',
                'value' => function($model){
                    return $model->block ? '<span class="btn btn-success">Да</span>' : '<span class="btn btn-danger">Нет</span>';
                }
            ],
            // 'created_at',
//            [
//                'attribute' => 'updated_at',
//                'value' => function($model){
//                    return date('Y-m-d',$model->updated_at);
//                }
//            ],
            [
                'attribute' => 'created_user',
                'format' => 'html',
                'value' => function($model){
                    return $model->updatedUser->name;
                }
            ],
            [
                'attribute' => 'for_user_id',
                'label'=> 'Для пользователя',
                'format' => 'html',
                'value' => function($model){
                    $user = \app\modules\common\models\User::find()->select(['name','id'])->where(['id'=>$model->for_user_id])->asArray()->One();
                    return Html::a($user['name'],'/user/view?id=' . $user['id']);
                }
            ],
            [
                'attribute' => 'count_for_user',
                'label'=> 'Оставшиеся использования',
            ],
//            [
//                'attribute' => 'updated_user',
//                'format' => 'html',
//                'value' => function($model){
//                    return Html::a($model->updatedUser->name,'/user/view?id=' . $model->updatedUser->id);
//                }
//            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model){
                    return $model->status ? '<span class="btn btn-success">Да</span>' : '<span class="btn btn-danger">Нет</span>';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'headerOptions' => ['width' => '80'],
                'template' => '{update}{link}',
            ],
        ],
    ]); ?>
</div>
