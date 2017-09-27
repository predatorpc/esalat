<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\common\models\UserSearchDefault2 $searchModel
 */

$this->title = Yii::t('admin', 'Отчет о реальных клиентах');
$this->params['breadcrumbs'][] = $this->title;

// Для статуса
$itemsStatus = [
    '0' => Yii::t('admin', 'Не активный'),
    '1' => Yii::t('admin', 'Активный'),
];


?>


<div class="user-index">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <form METHOD="get">
            <div class="col-md-4">
                <?=
                DatePicker::widget([
                    'name'          => 'UserSearchDefault2[dateStart]',
                    'value'         => $params['UserSearchDefault2']['dateStart'],
                    'type'          => DatePicker::TYPE_RANGE,
                    'name2'         => 'UserSearchDefault2[dateEnd]',
                    'value2'        => $params['UserSearchDefault2']['dateEnd'],
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

    <?php Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,

        'columns'     => [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'id', 'label' => 'ID', 'contentOptions' => ['style' => 'width:10%; white-space: normal;']],
            /*            'extremefitness',*/

            ['attribute'      => 'name', 'label' => 'ФИО',
             'content'        => function ($data) {
                 if (!empty($data['id'])) {
                     $user = \app\modules\common\models\User::find()->where(
                         'id = ' . $data['id']
                     )->one();
                     if (!empty($user)) {
                         return Html::a($user->name, '/user/view?id=' . $data['id'], ['target' => '_blank']);
                     } else {
                         return '';
                     }
                 } else {
                     return '';
                 }
             },
             'contentOptions' => ['style' => 'width:20%; white-space: normal;']
            ],

            ['attribute' => 'birthday', 'label' => 'Дата рождения',
             'value'     => function ($model) {
                 if (!empty($model->birthday)) {
                     return date('d.m.Y', strtotime($model->birthday));
                 } else {
                     return '<p style="color: #ff0000;">Не установлена</p>';
                 }

             },
             'format'    => 'raw'],
//            's',
            ['attribute' => 'phone', 'label' => 'Телефон'],
//            'secret_word',
//            'email:email',
//            'updated_at',
//            ['attribute' => 'created_at', 'label' => 'Дата регистрации'],
//            'password_reset_token',
//            'password_hash',
//            'auth_key',
//            'password',
//            'money',
//            'bonus',
//            'hash',
//            'staff',
//            'driver',
//            'manager',
//            'level',
//            'call',
//            'store_id',
//            'sms',
//           ['attribute' => 'enter','format' => ['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
            ['attribute' => 'registration', 'label' => 'Дата регистрации',
             'value'     => function ($model) {
                 return date('d.m.Y H:i', strtotime($model->registration));
             }
            ],
            ['attribute' => 'orderDate', 'label' => 'Дата последнего заказа',
             'value'     => function ($model) {
                 if (!empty($model->orderDate)) {
                     return date('d.m.Y H:i', strtotime($model->orderDate));
                 } else {
                     return '<p style="color: #ff0000;">Не установлена</p>';
                 }

             },
             'format'    => 'raw',
             'contentOptions' =>
                ['style' => 'width:11%; white-space: normal;']
            ],
            [
                'class'         => 'yii\grid\ActionColumn',
                'header'        => Yii::t('admin', 'Профайл'),
                'headerOptions' => ['width' => '30'],
                'template'      => '{view}',
                'buttons'       => [
                    'view' => function ($url, $model) {
                        return Html::a(Yii::t('admin', 'Открыть'), ['/reports/profile?id=' . $model->id], ['target' => '_blank']);
                    },
                ],
            ],
//            'confirm',
//            'agree',
//            'typeof',
//            'compliment',
            [
                'attribute'           => 'status',
                'label'               => Yii::t('admin', 'Статус'),
                'value'               => function ($data) {
                    if ($data['status'] == 1)
                        return '<p style="color: #0c910d;">' . Yii::t('admin', 'Активный') . '</p>';
                    else
                        return '<p style="color: #ff0000;">' . Yii::t('admin', 'Не активный') . '</p>';

                },
                'filterType'          => GridView::FILTER_SELECT2,
                'filter'              => $itemsStatus,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions'  => ['placeholder' => Yii::t('admin', 'Активность')],
                'format'              => 'html',
                'contentOptions'      => ['style' => 'min-width: 150px;']
            ],

            /*[
                'class'   => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['reports/view', 'id' => $model->id, 'edit' => 't']),
                            ['title' => Yii::t('yii', 'Edit'),]
                        );
                    }
                ],
            ],*/
        ],
        'responsive'  => true,
        'hover'       => false,
        'condensed'   => true,
        'floatHeader' => false,

        'panel' => [
            'heading'    => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type'       => 'info',
            'before'     => '<b>Наличие минимум одной покупки. НЕ сотрудник. Без промокода. Товар из ЭкстримФитнеса</b>',
            'after'      => Html::a('<i class="glyphicon glyphicon-repeat"></i> Сбросить фильтр', Url::to(['reports/real-clients-2']), ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]);
    Pjax::end(); ?>

</div>