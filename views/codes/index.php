<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\catalog\tagssearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Управление промо-кодами');

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tags-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Добавить код').' +', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('admin', 'Добавить тип кода').' +', ['add-type-promo'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',

            'type_id',
            [
                'attribute' => 'user_id',
                'label' => Yii::t('admin', 'Пользователь'),
                'content' => function($data)
                {
                    if(!empty($data['user_id'])) {
                        $user = \app\modules\common\models\User::find()->where(
                            'id = ' . $data['user_id']
                        )->one();
                        if (!empty($user)) {
                            return Html::a($user->name, '/codes/update?id='.$data['id']);
                        } else {
                            return '';
                        }
                    }
                    else{

                        return '';
                    }
                }
            ],


            'code',

            'count',
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
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=> Yii::t('admin', 'Действия'),
                'headerOptions' => ['width' => '80'],
                'template' => '{view} {update} ',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-share"></span>',
                            '/codes/view?id='.$model->id);
                    },
                    'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            '/codes/update?id='.$model->id);
                    },
                    /*  'delete' => function ($url,$model) use ($userId) {
                          if($userId == 10013181)
                              return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                  '/shops/shopsdelete?id='.$model->id,
                                  [
                                     // 'class' => 'btn btn-danger',
                                      'data' => [
                                          'confirm' => 'Точно удалить?',
                                          //'method' => 'get',
                                      ]
                                  ]);
                          else
                              return Html::a('');
                      },*/
//                    'delete' => function ($url,$model) {
//                        return Html::a('<span class="glyphicon glyphicon-download-alt"></span>',
//                            '/systems/core/goods_exports.php?shop_id='.$model->id);
//                    },

                ],

            ],

         //   ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
