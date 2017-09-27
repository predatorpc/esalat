<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\common\models\UsersLogsSearch $searchModel
 */

$this->title = Yii::t('admin', 'Отчет о добавленных товарах (пользователи)');
$this->params['breadcrumbs'][] = $this->title;

$items_action = [
    '1' => Yii::t('admin', 'Добавление'),
    '2' => Yii::t('admin', 'Обновление')
];
$count_var = 0;
$count_kart = 0;

//print_r($params['UsersLogsSearch']);

if (!empty($params['UsersLogsSearch']['dateStart']) && !empty($params['UsersLogsSearch']['dateEnd'])) {

    if (!empty($params['UsersLogsSearch']['user'])) {
        $count_var = \app\modules\common\models\UsersLogs::find()
            ->leftJoin('users', 'users.id = users_logs.user_id')
            ->where(['between', 'users_logs.created_at', Date('Y-m-d 00:00:00', strtotime($params['UsersLogsSearch']['dateStart']))
                , Date('Y-m-d 23:59:59', strtotime($params['UsersLogsSearch']['dateEnd']))
            ])
            ->andWhere(['LIKE', 'users.name', $params['UsersLogsSearch']['user']])
            ->andWhere('users_logs.type = 1')
            ->andWhere('users_logs.variations_id is not null')
            ->count();

        $count_kart = \app\modules\common\models\UsersLogs::find()
            ->leftJoin('users', 'users.id = users_logs.user_id')
            ->where(['between', 'users_logs.created_at', Date('Y-m-d 00:00:00', strtotime($params['UsersLogsSearch']['dateStart']))
                , Date('Y-m-d 23:59:59', strtotime($params['UsersLogsSearch']['dateEnd']))
            ])
            ->andWhere(['LIKE', 'users.name', $params['UsersLogsSearch']['user']])
            ->andWhere('users_logs.type = 1')
            ->andWhere('users_logs.variations_id is null')
            ->count();

    } else {
        $count_var = \app\modules\common\models\UsersLogs::find()->where(['between', 'created_at', Date('Y-m-d 00:00:00', strtotime($params['UsersLogsSearch']['dateStart']))
            , Date('Y-m-d 23:59:59', strtotime($params['UsersLogsSearch']['dateEnd']))
        ])
            ->andWhere('type = 1')
            ->andWhere('variations_id is not null')
            ->count();

        $count_kart = \app\modules\common\models\UsersLogs::find()
            ->where(['between', 'created_at', Date('Y-m-d 00:00:00', strtotime($params['UsersLogsSearch']['dateStart']))
                , Date('Y-m-d 23:59:59', strtotime($params['UsersLogsSearch']['dateEnd']))
            ])
            ->andWhere('type = 1')
            ->andWhere('variations_id is null')
            ->count();
    }


    //$count_kart = $count_var - $dataProvider->getCount();
}
//print_r($params['UsersLogsSearch']['type']);
//$count_var = \app\modules\catalog\models\GoodsVariations::find()->where('between','created_date', $salesReport->fromDate, $salesReport->toDate)->count();
//print_r($count_var);
//print_r($params);
?>

<style>
    div.content.cms a.btn {
        color: #0A6A8F;
    }
</style>


