<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\widgets\DetailView;
use app\modules\common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\UsersPaysSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin','Управление ДЕНЕЖНЫМИ транзакциями');
$this->params['breadcrumbs'][] = $this->title;

$itemsTypeTrans = [
    '0' => Yii::t('admin','не указан'),
    '1' => Yii::t('admin','Пополнение счета (VISA)'),
    '2' => Yii::t('admin','Пополнение счета (ExtremeFitness)'),
    '3' => Yii::t('admin','Старые продажи'),
    '4' => Yii::t('admin','Оплата заказа на сайте'),
    '5' => Yii::t('admin','Отмена заказа'),
    '6' => Yii::t('admin','Комиссия за продажу товара'),
    '8' => Yii::t('admin','Старые продажи'),
    '9' => Yii::t('admin','Оплата доставки'),
    '10' => Yii::t('admin','Перевод с клиента на клиента'),
    '13' => Yii::t('admin','Оплата заказа через терминал'),
    '20' => Yii::t('admin','Зачисление наличными'),
    '21' => Yii::t('admin','Комиссия за доставку товара'),
    '22' => Yii::t('admin','Вывод средств'),
    '23' => Yii::t('admin','Зачисление средств'),
];
$itemsType = [
    '0' => Yii::t('admin','Автоматическая'),
    '1' => Yii::t('admin','Ручная'),
];
$itemsStatus = [
    '0' => Yii::t('admin','Не активная'),
    '1' => Yii::t('admin','Активная'),
];

