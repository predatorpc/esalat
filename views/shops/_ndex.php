<<<<<<< HEAD
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\models\UserShop;
use app\modules\common\models\UserRoles;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('admin', 'Управление магазинами');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="shops-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить магазин +', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'phone',

            [
                'attribute'=>'name',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['name'], "/shops/update?id=".$url);
                },
            ],

            [
                'attribute'=>'name_full',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['name_full'], "/shops/update?id=".$url);
                },
            ],
             'comission_id',
             'comission_value',
            [
                'attribute'=>'show',
                'label' => 'Показ на сайте',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    if($data['show']==1) return Html::tag('p','Да');
                    if($data['show']==0) return Html::tag('p','Нет');
                },
            ],
             'registration',
            [
                'attribute'=>'status',
                'label' => 'Активный',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    if($data['status']==1) return Html::tag('p','Да');
                    if($data['status']==0) return Html::tag('p','Нет');
                },
            ],
            [
                'label' =>'Пользователь',
                'format' => 'raw',
                'content'=>function ($data, $model) {//} use ($form) {
                    $user = UserRoles::find()->select('user_id')
                        ->where(['shop_id' => $model])->one();
                    if ($user != null) {

                        $userInfo = UserShop::find()->select('name')->where(
                            ['id' => $user['user_id']]
                        )->asArray()->one();

                        return $userInfo['name'];
                    }
                    else
                        return '';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'headerOptions' => ['width' => '80'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-share"></span>',
                            '/shops/view?id='.$model->id);
                    },
                    'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            '/shops/update?id='.$model->id);
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
                    'delete' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-download-alt"></span>',
                            '/systems/core/goods_exports.php?shop_id='.$model->id);
                    },

                ],

            ],

        ],
    ]);
    ?>
</div>
=======
<?php

//use yii\helpers\Html;
//use yii\grid\GridView;
//use app\modules\common\models\UserShop;
//use app\modules\common\models\UserRoles;
//use yii\bootstrap\ActiveForm;
//use yii\helpers\ArrayHelper;

$this->title = 'Управление магазинами';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="shops-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить магазин +', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'phone',
            [
                'attribute'=>'name',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['name'], "/shops/update?id=".$url);
                },
            ],

            [
                'attribute'=>'name_full',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return Html::a($data['name_full'], "/shops/update?id=".$url);
                },
            ],
             'comission_id',
             'comission_value',
            [
                'attribute'=>'show',
                'label' => 'Показ на сайте',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    if($data['show']==1) return Html::tag('p','Да');
                    if($data['show']==0) return Html::tag('p','Нет');
                },
            ],
             'registration',
            [
                'attribute'=>'status',
                'label' => 'Активный',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    if($data['status']==1) return Html::tag('p','Да');
                    if($data['status']==0) return Html::tag('p','Нет');
                },
            ],
            [
                'label' =>'Пользователь',
                'format' => 'raw',
                'content'=>function ($data, $model) {//} use ($form) {
                    $user = UserRoles::find()->select('user_id')
                        ->where(['shop_id' => $model])->one();
                    if ($user != null) {

                        $userInfo = UserShop::find()->select('name')->where(
                            ['id' => $user['user_id']]
                        )->asArray()->one();

                        return $userInfo['name'];
                    }
                    else
                        return '';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'headerOptions' => ['width' => '80'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-share"></span>',
                            '/shops/view?id='.$model->id);
                    },
                    'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            '/shops/update?id='.$model->id);
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
                    'delete' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-download-alt"></span>',
                            '/systems/core/goods_exports.php?shop_id='.$model->id);
                    },

                ],

            ],

        ],
    ]);
    ?>
</div>
>>>>>>> 053495c556647b3b24062145fd37bd5e1e3d3870
