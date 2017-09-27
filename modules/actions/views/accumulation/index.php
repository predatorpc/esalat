<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\common\models\User;
use app\modules\actions\models\Actions;
use app\modules\actions\models\ActionsParams;


/* @var $this yii\web\View */
/* @var $searchModel app\modules\actions\models\ActionsAccumulationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('actions', 'Action accumulation report');
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="actions-accumulation-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>

    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute'=>'user_id',
                'label'=> Yii::t('admin', 'Покупатель'),
                'content'=>function ($model, $key, $index, $column){
                    return $model->user->name;
                },
                'filter'=>ArrayHelper::map(User::find()->select('id, CONCAT_WS(" ", name, phone)  as `name`')->where(['status'=>1])->orderBy('name')->asArray()->all(), 'id', 'name'),
            ],
            [
                'attribute'=>'action_id',
                'label'=> Yii::t('admin', 'Название акции'),
                'content'=>function ($model, $key, $index, $column){
                    if(!empty($model->action)){
                        return $model->action->title;
                    }
                    else{
                        return Yii::t('admin', 'Не найдено');
                    }

                },
                'filter'=>ArrayHelper::map(Actions::find()->where(['status'=>1])->orderBy('title')->asArray()->all(), 'id', 'title'),
            ],
            [
                //'attribute'=>'action_param_value_id',
                'label'=> Yii::t('admin', 'Не найдено'),
                'content'=>function ($model, $key, $index, $column){
                    if(!empty($model->paramName)){
                        return Yii::t('actions', $model->paramName->title);
                    }
                    else{
                        return Yii::t('admin', 'Не найдено');
                    }
                },
                //'filter'=>ArrayHelper::map(ActionsParams::find()->where(['status'=>1])->orderBy('title')->asArray()->all(), 'id', 'title'),
            ],

            //'paramName.title',
            [
                //'attribute'=>'action_param_value_id',
                'label'=> Yii::t('admin', 'Сумма накоплений'),
                'content'=>function ($model, $key, $index, $column){
                     return $model->current_value;

                },
                //'filter'=>ArrayHelper::map(ActionsParams::find()->where(['status'=>1])->orderBy('title')->asArray()->all(), 'id', 'title'),
            ],
            [
                //'attribute'=>'action_param_value_id',
                'label'=> Yii::t('admin', 'Процент'),
                'content'=>function ($model, $key, $index, $column){
                    return round($model->current_value*100/$model->action->accum_value,2).'%';

                },
                //'filter'=>ArrayHelper::map(ActionsParams::find()->where(['status'=>1])->orderBy('title')->asArray()->all(), 'id', 'title'),
            ],
            [
                'label'=> Yii::t('admin', 'Статус'),
                'content'=>function ($model, $key, $index, $column){
                    if($model->count_row == $model->active_row){
                        return Yii::t('admin', 'Накоплено');
                    }
                    else{
                        return Yii::t('admin', 'Потрачено');
                    }
                },
                //'filter'=>ArrayHelper::map(ActionsParams::find()->where(['status'=>1])->orderBy('title')->asArray()->all(), 'id', 'title'),
            ],


            // 'currency_id',
            // 'action_id',
            // 'action_param_value_id',
            // 'active',
            // 'status',

            /*[
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'headerOptions' => ['width' => '20'],
                'template' => '{update}',
            ],*/
        ],
    ]); ?>
</div>
