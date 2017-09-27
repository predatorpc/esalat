<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\helpers\Html;
//use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\shop\models\OrdersStatus;
use app\modules\basket\models\Basket;
use app\modules\common\models\UserShop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчёт по заказам';
$this->params['breadcrumbs'][] = $this->title;

$ordersList = [];
GLOBAL $dataProviderOrderIdsBig;
$dataProviderOrderIdsBig = $dataProviderOrderIds;
?>

<div class="shops-order-report">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <form method="GET" action="" id="dateFilterForm">
        <label style="vertical-align: top;">
            <input
                class="form-control"
                type="radio"
                name="orders-provider-confirm"
                value="all"
                style="display: inline-block;width:15px;margin-right: 15px;"
                <?=(!isset($filterOredrs['orders-provider-confirm']) || empty($filterOredrs['orders-provider-confirm']) || $filterOredrs['orders-provider-confirm'] == 'all')?' checked':''?>
            ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Все</span>
        </label>
        <label style="vertical-align: top;">
            <input
                class="form-control"
                type="radio"
                name="orders-provider-confirm"
                value="noconfirm"
                style="display: inline-block;width:15px;margin-right: 15px;"
                <?=(!empty($filterOredrs['orders-provider-confirm']) && $filterOredrs['orders-provider-confirm'] == 'noconfirm')?' checked':''?>
            ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Необработанные заказы</span>
        </label>
        <label style="vertical-align: top;">
            <input
                class="form-control"
                type="radio"
                name="orders-provider-confirm"
                style="display: inline-block;width:15px;margin-right: 15px;"
                value="overdue"
                <?=(!empty($filterOredrs['orders-provider-confirm']) && $filterOredrs['orders-provider-confirm'] == 'overdue')?' checked':''?>
            ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Просроченные заказы</span>
        </label>
        <br />
        <label style="vertical-align: top;">Дата
            <select name="orders-provider-date-variant" class="form-control">
                <option value="delivery"<?=(($filterOredrs['orders-provider-date-variant'] == 'delivery') ? 'selected' : '')?>>Доставки</option>
                <option value="order"<?=(($filterOredrs['orders-provider-date-variant'] == 'order') ? 'selected' : '')?>>Заказа</option>
            </select>
        </label>
        <label style="vertical-align: top;">От
            <input
                class="form-control"
                id="personalShopDateStart"
                type="date"
                name="orders-provider-date-start"
                value="<?=(!empty($filterOredrs['orders-provider-date-start'])?$filterOredrs['orders-provider-date-start']:'')?>"
            >
        </label>
        <label style="vertical-align: top;">До
            <input
                class="form-control"
                id="personalShopDateStop"
                type="date"
                name="orders-provider-date-stop"
                value="<?=(!empty($filterOredrs['orders-provider-date-stop'])?$filterOredrs['orders-provider-date-stop']:date('Y-m-d'))?>"
            >
        </label>
        <br />
        <label style="vertical-align: top;">Cостояние заказа
            <select name="orders-provider-status" class="form-control">
                <option value="all">Все</option>
                <option value="disable"<?=(($filterOredrs['orders-provider-status'] == 'disable') ? 'selected' : '')?>>Отменён</option>
                <?php
                $statusListFilter = OrdersStatus::find()->where(['type' => 2])/*->andWhere(['<>','id',1007])*/->andWhere(['status' => 1])->indexBy('id')->all();
                foreach ($statusListFilter as $key => $status) {
                    print '
                        <option value="' . $status->id . '" ' . (($status->id == $filterOredrs['orders-provider-status']) ? 'selected' : '') . '>' . $status->name . '</option>
                        ';
                }
                ?>
            </select>
        </label>
        <?php
        if($shopId == 10000130 || $shopId == 10000154){
            ?>

            <label style="vertical-align: top;">Клуб
                <select name="orders-provider-club" class="form-control">
                    <option value="all">Все</option>
                    <option
                        value="home" <?= (('home' == $filterOredrs['orders-provider-club']) ? 'selected' : '') ?>>
                        Прочее
                    </option>
                    <?php
                    $extremeAddress = \app\modules\basket\models\BasketLg::getClubDelivery();

                    foreach ($extremeAddress as $key => $club) {
                        print '
                        <option value="' . $club['value'] . '" ' . (($club['value'] == $filterOredrs['orders-provider-club']) ? 'selected' : '') . '>' . $club['address'] . '</option>
                        ';
                    }
                    ?>
                </select>
            </label>
            <?php
        }
        ?>
        <br />
        <br />
        <button type="submit" name="setFilter" value="1" class="btn btn-primary" />Показать</button>
        &nbsp;
        <button type="submit" name="delFilter" value="1" class="btn btn-primary" />Сбросить фильтр</button>
    </form>

    <?php
    if(!empty($dataProvider)) {
        echo "<hr>\n".ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'orderId',
                        'label' => 'Номер заказа',
                        'value' => function ($data) {
                            return $data['orderId'];
                        }
                    ],
                    [
                        'label' => 'Склад',
                        'value' => function ($data) {
                            return $data['store_id'];
                        }
                    ],
                    [
                        'attribute' => 'productName',
                        'label' => 'Товар',
                        'format' => 'html',
                        'value' => function ($data) {
                            return '<a href="/catalog/' . $data['productId'] . '">' . $data['productName'] . ' ' . $data['tags'] . '</a>';
                        }
                    ],
                [
                    'attribute' => 'productCount',
                    'label' => 'Количество',
                    'format' => 'html',
                    'value' => function($data){
                        $address = \app\modules\common\models\Address::find()
                            ->leftJoin('shops_stores','shops_stores.address_id = address.id')
                            ->where(['shops_stores.id' => $data['store_id']])
                            ->one();
                        return $data['productCount'];/*. '<br /><br />' . $address->street . ', ' . $address->house;*/
                    }
                ],                    
