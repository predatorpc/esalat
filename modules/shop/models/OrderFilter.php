<?php

namespace app\modules\shop\models;

use app\modules\basket\models\Basket;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\Codes;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsTypes;
use app\modules\common\models\ActiveRecordRelation;
use app\modules\common\models\DeliveriesPrices;
use app\modules\common\models\User;
use app\modules\common\models\UsersPays;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsStores;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $code_id
 * @property integer $type
 * @property string $extremefitness
 * @property string $comments
 * @property string $comments_call_center
 * @property string $date
 * @property integer $call_status
 * @property integer $status
 *
 * @property UsersPays[] $usersPays
 */
class OrderFilter extends Model
{
    private $params = [];
    private $order;
    private $orderQuery;
    private $orderQueryEmpty;
    private $ordersItemsQuery;

    public $dateStatus = 0;
    public $dateStart;
    public $dateEnd;

    public $shops = [];
    public $clients = [];
    public $staff = 2;
    public $category;
    public $productType;
    public $orderItemStatus = 1;

    public $priceResult = [];

    public function __construct()
    {
        $this->order = new Orders();
        $this->orderQuery = $this->getEmptyQuery();
    }

    public function getEmptyQuery(){
        return Orders::find()
            ->leftJoin('orders_groups','orders_groups.order_id = orders.id')
            ->leftJoin('orders_items','orders_items.order_group_id = orders_groups.id')
            ->leftJoin('users','users.id = orders.user_id')
            ->orderBy('orders.id DESC');
    }

    public function setFilterParams($params){
        if(empty($params)){

            if(!empty(Yii::$app->session['reports']['order']['find']['params'])){
                $params = Yii::$app->session['reports']['order']['find']['params'];
            }
        }else{
            $_SESSION['reports']['order']['find']['params'] = $params;
        }
        $this->params = $params;
    }

    public function getFilterParams(){
        return $this->params;
    }

    public function setOrdersItemsQuery($query){
        $this->ordersItemsQuery = $query;
    }

    public function getOrdersItemsQuery(){
        return $this->ordersItemsQuery;
    }

    public function getShopsList(){
        $params = Yii::$app->session['reports']['order']['find']['params'];
        if(!empty($params['Shops']['id'])){
            foreach ($params['Shops']['id'] as $i => $shop) {
                if(empty($shop)){
                    unset($params['Shops']['id'][$i]);
                }
            }
        }
        if(!empty($params['Shops']['id'])){
            return ArrayHelper::map(Shops::find()->where(['IN','id',$params['Shops']['id']])->all(),'id','name');
        }
    }

    public static function getOrderItemQuery($id){
        $ordersItems = OrdersItems::find()
            ->leftJoin('orders_groups','orders_groups.id = orders_items.order_group_id')
            ->where(['orders_groups.order_id' => $id]);

        if(!empty(Yii::$app->session['reports']['order']['find']['params'])){
            $params = Yii::$app->session['reports']['order']['find']['params'];

            if(!empty($params['OrdersItems']['status_id'])){
                if($params['OrdersItems']['status_id'] != 'NULL'){
                    $ordersItems->andWhere(['status_id' => $params['OrdersItems']['status_id']]);
                }
            }

            if(!empty($params['Shops']['id'])){
                $ordersItems
                    ->leftJoin('shops_stores','shops_stores.id = orders_items.store_id')
                    ->leftJoin('shops','shops.id = shops_stores.shop_id')
                    ->andWhere(['IN','shops.id',$params['Shops']['id']]);
            }
        }
        return $ordersItems;
    }