?>
<div class="users-pays-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin','Добавить денежную транзакцию').' +', ['create-new'], ['class' => 'btn btn-success']) ?>
        <?php //= Html::a('Добавить бонусную транзакцию +', ['create-bonus'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php

    if($userFlag){

        foreach ($modelUser as $item) {

            //var_dump($item);die();

            echo DetailView::widget([
                'model' => $item,
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('admin','ID Пользователя'),
                    ],
                    [
                        'attribute' => 'phone',
                        'label' => Yii::t('admin','Телефон'),
                    ],
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('admin','Имя пользователя'),
                        'format' => 'raw',
                        'value' =>  Html::a($item->name, "/transactions/create?id=".$item->id),
                    ],
                    'email:email',
                    [
                        'attribute' => 'money',
                        'label' => Yii::t('admin','Сейчас на счету'),
                    ],
                    [
                        'attribute' => 'bonus',
                        'label' => Yii::t('admin','Текущие бонусы'),
                    ],
                    [
                        'attribute' => 'moneyCount',
                        'label' => Yii::t('admin','Пополнение баланса за все время'),
                    ],
                    [
                        'attribute' => 'moneySpend',
                        'label' => Yii::t('admin','Покупки за все время'),
                    ],
                    [
                        'attribute' => 'moneyDelivery',
                        'label' => Yii::t('admin','Оплата доставки'),
                    ],




                    //            [ 'filter' => $params1,],
                    //            'fullname:ntext',
                    //            'auth_key',
                    //           'password_hash',
                    //            'password_reset_token',
                    //            'status',
                    //            'created_at',
                    //            'updated_at',
                ],
            ]);
            echo "<hr>";
        }
     }

    ?>



    <?php
    $gridColumns = [
        [
            'class'=>'kartik\grid\SerialColumn',
            'width'=>'36px',
            'header'=>'',
        ],
        [
            'attribute' => 'user_id',
            'label' => Yii::t('admin','Клиент'),
            'value'=>function($model){
                return Html::a($model->users->name,"/transactions/create?id=".$model->users->id);
            },
            'format'=>'raw',
        ],
        [
            'attribute' => 'created_user_id',
            'label' => Yii::t('admin','ID Создателя'),
            'value' => function($model){
                if(!empty($model->creatorUser)){
                    return $model->creatorUser['name'];
                }
                else
                    return '';
            },
            'format' => 'raw',
        ],
        [
            'attribute' => 'order_id',
            'label' => Yii::t('admin','Номер Заказа'),
        ],
        /*[
            'attribute' => 'users',
            'label' => 'ФИО',
            'format' => 'raw',
            'value' => function($data, $value){
                $userName = User::find()->select('name')->where('id = '.$data->user_id)->asArray()->one();
                return Html::a($userName['name'], "/transactions/create?id=".$data->user_id);

            }
        ],*/
        [
            'attribute' => 'userPhone',
            'label' => Yii::t('admin','Телефон'),
            'value'=>function($model){
                return $model->users->phone;
            },
            'format'=>'raw',
        ],
        [
            'attribute' => 'type',
            'label' => Yii::t('admin','Тип'),
            'value' => function($data){
                switch (intval($data['type'])) {
                    case 0: return Yii::t('admin',"не указан");
                    case 1: return Yii::t('admin',"Пополнение счета (VISA)");
                    case 2: return Yii::t('admin',"Пополнение счета (ExtremeFitness)");
                    case 3: return Yii::t('admin',"Старые продажи");
                    case 4: return Yii::t('admin',"Оплата заказа на сайте");
                    case 5: return Yii::t('admin',"Отмена заказа");
                    case 6: return Yii::t('admin',"Комиссия за продажу товара");
                    case 8: return Yii::t('admin',"Старые продажи");
                    case 9: return Yii::t('admin',"Оплата доставки");
                    case 10: return Yii::t('admin',"Перевод с клиента на клиента");
                    case 13: return Yii::t('admin',"Оплата заказа через терминал");
                    case 20: return Yii::t('admin',"Зачисление наличными");
                    case 21: return Yii::t('admin',"Комиссия за доставку товара");
                    case 22: return Yii::t('admin',"Вывод средств");
                    case 23: return Yii::t('admin',"Зачисление средств");
                        break;
                }

            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$itemsTypeTrans,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>Yii::t('admin','Тип')],
            'format'=>'raw'
        ],
        [
            'attribute' => 'money',
            'label' => Yii::t('admin','Сумма'),
            'filter'=>false,
            'mergeHeader'=>true,
            'format'=>'raw',
        ],
        [
            'attribute' => 'comments',
            'label' => Yii::t('admin','Комментарий'),
            'format'=>'raw',
        ],
        [
            'attribute' => 'date',
            'label' => Yii::t('admin','Дата'),
            'filterType'=>GridView::FILTER_DATE ,
            'filterWidgetOptions'=>[
                'pluginOptions'=>[
                    'allowClear'=>true,
                    'autoclose' => true,
                ],
            ],
        ],
        [
            'attribute' => 'type_id',
            'label' => Yii::t('admin','Способ зачисления'),
            'value' => function($data){
                if(intval($data['type_id'])==1)
                    return '<p style="color: #00ff00;">Ручная</p>';
                else
                    return Yii::t('admin','Автоматическая');
            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$itemsType,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>'Тип'],
            'format' => 'html',
        ],
        [
            'attribute' => 'status',
            'label' => Yii::t('admin','Статус'),
            'value' => function($data){
                if($data['status'] == 1)
                    return Yii::t('admin','Активная');
                else
                    return '<p style="color: #ff0000;">'.Yii::t('admin','Не активная').'</p>';

            },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$itemsStatus,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>Yii::t('admin','Активность')],
            'format' => 'html',
            'format' => 'html',
        ],
    ];
    echo GridView::widget([
        'id' => 'kv-grid-demo',
        'dataProvider'=>$dataProvider,
        'filterModel'=>$searchModel,
        'columns'=>$gridColumns,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax'=>false, // pjax is set to always true for this demo
        // set your toolbar
        'toolbar'=> [
            '{export}',
            '{toggleData}',
        ],
        // set export properties
        'export'=>[
            'fontAwesome'=>true
        ],
        // parameters from the demo form
        'panel'=>[
            'type'=>GridView::TYPE_PRIMARY,
            'before'=>'{pager}',
        ],
        'persistResize'=>false,
        //'exportConfig'=>$exportConfig,
    ]);
    ?>
</div>
