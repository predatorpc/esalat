<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use app\modules\common\models\User;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\UsersPaysSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('admin', 'Управление БОНУСНЫМИ транзакциями');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-pays-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <p>
        <?php //= Html::a('Добавить денежную транзакцию +', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('admin', 'Добавить бонусную транзакцию +'), ['create-bonus'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    if($userFlag){
        foreach ($modelUser as $item) {
            echo DetailView::widget([
                'model' => $item,
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('admin', 'ID Пользователя'),
                    ],
                    [
                        'attribute' => 'phone',
                        'label' => Yii::t('admin', 'Телефон'),
                    ],
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('admin', 'Имя пользователя'),
                    ],
                    'email:email',
                    [
                        'attribute' => 'money',
                        'label' => Yii::t('admin', 'Сейчас на счету'),
                    ],
                    [
                        'attribute' => 'bonus',
                        'label' => Yii::t('admin', 'Текущие бонусы'),
                    ],
                    [
                        'attribute' => 'moneyCount',
                        'label' => Yii::t('admin', 'Пополнение баланса за все время'),
                    ],
                    [
                        'attribute' => 'moneySpend',
                        'label' => Yii::t('admin', 'Пополнение баланса за все время'),
                    ],
                    [
                        'attribute' => 'moneyDelivery',
                        'label' => Yii::t('admin', 'Оплата доставки'),
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



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            //            'user_id',
            //            [
            //                'attribute' => 'user_id',
            //                'label' => 'ID',
            //            ],
            [
                'attribute' => 'created_user_id',
                'label' => Yii::t('admin', 'ID Создателя'),
                'format' => 'raw',
                'content' => function($data, $value){
                    if(!empty($data->created_user_id)){
                        $userName = User::find()->select('name')->where('id = '.$data->created_user_id)->asArray()->one();
                        return $userName['name'];
                    }
                    else
                        return '';
                }
            ],
            [
                'attribute' => 'order_id',
                'label' => Yii::t('admin', 'ID Заказа'),
            ],
            [
                'attribute' => 'users',
                'label' => Yii::t('admin', 'ФИО'),
                'value' => 'users.name',
            ],
            [
                'attribute' => 'userPhone',
                'label' => Yii::t('admin', 'Телефон'),
                'value' => 'userPhone.phone',
            ],
            //            [
            //                'label' => 'UserName',
            //                'content' => function($data) use ($userModel){
            //                    foreach($userModel as $item)
            //                        if($item['id'] == $data['user_id'])
            //                            return $item['phone'];
            //                }
            //
            //            ],
            //            'order_id',
            //   'type',
            [
                'attribute' => 'type',
                'label' => Yii::t('admin', 'Тип'),
                'content' => function($data){
                    switch (intval($data['type'])) {
                        case 0: return Yii::t('admin', "не указан");
                        case 1: return Yii::t('admin', "Пополнение счета (VISA)");
                        case 2: return Yii::t('admin', "Пополнение счета (ExtremeFitness)");
                        case 3: return Yii::t('admin', "Старые продажи");
                        case 4: return Yii::t('admin', "Оплата заказа на сайте");
                        case 5: return Yii::t('admin', "Отмена заказа");
                        case 6: return Yii::t('admin', "Комиссия за продажу товара");
                        case 8: return Yii::t('admin', "Старые продажи");
                        case 9: return Yii::t('admin', "Отмена заказа, списание зачисленых бонусов");
                        case 10: return Yii::t('admin', "Перевод с клиента на клиента");
                        case 13: return Yii::t('admin', "Оплата заказа через терминал");
                        case 20: return Yii::t('admin', "Зачисление наличными");
                        case 21: return Yii::t('admin', "Комиссия за доставку товара");
                        //case 21: return Yii::t('admin', "Вывод средств");
                            break;
                    }
                }
            ],
            'bonus',
            'comments',
            'date',
            // 'status',
            [
                'attribute' => 'type_id',
                'label' => Yii::t('admin', 'Способ зачисления'),
                'content' => function($data){
                    if(intval($data['type_id'])==1)
                        return '<p style="color: #00ff00;">'.Yii::t('admin','Ручная').'</p>';
                    else
                        return Yii::t('admin', 'Автоматическая');
                }
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('admin', 'Статус'),
                'content' => function($data){
                    if($data['status'] == 1)
                        return Yii::t('admin', 'Активная');
                    else
                        return '<p style="color: #ff0000;">'.Yii::t('admin', 'Не активная').'</p>';
                }
            ],
        ],
    ]); ?>
</div>