    public function getOrderListFilter(){
        if(!empty($this->params)){
            if(!empty($this->params['OrderFilter']['productType'])){
                $this->productType = $this->params['OrderFilter']['productType'];
                $this->orderQuery
                    ->leftJoin('goods','goods.id = orders_items.good_id')
                    ->andWhere(['IN','goods.type_id',$this->productType]);
            }

            if(!empty($this->params['OrderFilter']['staff']) && $this->params['OrderFilter']['staff'] == 0){
                if($this->params['OrderFilter']['staff'] == 0){
                    $this->orderQuery
                        ->andWhere(['IS','users.staff',NULL]);
                }elseif($this->params['OrderFilter']['staff'] == 1){
                    $this->orderQuery
                        ->andWhere(['IS NOT','users.staff',NULL]);
                }
                $this->staff = $this->params['OrderFilter']['staff'];
            }

            if(!empty($this->params['Clients']['id'])){
                foreach ($this->params['Clients']['id'] as $i => $client) {
                    if(empty($client)){
                        unset($this->params['Clients']['id'][$i]);
                    }
                }
            }
            if(!empty($this->params['Clients']['id'])){
                $this->orderQuery
                    ->andWhere(['IN','orders.user_id',$this->params['Clients']['id']]);

                $this->clients = ArrayHelper::map(User::find()->where(['IN','id',$this->params['Clients']['id']])->all(),'id','name');
            }
            //----------------------------
            if(!empty($this->params['Shops']['id'])){
                foreach ($this->params['Shops']['id'] as $i => $shop) {
                    if(empty($shop)){
                        unset($this->params['Shops']['id'][$i]);
                    }
                }
            }
            if(!empty($this->params['Shops']['id'])){
                $this->orderQuery
                    ->leftJoin('shops_stores','shops_stores.id = orders_items.store_id')
                    ->leftJoin('shops','shops.id = shops_stores.shop_id')
                    ->andWhere(['IN','shops.id',$this->params['Shops']['id']]);

                $this->shops = ArrayHelper::map(Shops::find()->where(['IN','id',$this->params['Shops']['id']])->all(),'id','name');
            }
            //----------------------------
            if(!empty($this->params['Orders']['id'])){
                $this->orderQuery->andWhere(['orders.id' => intval($this->params['Orders']['id'])]);
            }
            if(!empty($this->params['Orders']['type'])){
                $this->orderQuery->andWhere(['orders.type' => intval($this->params['Orders']['type'])]);
            }
            if(!empty($this->params['Orders']['user_id'])){
                $this->orderQuery->andWhere(['orders.user_id' => intval($this->params['Orders']['user_id'])]);
            }
            //----------------------------
            if(!empty($this->params['OrderFilter']['dateStatus'])){
                $this->dateStatus = $this->params['OrderFilter']['dateStatus'];
            }
            //----------------------------
            if($this->dateStatus == 0){
                if(!empty($this->params['OrderFilter']['dateStart'])){
                    $this->orderQuery->andWhere(['>=','orders.date',$this->params['OrderFilter']['dateStart'] . ' 00:00']);
                }
                if(!empty($this->params['OrderFilter']['dateEnd'])){
                    $this->orderQuery->andWhere(['<=','orders.date',$this->params['OrderFilter']['dateEnd'] . ' 23:59:59']);
                }
            }else{
                if(!empty($this->params['OrderFilter']['dateStart'])){
                    $this->orderQuery
                        ->andWhere(['>=','orders_groups.delivery_date',$this->params['OrderFilter']['dateStart'] . ' 00:00']);
                }
                if(!empty($this->params['OrderFilter']['dateEnd'])){
                    $this->orderQuery
                        ->andWhere(['<=','orders_groups.delivery_date',$this->params['OrderFilter']['dateEnd'] . ' 23:59:59']);
                }
            }
            //----------------------------
            if(!empty($this->params['Codes']['code'])){
                $this->orderQuery->leftJoin('codes','codes.id = orders.code_id')->andWhere(['codes.code' => intval($this->params['Codes']['code'])]);
            }
            //----------------------------
            if(!empty($this->params['User']['name'])){
                $this->orderQuery->andWhere(['LIKE','users.name',$this->params['User']['name']]);
            }
            if(!empty($this->params['User']['store_id'])){
                if(!empty($this->params['Codes']['code'])){

                }else{
                    $this->orderQuery->leftJoin('codes','codes.id = orders.code_id');
                }
                $this->orderQuery->leftJoin('users AS usersForCodes','usersForCodes.id = codes.user_id');//->andWhere(['usersForCodes.store_id' => $this->params['User']['store_id']]);
                $this->orderQuery->andWhere([
                    'OR',
                    ['orders_groups.store_id' => $this->params['User']['store_id']],
                    ['usersForCodes.store_id' => $this->params['User']['store_id']]
                ]);
            }
            //----------------------------
            if(!empty($this->params['OrdersItems']['status_id'])){
                // Обработка статусов;

                if($this->params['OrdersItems']['status_id'] > 1000){
                    $this->orderQuery->andWhere(['orders_items.status_id' => intval($this->params['OrdersItems']['status_id'])]);
                    $this->orderQuery->andWhere(['orders_items.status' => 1]);
                }
                if($this->params['OrdersItems']['status_id'] == 1008){
                    $this->orderQuery->andWhere(['orders_items.status' => 0]);
                }
                if($this->params['OrdersItems']['status_id'] == 'NO'){
                    $this->orderQuery->andWhere(['orders_items.status' => 1]);
                    $this->orderQuery->andWhere([
                        'OR',
                        ['!=','orders_items.status_id',1007],
                        ['IS','orders_items.status_id',NULL]
                    ]);
                }
                if($this->params['OrdersItems']['status_id'] == 'NULL'){
                    $this->orderQuery->andWhere(['orders_items.status' => 1]);
                    $this->orderQuery->andWhere(['IS','orders_items.status_id',NULL]);
                }
                if($this->params['OrdersItems']['status_id'] == 0){
                    $this->orderQuery->andWhere(['orders_items.status' => 0]);
                    $this->orderQuery->andWhere([
                        'OR',
                        ['!=','orders_items.status_id',1008],
                        ['IS','orders_items.status_id',NULL]
                    ]);
                }
            }else{
                $this->orderQuery->andWhere(['orders_items.status' => 1]);
//                $this->orderQuery->andWhere(['IS NOT','orders_items.status_id',NULL]);
            }
            $this->orderQuery->andWhere(['orders.status' => 1]);
        }

        $this->orderQueryEmpty = clone $this->orderQuery;

        $this->priceResult['shopsPays'] = $this->orderQueryEmpty->sum('(orders_items.price - orders_items.discount - orders_items.comission) * orders_items.count');
        $this->priceResult['feePays'] = $this->orderQueryEmpty->sum('orders_items.fee * orders_items.count');

        $this->priceResult['fullPrice'] = $this->orderQueryEmpty->sum('orders_items.price * orders_items.count');
        $this->priceResult['bonus'] = $this->orderQueryEmpty->sum('orders_items.bonus * orders_items.count');
        $this->priceResult['discount'] = $this->orderQueryEmpty->sum('orders_items.discount * orders_items.count');

        $this->priceResult['commission'] = $this->orderQueryEmpty->sum('(orders_items.discount + orders_items.comission) * orders_items.count');
        $this->priceResult['commissionMinus'] = $this->orderQueryEmpty->sum('(orders_items.discount + orders_items.bonus + orders_items.fee) * orders_items.count');

        $this->priceResult['deliveryPrice'] = $this->orderQueryEmpty
            ->select(['orders_groups.delivery_price AS dPrice','orders_groups.id'])
            ->groupBy('orders_groups.id')
            ->sum('dPrice');
        $this->priceResult['deliveryPays'] = $this->orderQueryEmpty
            ->leftJoin('orders_selects','orders_selects.order_group_id = orders_groups.id')
            ->select(['orders_selects.price AS sPrice','orders_groups.id','orders_groups.delivery_surcharge AS deliverySurcharge'])
            ->andWhere(['>','orders_selects.status',0])
            ->groupBy('orders_groups.id')
            ->sum('sPrice + deliverySurcharge');

        $this->priceResult['commissionDelivery'] = $this->priceResult['deliveryPrice'] - $this->priceResult['deliveryPays'];
        $this->priceResult['cancel'] = $this->orderQueryEmpty
            ->select(['orders_items.price as oPrice','orders_items.discount as oDiscount','orders_items.bonus as oBonus','orders_items.count as oCount'])
            ->andWhere(['orders_items.status' => 0])
            ->sum('(oPrice - oDiscount - oBonus) * oCount');
        if(empty($this->priceResult['cancel'])){
            $this->priceResult['cancel'] = 0;
        }

    }

