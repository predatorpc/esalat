<?php
use yii\grid\GridView;
?>
<?= GridView::widget([
    'dataProvider' => $orderProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'orderId',
            'label' => 'Номер заказа',
            'value' => function($data){
                return $data['orderId'];
            }
        ],
        [
            'attribute' => 'productName',
            'label' => 'Товар',
            'format' => 'html',
            'value' => function($data){
                return '<a href="/catalog/'.$data['productId'].'">'.$data['productName'] . ' ' . $data['tags'] . '</a>';
            }
        ],
        [
            'attribute' => 'productCount',
            'label' => 'Количество',
            'value' => function($data){
                return $data['productCount'];
            }
        ],
        [
            'attribute' => 'allMoney',
            'label' => 'Сумма',
            'value' => function($data){
                return $data['allMoney'] . ' р.';
            }
        ],
        'ordersStatus',
        'ordersStatus',//9137172874
        [
            'attribute' => 'ordersItemStatusId',
            'label' => 'Статус',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function($data) use ($statusList) {
                $htmlSelectStatus = $data['ordersStatusId'].' '.$data['orderItemStatus'].' '.$data['orderItemStatusId'].' '.$data['orderItemId'] . ' ';
                if($data['ordersStatusId'] == 1 && $data['orderItemStatus'] == 1 && isset($data['orderItemStatusId']) && !empty($data['orderItemStatusId'])){
                    $htmlSelectStatus .= '
                                <select class="seller_status" id="status_change_'.$data['orderItemId'].'" style="width: 140px;">
                                ';
                    if($data['orderItemStatusId'] == 1001){
                        $htmlSelectStatus .= '
                                        <option value="' . $data['orderItemStatusId'] . '" selected>' . $statusList[1001]->name . '</option>
                                        <option value="1002">' . $statusList[1002]->name . '</option>
                                        <option value="1003">' . $statusList[1003]->name . '</option>
                                        ';
                    }elseif($data['orderItemStatusId'] == 1002){
                        $htmlSelectStatus .= '
                                        <option value="' . $data['orderItemStatusId'] . '" selected>' . $statusList[1002]->name . '</option>
                                        ';
                    }elseif($data['orderItemStatusId'] == 1003){
                        $htmlSelectStatus .= '
                                        <option value="' . $data['orderItemStatusId'] . '" selected>' . $statusList[1003]->name . '</option>
                                        <option value="1005">' . $statusList[1005]->name . '</option>
                                        ';
                    }elseif($data['orderItemStatusId'] == 1005){
                        $htmlSelectStatus .= '
                                        <option value="' . $data['orderItemStatusId'] . '" selected>' . $statusList[1005]->name . '</option>
                                        ';
                    }elseif($data['orderItemStatusId'] == 1004){
                        $htmlSelectStatus .= '
                                        <option value="' . $data['orderItemStatusId'] . '" selected>' . $statusList[1004]->name . '</option>
                                        <option value="1005">' . $statusList[1005]->name . '</option>
                                        ';
                    }else{
                        $htmlSelectStatus .= '
                                        <option value="' . $data['orderItemStatusId'] . '" selected>' . $statusList[1007]->name . '</option>
                                        ';
                    }
                    $htmlSelectStatus .= '
                                </select>
                                ';
                }elseif($data['ordersStatusId'] == 0 || $data['orderItemStatus'] == 0){
                    $htmlSelectStatus .= '<span class="text-danger">Отменён</span>';
                }elseif(!isset($data['orderItemStatusId']) && empty($data['orderItemStatusId'])){
                    $htmlSelectStatus .= '<span class="text-warning">Новый заказ</span>';
                }
                return $htmlSelectStatus;

            }
        ],
        [
            'attribute' => 'orderDate',
            'label' => 'Дата заказа',
            'value' => function($data){
                return $data['orderDate'];
            }
        ],
        [
            'attribute' => 'deliveryDate',
            'label' => 'Дата доставки',
            'value' => function($data){
                return $data['deliveryDate'];
            }
        ],
        [
            'attribute' => 'userAddress',
            'label' => 'Адрес',
            'value' => function($data){
                return $data['deliveryName'] . ' ' . $data['userAddress'];
            }
        ],


        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>