<div class="users-logs-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('admin', 'Create {modelClass}', [
    'modelClass' => 'Users Logs',
]), ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>


    <div class="calendar-fast">
        <a class="dashed"
           href="<?= Url::to(['reports/reports-users', 'UsersLogsSearch[dateStart]' => Date("Y-m-d"), 'UsersLogsSearch[dateEnd]' => Date("Y-m-d")]); ?>">Сегодня</a>|
        <a class="dashed"
           href="<?= Url::to(['reports/reports-users', 'UsersLogsSearch[dateStart]' => Date('Y-m-d', strtotime('-1 day')), 'UsersLogsSearch[dateEnd]' => Date('Y-m-d', strtotime('-1 day'))]); ?>">Вчера</a>|
        <a class="dashed"
           href="<?= Url::to(['reports/reports-users', 'UsersLogsSearch[dateStart]' => Date('Y-m-d', strtotime('-2 day')), 'UsersLogsSearch[dateEnd]' => Date('Y-m-d', strtotime('-2 day'))]); ?>">Позавчера</a>|
        <a class="dashed"
           href="<?= Url::to(['reports/reports-users', 'UsersLogsSearch[dateStart]' => Date('Y-m-d', strtotime('-1 week')), 'UsersLogsSearch[dateEnd]' => Date('Y-m-d')]); ?>">Прош.
            неделя</a>|
        <a class="dashed"
           href="<?= Url::to(['reports/reports-users', 'UsersLogsSearch[dateStart]' => Date('Y-m-d', strtotime('-1 month')), 'UsersLogsSearch[dateEnd]' => Date("Y-m-d")]); ?>">Прош.
            месяц</a>|
        <a class="dashed" href="/reports/reports-users"><b>Сбросить фильтр</b></a>
    </div>
    <br>

    <div class="row">
        <form METHOD="get">
            <div class="col-md-4">
                <?=
                DatePicker::widget([
                    'name'          => 'UsersLogsSearch[dateStart]',
                    'value'         => $params['UsersLogsSearch']['dateStart'],
                    'type'          => DatePicker::TYPE_RANGE,
                    'name2'         => 'UsersLogsSearch[dateEnd]',
                    'value2'        => $params['UsersLogsSearch']['dateEnd'],
                    'language'      => Yii::$app->language,
                    'separator'     => 'по',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format'    => 'yyyy-MM-dd',
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-2">
                <?= \yii\helpers\Html::submitButton('<i class="glyphicon glyphicon-transfer"></i> Сформировать', ['class' => 'btn btn-info']) ?>
            </div>
        </form>
    </div>
    <br><br>

    <?php //print_r($params['UsersLogsSearch']);

    //print_r($dataProvider->query->andWhere('goods_variations.full_name  = 1'));

    //$good_var = \app\modules\catalog\models\GoodsVariations::find()->select(['full_name'])->asArray()->One();
    //print_r($good_var);

    ?>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,

        'columns'     => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'user',
                'value'     => 'user.name',
                'label'     => 'ФИО',
                'content'   => function ($model) {
                    if (!empty($model->user->id)) {
                        return Html::a($model->user->name, '/user/view?id=' . $model->user->id, ['target' => '_blank']);
                    } else {
                        return '';
                    }
                },
            ],

            /*['attribute'      => 'id', 'label' => 'ФИО',
             'content'        => function ($data) {
                 if (!empty($data['user_id'])) {
                     $user = \app\modules\common\models\User::find()->where(
                         'id = ' . $data['user_id']
                     )->one();
                     if (!empty($user)) {
                         return Html::a($user->name, '/user/view?id=' . $data['user_id'], ['target' => '_blank']);
                     } else {
                         return '';
                     }
                 } else {
                     return '';
                 }
             },
             'contentOptions' => ['style' => 'width:20%; white-space: normal;']
            ],*/
            [
                'attribute' => 'good',
                'value'     => 'good.name',
                // 'width'=>'10%',
                'label'     => Yii::t('admin', 'Название товара'),
                'content'   => function ($model) {
                    //$good = \app\modules\catalog\models\Goods::find()->select(['name'])->where(['id' => $data->good_id])->asArray()->One();
                    return Html::a($model->good->name,
                        '/product/update?id=' . $model->good->id);
                },
            ],

            [
                'attribute' => 'variations',
                'value'     => 'variations.full_name',
                // 'width'=>'10%',
                'label'     => Yii::t('admin', 'Название вариации'),
                'content'   => function ($model) {
                    //$good_var = \app\modules\catalog\models\GoodsVariations::find()->select(['full_name'])->where(['id' => $data->variations_id])->asArray()->One();
                    if (!empty($model->variations['full_name'])) {
                        return $model->variations['full_name'];
                    } else {
                        return '<p style="color: #ff3b36;">Добавление карточки</p>';
                    }
                },
                'format'    => 'raw',

            ],

            ['attribute' => 'type', 'label' => 'Тип действия',
             'value'     => function ($model) {
                 if (!empty($model->type) && $model->type == 1) {
                     return '<p style="color: #0c910d;">Добавление</p>';
                 } elseif (!empty($model->type) && $model->type == 2) {
                     return 'Обновление';
                 }
             },

                //'filterType'          => GridView::FILTER_SELECT2,
                //'filter'              => $items_action,
                //'filterWidgetOptions' => [
                //    'pluginOptions' => ['allowClear' => true],
                //],
                //'filterInputOptions'  => ['placeholder' => Yii::t('admin', 'Активость')],

             'format'         => 'raw',
             'contentOptions' =>
                 ['style' => 'width:11%; white-space: normal;']
            ],

            ['attribute' => 'created_at', 'label' => 'Дата действия',
             'value'     => function ($model) {
                 return date('d.m.Y H:i', strtotime($model->created_at));
             }
            ],

//            ['attribute' => 'created_at', 'label' => 'Дата действия', 'format' => ['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
            //           ['attribute' => 'updated_at','format' => ['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
//            'status',


        ],
        'responsive'  => true,
        'hover'       => false,
        'condensed'   => true,
        'floatHeader' => false,

        'pjax'         => false,
/*        'pjaxSettings' => [
            'neverTimeout' => true,
        ],*/

        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type'    => 'info',
            'before'  => '<p><b>Добавлено вариаций: </b>' . $count_var . '</p>' . '<p><b>Добавлено карточек: </b>' . $count_kart . '</p>',

            'after'      => Html::a('<i class="glyphicon glyphicon-repeat"></i> Сбросить фильтр', Url::to(['reports/reports-users']), ['class' => 'btn btn-info']),
            'showFooter' => false
        ],

    ]);


    ?>


</div>