    public function getOrderList(){
        return $this->orderQuery->groupBy('orders.id');
    }

    public function getModelOrder(){
        //return !empty($this->params['Orders']['id']) ? Orders::findOne($this->params['Orders']['id']) : new Orders();
        return $this->orderQuery->one();
    }

    public function getModelOrderType(){
        if(!empty($this->params['Orders']['type'])){
            return Orders::find()->where(['type' => $this->params['Orders']['type']])->one();
        }else{
            return new Orders();
        }
    }

    public function getModelOrderId(){
        if(!empty($this->params['Orders']['id'])){
            return Orders::findOne($this->params['Orders']['id']);
        }else{
            return new Orders();
        }
    }

    public function getModelOrderUser(){
        if(!empty($this->params['Orders']['user_id'])){
            return Orders::find()->where(['user_id' => $this->params['Orders']['user_id']])->one();
        }else{
            return new Orders();
        }
    }

    public function getModelUser(){
        if(!empty($this->params['User']['name'])){
            return User::find()->where(['LIKE','users.name',$this->params['User']['name']])->one();
        }else{
            return new User();
        }
    }

    public function getModelUserForStore(){
        if(!empty($this->params['User']['store_id'])){
            return User::find()->where(['users.store_id' => $this->params['User']['store_id']])->one();
        }else{
            return new User();
        }
    }

