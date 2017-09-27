<?php

namespace app\modules\shop\models;

use app\modules\basket\models\BasketLg;
use app\modules\catalog\models\GoodsVariations;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\models\UserShop;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsStores;
use app\modules\catalog\models\Goods;
use app\modules\common\models\Deliveries;
use app\modules\common\models\Address;
use app\modules\common\models\UserRoles;

/**
 * OrdersItemsSearch represents the model behind the search form about `app\models\OrdersItems`.
 */
class OrdersItemsSearch extends OrdersItems
{
    public $orderId;
    public $productName;
    public $orderDate;
    public $deliveryDate;
    public $orderItemStatusId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_group_id', 'good_id', 'variation_id', 'count', 'bonus', 'seller_status_id_', 'status_id', 'status'], 'integer'],
            [['orderItemStatusId','deliveryDate','orderDate','orderId','productName','time', 'comments', 'comments_shop', 'comments_call_center', 'receive', 'user_name', 'release'], 'safe'],
            [['comission', 'price', 'discount', 'fee'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OrdersItems::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_group_id' => $this->order_group_id,
            'good_id' => $this->good_id,
            'variation_id' => $this->variation_id,
            'time' => $this->time,
            'comission' => $this->comission,
            'price' => $this->price,
            'discount' => $this->discount,
            'count' => $this->count,
            'fee' => $this->fee,
            'bonus' => $this->bonus,
            'seller_status_id_' => $this->seller_status_id_,
            'receive' => $this->receive,
            'release' => $this->release,
            'status_id' => $this->status_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'comments_shop', $this->comments_shop])
            ->andFilterWhere(['like', 'comments_call_center', $this->comments_call_center])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }

    public function searchOrdersForProviderAll(){
        $shopId = UserShop::getIdentityShop();

        $query = new Query();
        $query->from ([Orders::tableName()])
            ->select([
                "orders.id",
            ])
            ->leftJoin( OrdersGroups::tableName(),'orders.id = orders_groups.order_id')
            ->leftJoin( OrdersItems::tableName(),'orders_items.order_group_id = orders_groups.id')
            ->leftJoin( Goods::tableName(),'goods.id = orders_items.good_id')
            ->leftJoin( Shops::tableName(),'shops.id = goods.shop_id')
            ->where(['IN', 'shops.id', [$shopId]])
            ->andWhere(['orders.status' => 1])
            ->andWhere(['orders.type' => 1])
            ->groupBy('orders.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    public function searchOrdersForProviderOverdue(){
        $shopId = UserShop::getIdentityShop();

        $query = new Query();
        $query->from ([Orders::tableName()])
            ->select([
                "orders.id",
            ])
            ->leftJoin( OrdersGroups::tableName(),'orders.id = orders_groups.order_id')
            ->leftJoin( OrdersItems::tableName(),'orders_items.order_group_id = orders_groups.id')
            ->leftJoin( Goods::tableName(),'goods.id = orders_items.good_id')
            ->leftJoin( Shops::tableName(),'shops.id = goods.shop_id')
            ->where(['IN', 'shops.id', [$shopId]])
            ->andWhere(['orders.status' => 1])
            ->andWhere(['orders.type' => 1])
            ->andWhere(['IN','orders_items.status_id',[1001,1002,1003,1004,1005]])
            ->andWhere(['<','orders_groups.delivery_date',date('Y-m-d h:i:s')])
            ->groupBy('orders.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    public function searchOrdersForProviderNoConfirm(){
        $shopId = UserShop::getIdentityShop();

        $query = new Query();
        $query->from ([Orders::tableName()])
            ->select([
                "orders.id",
            ])
            ->leftJoin( OrdersGroups::tableName(),'orders.id = orders_groups.order_id')
            ->leftJoin( OrdersItems::tableName(),'orders_items.order_group_id = orders_groups.id')
            ->leftJoin( Goods::tableName(),'goods.id = orders_items.good_id')
            ->leftJoin( Shops::tableName(),'shops.id = goods.shop_id')
            ->where(['IN', 'shops.id', [$shopId]])
            ->andWhere(['orders.status' => 1])
            ->andWhere(['orders.type' => 1])
            ->andWhere(['orders_items.status_id' => NULL])
            ->groupBy('orders.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    public function searchOrdersForProvider($params){
        //print '<pre>';print_r($params);print '</pre>';
        $shopId = UserShop::getIdentityShop();

        $querySearchOrders = new Query();
        $querySearchOrders->from ([Orders::tableName()])
            ->select(['orders.id as id'])
            ->leftJoin( OrdersGroups::tableName(),'orders.id = orders_groups.order_id')
            ->leftJoin( OrdersItems::tableName(),'orders_items.order_group_id = orders_groups.id')
            ->leftJoin( Goods::tableName(),'goods.id = orders_items.good_id')
            ->leftJoin( Shops::tableName(),'shops.id = goods.shop_id')

            ->where(['IN', 'shops.id', [$shopId]])
            ->andWhere(['orders.status' => 1])
            ->andWhere(['orders.type' => 1])
            ->orderBy(['orders.id' => SORT_DESC])
            ->groupBy('orders.id');


        $statusListBase = OrdersStatus::find()->where(['type' => 2])/*->andWhere(['<>','id',1007])*/->andWhere(['status' => 1])->all();
        foreach($statusListBase as $i => $status){
            $statusListBase[$i] = $status->id;
        }

        $statusList = [];
        if(isset($params['orders-provider-status'])){
            if($params['orders-provider-status'] == 'disable'){
                $querySearchOrders->andWhere(['orders_items.status' => 0]);
            }else{
                $statusList = $statusListBase;//[1001,1002,1003,1004,1005,1008];
                if($params['orders-provider-status'] == 'all'){

                }elseif(in_array($params['orders-provider-status']*1,$statusList)){
                    $statusList = [$params['orders-provider-status']];
                }
            }
        }elseif(isset($params['orders-provider-confirm']) && $params['orders-provider-confirm'] == 'overdue') {
            $statusList = [1001, 1002, 1003, 1004, 1005];
            $querySearchOrders
                ->andWhere(['<', 'orders_groups.delivery_date', date('Y-m-d h:i:s')]);
        }

        if(isset($params['orders-provider-confirm']) && $params['orders-provider-confirm'] == 'noconfirm'){
            $querySearchOrders->andWhere(['orders_items.status_id' => NULL]);
            $querySearchOrders->andWhere(['orders_items.status' => 1]);
        }elseif(!empty($statusList)){
            //print '<pre>';print_r($params);print '</pre>';
            $querySearchOrders
                ->andWhere(['IN','orders_items.status_id',$statusList]);
        }

        if(isset($params['orders-provider-date-variant']) && $params['orders-provider-date-variant'] == 'order'){
            if(isset($params['orders-provider-date-start']) && !empty($params['orders-provider-date-start'])){
                $querySearchOrders
                    ->andWhere(['>=','orders.date',$params['orders-provider-date-start']]);
            }
            if(isset($params['orders-provider-date-stop']) && !empty($params['orders-provider-date-stop'])){
                $querySearchOrders
                    ->andWhere(['<=','orders.date',$params['orders-provider-date-stop']]);
            }
        }else{
            if(isset($params['orders-provider-date-start']) && !empty($params['orders-provider-date-start'])){
                $querySearchOrders
                    ->andWhere(['>=','orders_groups.delivery_date',$params['orders-provider-date-start']]);
            }
            if(isset($params['orders-provider-date-stop']) && !empty($params['orders-provider-date-stop'])){
                $querySearchOrders
                    ->andWhere(['<=','orders_groups.delivery_date',$params['orders-provider-date-stop']]);
            }
        }

        if(isset($params['orders-provider-club']) && !empty($params['orders-provider-club'])){
            if($params['orders-provider-club'] == 'all'){

            }elseif($params['orders-provider-club'] == 'home'){
                $extremeAddress = BasketLg::getClubDelivery();
                $clubList = [];
                foreach($extremeAddress as $club){
                    $clubList[] = $club['value'];
                }
                $querySearchOrders
                    ->andWhere(['NOT IN','orders_groups.address_id',$clubList]);
            }else{
                $querySearchOrders
                    ->andWhere(['=','orders_groups.address_id',$params['orders-provider-club']]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $querySearchOrders,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'orders.id' => SORT_DESC,
                    'orders.date' => SORT_DESC,
                ]
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'orders.date',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;

    }

    public function searchForProvider($params){
        $shopId = UserShop::getIdentityShop();
        $query = new Query();

        $query->from ([Orders::tableName()])
            ->select([
                "orders_items.*",
                "orders_groups.order_id",
                "(`orders_items`.`price`*`orders_items`.`count`) AS `all_money`",
                "`orders`.`date` AS `order_date`",
                "`deliveries`.`name`  AS `delivery_name`",
                "`shops_stores`.`name` AS `delivery_address`",
                "`goods`.`name` AS `good_name`",
                "`orders`.`status` AS `order_status`",
                "orders_items.count",
                "`orders_groups`.`delivery_id`",
                "`orders_groups`.`delivery_date`",
                "deliveries.name AS deliverys_name",
                "CONCAT(address.street, ', д. ', address.house,  ', кв.', `address`.`room`) AS user_address",
                "`orders_groups`.`store_id`",
                "`orders_groups`.`address_id`",
                "`goods`.`code` AS `good_code`",
                "get_options(`orders_items`.`good_id`) AS `options`",
                "get_tags(`orders_items`.`variation_id`) AS `tags`",
                "`goods`.`id` AS `good_id`",
                "`producers`.`name` AS `producer_name`",
            ])
            ->leftJoin( OrdersGroups::tableName(),'orders.id = orders_groups.order_id')
            ->leftJoin( OrdersItems::tableName(),'orders_items.order_group_id = orders_groups.id')
            ->leftJoin( Deliveries::tableName(),'orders_groups.delivery_id = deliveries.id')
            ->leftJoin( Goods::tableName(),'goods.id = orders_items.good_id')
            ->leftJoin( Producers::tableName(),'producers.id = goods.producer_id')
            ->leftJoin( Address::tableName(),'orders_groups.address_id = address.id')
            ->leftJoin( ShopsStores::tableName(),'shops_stores.id = orders_groups.store_id')
            ->leftJoin( Shops::tableName(),'shops.id = goods.shop_id')
            ->where(['IN', 'shops.id', [$shopId]])
            ->andWhere(['IN', 'orders.id', $params])
            ->andWhere(['orders.status' => 1])
            ->andWhere(['orders.type' => 1])
            ->orderBy(['orders_items.id' => SORT_DESC]);

        return $query->all();
    }

    //bad method
    public function searchAllOrdersProvider($params)
    {
        $shopId = UserShop::getIdentityShop();

        $query = OrdersItems::find()
            ->select('
				orders.*,
				goods.id AS prod_id,
				orders_items.id AS item_id,
				orders_items.count
			')
//            ->joinWith(['orders_groups'])
            ->joinWith(['orders'])
            //->joinWith(['goods'])
            ->joinWith(['shops'])
            ->where(['IN', 'shops.id', [$shopId]])
            ->andWhere(['orders.status' => 1])
            ->andWhere(['orders.type' => 1])
            ->groupBy('orders.id')
            ->orderBy(['orders.id' => SORT_DESC]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => [
                'defaultOrder' => [
                    'orders.id' => SORT_DESC,
                    'orders.date' => SORT_DESC,
                ]
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'orders.id' => [
                    'asc' => ['orders.id' => SORT_ASC],
                    'desc' => ['orders.id' => SORT_DESC],
                    'label' => 'orders.id',
                    'default' => SORT_ASC
                ],
                'orders.date',
                'confirm',
                'status',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_group_id' => $this->order_group_id,
            'good_id' => $this->good_id,
            'variation_id' => $this->variation_id,
            'time' => $this->time,
            'comission' => $this->comission,
            'price' => $this->price,
            'discount' => $this->discount,
            'count' => $this->count,
            'fee' => $this->fee,
            'bonus' => $this->bonus,
            'receive' => $this->receive,
            'release' => $this->release,
            'status_id' => $this->status_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'comments_shop', $this->comments_shop])
            ->andFilterWhere(['like', 'comments_call_center', $this->comments_call_center])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }

    public function searchOrderReport($params){

        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->all();

        $shopId = [];
        if(!empty($getUserRole)){
            foreach ($getUserRole as $item) {
                $shopId[] = $item->shop_id;
            }
        }
//        $shopId =$getUserRole['shop_id'];

        $query = (new Query())
            ->from(Shops::tableName())
            ->select([
                'shops.id AS shopId',
                'orders.id AS orderId',
                'orders.comments as comments',

                'orders.user_id AS ordersUserId',

                'orders.status AS ordersStatusId',//
                'orders.date AS orderDate',

                'orders_items.id AS orderItemId',
                'orders_items.comments_shop AS commentShop',
                'orders_items.status AS orderItemStatus',//
                'orders_items.status_id AS orderItemStatusId',//
                '(goods_variations.price *`orders_items`.`count`) AS allMoney',
                'orders_items.count AS productCount',
                'orders_items.store_id AS store_id',
                'get_options(`orders_items`.`good_id`) AS productOptions',
                'get_tags(`orders_items`.`variation_id`) AS tags',

                'orders_status.name AS ordersStatus',//

                'orders_groups.delivery_id AS deliveryId',
                'orders_groups.delivery_date AS deliveryDate',
                'deliveries.name  AS deliveryName',

                'goods.id AS productId',
                'goods.name AS productName',
                'goods_variations.code AS `productCode`',
                'goods_variations.price AS `productPrice`',

                "CONCAT(address.street, ', д. ', address.house,  ', кв.', `address`.`room`) AS userAddress",
            ])
            ->leftJoin(ShopsStores::tableName(),'shops_stores.shop_id = shops.id')
            ->leftJoin(OrdersItems::tableName(),'orders_items.store_id = shops_stores.id')
            ->leftJoin(Goods::tableName(),'goods.id = orders_items.good_id')
            ->leftJoin(GoodsVariations::tableName(),'goods_variations.id = orders_items.variation_id')
            ->leftJoin(OrdersGroups::tableName(),'orders_items.order_group_id = orders_groups.id')
            ->leftJoin(Orders::tableName(),'orders.id = orders_groups.order_id')
            ->leftJoin(Deliveries::tableName(),'orders_groups.delivery_id = deliveries.id')
            ->leftJoin(Address::tableName(),'orders_groups.address_id = address.id')
            ->leftJoin(OrdersStatus::tableName(),'orders_items.status_id = orders_status.id')

            ->where(['IN', 'shops.id', $shopId])
            ->andWhere(['orders.status' => 1])
            ->andWhere(['orders.type' => 1])
            ->orderBy(['orders.id' => SORT_DESC]);

        $statusListBase = OrdersStatus::find()->where(['type' => 2])->andWhere(['status' => 1])->all();
        foreach($statusListBase as $i => $status){
            $statusListBase[$i] = $status->id;
        }

        $statusList = [];
        if(isset($params['orders-provider-status'])){
            if($params['orders-provider-status'] == 'disable'){
                $query->andWhere(['orders_items.status' => 0]);
            }else{
//                $statusList = $statusListBase;//[1001,1002,1003,1004,1005,1008];
		$statusList = [1001, 1002, 1003, 1004, 1005, 1006, 1007, 1008];
                
                if($params['orders-provider-status'] == 'all'){

                }elseif(in_array($params['orders-provider-status']*1,$statusList)){
                    $statusList = [$params['orders-provider-status']];
                }
            }
        }elseif(isset($params['orders-provider-confirm']) && $params['orders-provider-confirm'] == 'overdue') {
            $statusList = [1001, 1002, 1003, 1004, 1005];
            $query->andWhere(['<', 'orders_groups.delivery_date', date('Y-m-d h:i:s')]);
        }


        if(isset($params['orders-provider-confirm']) && $params['orders-provider-confirm'] == 'noconfirm'){
            $query->andWhere(['orders_items.status_id' => NULL]);
            $query->andWhere(['orders_items.status' => 1]);
        }elseif(!empty($statusList)){
            if(isset($params['orders-provider-confirm']) && $params['orders-provider-confirm'] == 'all'){

            }else {
                $query->andWhere(['IN', 'orders_items.status_id', $statusList]);
            }
        }

        if(isset($params['orders-provider-date-variant']) && $params['orders-provider-date-variant'] == 'order'){
            if(isset($params['orders-provider-date-start']) && !empty($params['orders-provider-date-start'])){
                $query->andWhere(['>=','orders.date',$params['orders-provider-date-start']]);
            }
            if(isset($params['orders-provider-date-stop']) && !empty($params['orders-provider-date-stop'])){
                $query->andWhere(['<=','orders.date',$params['orders-provider-date-stop']]);
            }
        }else{
            if(isset($params['orders-provider-date-start']) && !empty($params['orders-provider-date-start'])){
                $query->andWhere(['>=','orders_groups.delivery_date',$params['orders-provider-date-start']]);
            }
            if(isset($params['orders-provider-date-stop']) && !empty($params['orders-provider-date-stop'])){
                $query->andWhere(['<=','orders_groups.delivery_date',$params['orders-provider-date-stop']]);
            }
        }

        if(isset($params['orders-provider-club']) && !empty($params['orders-provider-club'])){
            if($params['orders-provider-club'] == 'all'){

            }elseif($params['orders-provider-club'] == 'home'){
                $extremeAddress = BasketLg::getClubDelivery();
                $clubList = [];
                foreach($extremeAddress as $club){
                    $clubList[] = $club['value'];
                }
                $query->andWhere(['NOT IN','orders_groups.address_id',$clubList]);
            }else{
                $query->andWhere(['=','orders_groups.address_id',$params['orders-provider-club']]);
            }
        }

        if(!empty($params['OrdersItemsSearch']['orderId'])){
            $query->andWhere(['orders.id'=>$params['OrdersItemsSearch']['orderId']]);
        }
        //print_r($query);die;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'orders.id' => SORT_DESC,
                    'orders.date' => SORT_DESC,
                ]
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'orderId',
                'productName',
                'productCount',
                'allMoney',
                //'orderDate',
                //'deliveryDate',
                'orderItemStatusId',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            //'orderDate' => $this->id,
            //'deliveryDate' => $this->order_group_id,
            'good_id' => $this->good_id,
            'variation_id' => $this->variation_id,
            'time' => $this->time,
            'comission' => $this->comission,
            'price' => $this->price,
            'discount' => $this->discount,
            'count' => $this->count,
            'fee' => $this->fee,
            'bonus' => $this->bonus,
            'seller_status_id_' => $this->seller_status_id_,
            'receive' => $this->receive,
            'release' => $this->release,
            'status_id' => $this->status_id,
            'status' => $this->status,
            //'orders_items.status_id' => $this->orderItemStatusId,
        ]);

        $query->andFilterWhere(['like', 'orders.id', $this->orderId])
            ->andFilterWhere(['like', 'goods.name', $this->productName]);
            
//            $sql= $query->createCommand()->getRawSql();
//            echo($sql);die();

        return $dataProvider;
    }

    public function searchOrderIdReport($params){
        $shopId = UserShop::getIdentityShop();

        $query = (new Query())
            ->from(Shops::tableName())
            ->select([
                'orders.id AS orderId',
            ])
            ->leftJoin(Goods::tableName(),'shops.id = goods.shop_id')
            ->leftJoin(OrdersItems::tableName(),'goods.id = orders_items.good_id')
            ->leftJoin(OrdersGroups::tableName(),'orders_items.order_group_id = orders_groups.id')
            ->leftJoin(Orders::tableName(),'orders.id = orders_groups.order_id')
            ->leftJoin(Deliveries::tableName(),'orders_groups.delivery_id = deliveries.id')
            ->leftJoin(Address::tableName(),'orders_groups.address_id = address.id')
            ->leftJoin(OrdersStatus::tableName(),'orders_items.status_id = orders_status.id')

            ->where(['IN', 'shops.id', [$shopId]])
            ->andWhere(['orders.status' => 1])
            ->andWhere(['orders.type' => 1])
            ->orderBy(['orders.id' => SORT_DESC])
            ->groupBy('orders.id');

        $statusListBase = OrdersStatus::find()->where(['type' => 2])->andWhere(['status' => 1])->all();
        foreach($statusListBase as $i => $status){
            $statusListBase[$i] = $status->id;
        }

        $statusList = [];
        if(isset($params['orders-provider-status'])){
            if($params['orders-provider-status'] == 'disable'){
                $query->andWhere(['orders_items.status' => 0]);
            }else{
                $statusList = $statusListBase;
                if($params['orders-provider-status'] == 'all'){

                }elseif(in_array($params['orders-provider-status']*1,$statusList)){
                    $statusList = [$params['orders-provider-status']];
                }
            }
        }elseif(isset($params['orders-provider-confirm']) && $params['orders-provider-confirm'] == 'overdue') {
            $statusList = [1001, 1002, 1003, 1004, 1005];
            $query->andWhere(['<', 'orders_groups.delivery_date', date('Y-m-d h:i:s')]);
        }

        if(isset($params['orders-provider-confirm']) && $params['orders-provider-confirm'] == 'noconfirm'){
            $query->andWhere(['orders_items.status_id' => NULL]);
            $query->andWhere(['orders_items.status' => 1]);
        }elseif(!empty($statusList)){
            $query->andWhere(['IN','orders_items.status_id',$statusList]);
        }

        if(isset($params['orders-provider-date-variant']) && $params['orders-provider-date-variant'] == 'order'){
            if(isset($params['orders-provider-date-start']) && !empty($params['orders-provider-date-start'])){
                $query->andWhere(['>=','orders.date',$params['orders-provider-date-start']]);
            }
            if(isset($params['orders-provider-date-stop']) && !empty($params['orders-provider-date-stop'])){
                $query->andWhere(['<=','orders.date',$params['orders-provider-date-stop']]);
            }
        }else{
            if(isset($params['orders-provider-date-start']) && !empty($params['orders-provider-date-start'])){
                $query->andWhere(['>=','orders_groups.delivery_date',$params['orders-provider-date-start']]);
            }
            if(isset($params['orders-provider-date-stop']) && !empty($params['orders-provider-date-stop'])){
                $query->andWhere(['<=','orders_groups.delivery_date',$params['orders-provider-date-stop']]);
            }
        }

        if(isset($params['orders-provider-club']) && !empty($params['orders-provider-club'])){
            if($params['orders-provider-club'] == 'all'){

            }elseif($params['orders-provider-club'] == 'home'){
                $extremeAddress = BasketLg::getClubDelivery();
                $clubList = [];
                foreach($extremeAddress as $club){
                    $clubList[] = $club['value'];
                }
                $query->andWhere(['NOT IN','orders_groups.address_id',$clubList]);
            }else{
                $query->andWhere(['=','orders_groups.address_id',$params['orders-provider-club']]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'orders.id' => SORT_DESC,
                    'orders.date' => SORT_DESC,
                ]
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'orders.id',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