/*                    [
                        'attribute' => 'productCount',
                        'label' => 'Количество',
                        'value' => function ($data) {
                            if($data['']))
                                    return $data['productCount'];
                        }
                    ],
*/                    [
                        'attribute' => 'allMoney',
                        'label' => 'Сумма',
                        'value' => function ($data) {
                            return $data['allMoney'] . ' р.';
                        }
                    ],
                    //'ordersStatus',//9137172874
                    [
                        'attribute' => 'ordersItemStatusId',
                        'label' => 'Статус',
                        'format' => 'raw',
                        //'contentOptions' => ['class' => 'text-center'],
                        //'headerOptions' => ['class' => 'text-center'],
                        'content' => function ($data) use ($statusList) {
                            //$htmlSelectStatus = $data['ordersStatusId'].' '.$data['orderItemStatus'].' '.$data['orderItemStatusId'].' '.$data['orderItemId'] . ' ';
                            $htmlSelectStatus = '';
                            if ($data['ordersStatusId'] == 1 && $data['orderItemStatus'] == 1 && isset($data['orderItemStatusId']) && !empty($data['orderItemStatusId'])) {
                                $htmlSelectStatus .= '<span class="text-warning">' . $statusList[$data['orderItemStatusId']]->name . '</span>';
                            } elseif ($data['ordersStatusId'] == 0 || $data['orderItemStatus'] == 0) {
                                $htmlSelectStatus .= '<span class="text-danger">Отменён</span>';
                            } elseif (!isset($data['orderItemStatusId']) && empty($data['orderItemStatusId'])) {
                                $htmlSelectStatus .= '<span class="bg-success">Новый заказ</span><br /><br />';
                                $htmlSelectStatus .= '
                    <select class="seller_status" id="status_change_' . $data['orderItemId'] . '" style="width: 140px;">
                        <option value="empty">---</option>
                        <option value="1001">' . $statusList[1001]->name . '</option>
                        <option value="1002">' . $statusList[1002]->name . '</option>
                    </select>
                    <br /><br />
                    <div style="text-align: center;">
                        <div data-id="' . $data['orderItemId'] . '" data-order-item="' . $data['orderItemId'] . '" class="re button-save-status-order button_ok" data-action="save-status">Сохранить</div>
                    </div>
                    ';
                            }
                            return $htmlSelectStatus;

                        },
                        'filter' => ArrayHelper::map(OrdersStatus::find()->where(['type' => 2])/*->andWhere(['<>','id',1007])*/
                        ->andWhere(['status' => 1])->indexBy('id')->all(), 'id', 'name'),
                    ],
                    [
                        'attribute' => 'orderDate',
                        'label' => 'Дата заказа',
                        'value' => function ($data) {
                            return $data['orderDate'];
                        },
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'deliveryDate',
                        'label' => 'Дата доставки',
                        'value' => function ($data) {
                            return $data['deliveryDate'];
                        },
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'userAddress',
                        'label' => 'Адрес',
                        'value' => function ($data) {
                            return $data['deliveryName'] . ' ' . $data['userAddress'];
                        },
                        'filter' => array("1" => "Активно", "2" => "Не активно"),
                    ],
                    [
                        'attribute' => 'commentShop',
                        'label' => 'Комментарий',
                        'format' => 'raw',
                        'value' => function ($data) {
                            //$data['commentShop']
                            $comment = '
                <a aria-label="Комментарий" title="Комментарий" href="" class="order-report-comment-control">
                    <span class="glyphicon glyphicon-eye-close"></span>
                </a>
                <div class="order-report-comment-text">' . $data['commentShop'] . '</div>
                <div class="not-visible order-report-comment-popup">
                    <textarea rows="7" style="width:100%;text-align:left;">' . $data['commentShop'] . '</textarea>
                    <input type="submit" value="Сохранить" class="button-save-comment-order" data-order-item="' . $data['orderItemId'] . '">
                </div>
                ';
                            return $comment;
                        }
                    ],
                ],
                //'export' => false,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_CSV => false,
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Экспорт',
                    'class' => 'btn btn-default'
                ]
            ]) . "<hr>\n";
        print GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'responsiveWrap'=> false,
            'rowOptions' => function ($model, $index, $widget, $grid){
                GLOBAL $dataProviderOrderIdsBig;
                if(isset($dataProviderOrderIdsBig[$model['orderId']])){
                    unset($dataProviderOrderIdsBig[$model['orderId']]);
                    return ['style'=>'border-top: 20px double rgb(12,124,168);'];
                }
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'ordersUserId',
                    'label' => '',
                    'value'=>function ($data, $model) use ($shopId) {
                        if($shopId == 10000145 || $shopId == 10000154){
                            $user = UserShop::find()
                                ->select('id, name, phone, level')
                                ->where(['id' => $data['ordersUserId']])
                                ->one();
                            if ($user['level'] > 0) {
                                return 'Сотрудник ('.$user['id'].', '.$user['name'].', '.$user['phone'].','.$user['level'].')';
                            } else {
                                return '-';
                            }
                        }
                        else {
                            return ' ';
                        }
                    }
                ],

                [
                    'attribute' => 'orderId',
                    'label' => 'Номер заказа',
                    'value' => function($data){
                        GLOBAL $dataProviderOrderIdsBig;
                        if(isset($dataProviderOrderIdsBig[$data['orderId']])){
                            return $data['orderId'];
                        }
                        return '';
                    }
                ],
                [
                    'attribute' => 'productName',
                    'label' => 'Товар',
                    'format' => 'html',
                    'value' => function($data){

                        return '<a href="'.app\modules\catalog\models\Goods::getPath($data['productId']).'">'.$data['productName'] . ' ' . $data['tags'] . '</a>';
                    }
                ],
                [
                    'attribute' => 'store_id',
                    'label' => 'Склад',
                    'value' => function($data){
                        $ad =  \app\modules\common\models\Address::find()->leftJoin('shops_stores','shops_stores.address_id = address.id')->where(['shops_stores.id' => $data['store_id']])->one();
                        return $ad->street . ', ' . $ad->house;
                    }
                ],
                [
                    'attribute' => 'productCount',
                    'label' => 'Количество',
                    'format' => 'html',
                    'value' => function($data){
                        $address = \app\modules\common\models\Address::find()
                            ->leftJoin('shops_stores','shops_stores.address_id = address.id')
                            ->where(['shops_stores.id' => $data['store_id']])
                            ->one();
                        return $data['productCount'];/*. '<br /><br />' . $address->street . ', ' . $address->house;*/
                    }
                ],
                [
                    'attribute' => 'allMoney',
                    'label' => 'Сумма',
                    'value' => function($data){
                        return $data['allMoney'] . ' р.';
                    }
                ],
                //'ordersStatus',//9137172874
                [
                    'attribute' => 'ordersItemStatusId',
                    'label' => 'Статус',
                    'format' => 'raw',
                    //'contentOptions' => ['class' => 'text-center'],
                    //'headerOptions' => ['class' => 'text-center'],
                    'content' => function($data) use ($statusList) {
                        //$htmlSelectStatus = $data['ordersStatusId'].' '.$data['orderItemStatus'].' '.$data['orderItemStatusId'].' '.$data['orderItemId'] . ' ';
                        $htmlSelectStatus = '';
                        if($data['ordersStatusId'] == 1 && $data['orderItemStatus'] == 1 && isset($data['orderItemStatusId']) && !empty($data['orderItemStatusId'])){
                            $htmlSelectStatus .= '<span class="text-warning">' . $statusList[$data['orderItemStatusId']]->name . '</span>';
                        }elseif($data['ordersStatusId'] == 0 || $data['orderItemStatus'] == 0){
                            $htmlSelectStatus .= '<span class="text-danger">Отменён</span>';
                        }elseif(!isset($data['orderItemStatusId']) && empty($data['orderItemStatusId'])){
                            $htmlSelectStatus .= '<span class="bg-success">Новый заказ</span><br /><br />';
                            $htmlSelectStatus .= '
                            <select class="seller_status" id="status_change_'.$data['orderItemId'].'" style="width: 140px;">
                                <option value="1001">' . $statusList[1001]->name . '</option>
                                <option value="1002">' . $statusList[1002]->name . '</option>
                            </select>
                            <br /><br />
                            <div style="text-align: center;">
                                <div data-id="'.$data['orderItemId'].'" data-order-item="'.$data['orderItemId'].'" class="button_blue button-save-status-order button_ok" data-action="save-status"><div>Сохранить</div></div>
                            </div>
                            ';
                        }
                        return $htmlSelectStatus;

                    },
                    'filter' => ArrayHelper::map(OrdersStatus::find()->where(['type' => 2])/*->andWhere(['<>','id',1007])*/->andWhere(['status' => 1])->indexBy('id')->all(), 'id', 'name'),
                ],
                [
                    'attribute' => 'orderDate',
                    'label' => 'Дата заказа',
                    'value' => function($data){
                        return $data['orderDate'];
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'deliveryDate',
                    'label' => 'Дата доставки',
                    'value' => function($data){
                        return $data['deliveryDate'];
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'userAddress',
                    'label' => 'Адрес',
                    'value' => function($data){
                        return $data['deliveryName'] . ' ' . $data['userAddress'];
                    },
                    'filter'=>array("1"=>"Активно","2"=>"Не активно"),
                ],
                [
                    'attribute' => 'commentShop',
                    'label' => 'Комментарий',
                    'format' => 'raw',
                    'value' => function($data){
                        //$data['commentShop']
                        $comment = '
                        <a aria-label="Комментарий" title="Комментарий" href="" class="order-report-comment-control">
                            <span class="glyphicon glyphicon-eye-close"></span>
                        </a>
                        <div class="order-report-comment-text">'.$data['commentShop'].'</div>
                        <div class="not-visible order-report-comment-popup">
                            <textarea rows="7" style="width:100%;text-align:left;">'.$data['commentShop'].'</textarea>
                            <input type="submit" value="Сохранить" class="button-save-comment-order" data-order-item="'.$data['orderItemId'].'">
                        </div>
                        ';
                        return $comment;
                    }
                ],
                [
                    'attribute' => 'comments',
                    'label' => 'Комментарий клиента',
                    //'value' =''
                ],
                /*[
                    'label' => 'Действия',
                    'format' => 'raw',
                    'value' => function($data){
                        $responce = '';
                        if($data['orderItemStatus'] == 1 && $data['ordersStatusId'] == 1 && isset($data['orderItemStatusId']) && !empty($data['orderItemStatusId'])){
                            $responce .= '
                            <div style="text-align: center;">
                                <div data-id="'.$data['orderItemId'].'" data-order-item="'.$data['orderItemId'].'" class="button_ok" data-action="save">Сохранить</div>
                            </div>
                            ';
                        }elseif($data['orderItemStatus'] == 0 || $data['ordersStatusId'] == 0){

                        }elseif(!isset($data['orderItemStatusId']) || empty($data['orderItemStatusId'])){
                            $responce .= '
                            <div style="text-align: center;">
                                <div data-id="'.$data['orderItemId'].'" data-order-item="'.$data['orderItemId'].'" class="button_ok" data-action="accept">Принять</div>
                            </div>
                            ';
                        }

                        return $responce;
                    }
                ],*/


                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
    }
    ?>

</div>
