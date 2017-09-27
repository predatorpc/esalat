<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\catalog\tagssearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Свойства');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tags-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Добавить свойство +'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            //'group_id',
            [
                'attribute' => 'group_id',
                'label' => Yii::t('admin', 'Группа'),
                'content' => function($data){
                    $name = \app\modules\catalog\models\TagsGroups::find()->where(['id' => $data['group_id']])->one();
                    return $name->name;
                }

            ],
            [
                'attribute' => 'value',
                'label' => Yii::t('admin', 'Значение'),
            ],
//            'value',
//            'status',
            [
                'attribute' => 'tags',
                'label' => Yii::t('admin', 'Статус'),
                'content' => function($data){
                    if($data['status'] == 1)
                        return Yii::t('admin', 'Активная');
                    else
                        return Yii::t('admin', 'Не активная');

                }

            ],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
