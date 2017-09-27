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
        <?= Html::a(Yii::t('admin', 'Добавить магазин +'), ['create'], ['class' => 'btn btn-primary']) ?>
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
                'label' => Yii::t('admin', 'Показ на сайте'),
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    if($data['show']==1) return Html::tag('p', Yii::t('admin', 'Да'));
                    if($data['show']==0) return Html::tag('p', Yii::t('admin', 'Нет'));
                },
            ],
             'registration',
            [
                'attribute'=>'status',
                'label' => Yii::t('admin', 'Активный'),
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    if($data['status']==1) return Html::tag('p', Yii::t('admin', 'Да'));
                    if($data['status']==0) return Html::tag('p', Yii::t('admin', 'Нет'));
                },
            ],
            [
                'label' => Yii::t('admin', 'Рассписание'),
                'format'=>'raw',
                'value' => function ($data) {
                    $stores = $data->stores;
                    if($stores){
                        foreach ($stores as $store){
                            if(count($store->timeTables)>0){
                                    return '<span style="cursor: default; color: #fff;background-color: #5cb85c;border-color: #4cae4c;display: inline-block;padding: 6px 12px; margin-bottom: 0; font-size: 14px;font-weight: normal;line-height: 1.42857143;text-align: center;white-space: nowrap; vertical-align: middle;">Есть</span>';
                            }
                        }
                    }
                    return '<a style="cursor: default; color: #fff;background-color: #d9534f;border-color: #d43f3a;display: inline-block;padding: 6px 12px; margin-bottom: 0; font-size: 14px;font-weight: normal;line-height: 1.42857143;text-align: center;white-space: nowrap; vertical-align: middle;">Нету</a>';
                },
            ],
            [
                'label' => Yii::t('admin', 'Пользователь'),
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
                'header'=> Yii::t('admin', 'Действия'),
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