    public function getOrdersGroupsForStore(){
        if(!empty($this->params['User']['store_id'])){
            return OrdersGroups::find()->where(['store_id' => $this->params['User']['store_id']])->one();
        }else{
            return new OrdersGroups();
        }
    }

    public function getModelOrderItem(){
        if(!empty($this->params['OrdersItems']['status_id'])){
            if($this->params['OrdersItems']['status_id'] != 'NULL'){
                return OrdersItems::find()->where(['status_id' => $this->params['OrdersItems']['status_id']])->one();
            }else{
                $ordersItems = new OrdersItems();
                $ordersItems->status_id = 'NULL';
                return $ordersItems;
            }
        }else{
            return new OrdersItems();
        }
    }

    public function getModelPromoCode(){
        if(!empty($this->params['Codes']['code'])){
            return Codes::find()->where(['code' => $this->params['Codes']['code']])->one();
        }else{
            return new Codes();
        }
    }

    public function getModelOrderDateStart(){
        $this->dateStart = !empty($this->params['OrderFilter']['dateStart']) ? $this->params['OrderFilter']['dateStart'] : date('Y-m-d');
        return $this;
    }

    public function getModelOrderDateEnd(){
        $this->dateEnd = !empty($this->params['OrderFilter']['dateEnd']) ? $this->params['OrderFilter']['dateEnd'] : date('Y-m-d');
        return $this;
    }

    public function checkParam($modelName,$param){
        return !empty($this->params[$modelName][$param]) ? true : false;
    }

    public function getCategoryList(){
        return Category::find()->where(['active' => 1,'level' => 0])->all();
    }

    public function getProductTypeList(){
        return GoodsTypes::find()->where(['status' => 1])->all();
    }

    public function getOrderListFullPrice(){
        return $this->orderQueryEmpty->sum('orders_items.price * orders_items.count');
    }

    public function getOrderListFullBonus(){
        return $this->orderQueryEmpty->sum('orders_items.bonus * orders_items.count');
    }

    public function getOrderListFullDiscount(){
        return $this->orderQueryEmpty->sum('orders_items.discount * orders_items.count');
    }

    public function getOrderListFullDelivery(){
        return $this->orderQueryEmpty->select(['orders_groups.delivery_price AS dPrice','orders_groups.id'])->groupBy('orders_groups.id')->sum('dPrice');
    }

}

