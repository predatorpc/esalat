<?php
namespace app\component;

use app\modules\actions\models\Actions;
use app\modules\actions\models\ActionsParamsValue;
use app\modules\actions\models\ActionsAccumulation;
use app\modules\basket\models\Basket;
use app\modules\basket\models\BasketProducts;
use app\modules\basket\models\PromoCode;
use app\modules\managment\models\ShopGroupVariantLink;

use yii\base\Component;
use Yii;

/*----------------Компонент Акции----------------
 * ------------------Описание--------------------
 *
 * */

class ActionsComponent extends Component{

    CONST ACTION_DELIVERY =5;
    CONST ACTION_GROUP =7;
    CONST ACTION_ON = [1=>'variant_id', 2=>'product_id', 3=>'category', 4=>'type', 6=>'variant_id'];//, 7=>'group'];
    const TYPE_GOOD = [1003, 1007];
    const SPORT_FOOD = [1010,1012];
    const GOODS_EF = 10000215;// товары extreme fitness исключаем из скидки по промокоду
    CONST STATUS_ENABLE =1;
    CONST STATUS_DISABLE =0;
    const IGNORED_VARIATION = [1000066096, 1000066336];//[1000075159,1000075177];//1000059533 1000066096
    const IGNORED_VARIATION_IS = [1000066096, 1000066336];//[1000075177];//1000059533 1000066096


    private $basket = false;
    private $basketProducts = [];
    private $price = false;
    private $priceWithBonus = false;//цена корзины с учетом потраченых бонусов по продуктам и товарам для ддома 1003 и 1007
    private $priceFull = false;
    private $freeBonus = 0;

    private $addBonus = 0;
    private $addRubl = 0;
    private $addPercent = 0;

    private $accumulation = [];
    private $promoFlag = false;

    public function init(){
        parent::init();
        $this->basket = Yii::$app->basket->getBasket();
        $this->basketProducts = Yii::$app->basket->getBasketProducts();
        foreach ($this->basketProducts as $key=>$product){//определение priceDiscont
            $this->basketProducts[$key]['priceDiscount'] = $product['price'] - $product['bonus'];
            //var_dump($product->variant->product->type_id);
        }
        //отладка корзины
        //$this->getActiveAccum();
        //echo '<pre>';
        //print_r($this->basket['products']);
        //echo '<pre>';
        //die();
        /*
        echo 'init</br>';
        foreach ($this->basketProducts as $key => $product) {
            echo $product['variant_id'] .' '.$product['price'].' '. $product['priceDiscount'].'</br>';
            //print_R($product->propertyList);
            foreach ($product->product->variationsCatalog as $variant){
                print_r($variant);
            }
            //print_R($product->product->variationsCatalog);
        }
        echo '</br>';
        */

        //оплата бонусами испольщуем пока что старый вариант
        //$this->freeBonus = $this->basket->priceBonus;
        //$this->calcBonus();//расчет бонусами, которые есть на счету
    }

    public function getAccumulation(){
        foreach ($this->accumulation as $actionId=>$params) {
            foreach ($params as $paramId => $param) {
                if(empty($param['count_purchase'])){
                    $this->accumulation[$actionId][$paramId]['current_value']=$this->priceWithBonus;
                }
            }
        }
        return $this->accumulation;
    }

    public function getIgnored(){
        return self::IGNORED_VARIATION;
    }

    public function applyActions(){
        $this->checkingAllActions();
        return $this->basket;
    }

    public function getPromoFlag(){
        return $this->promoFlag;
    }

    public function getCurrentBasket(){
        return $this->basket;
    }

    public function getCurrentBasketProducts(){
        return $this->basketProducts;
    }

    public function getActiveAccum(){
        $actions = Actions::find()->where(['status'=>1, 'accumulation'=>1])
            ->andWhere(['<', 'date_start', time()])
            ->andWhere(['>', 'date_end', time()])
            ->orderBy(['priority'=>SORT_ASC])
            ->with('accum')
            ->with('actionParamsValue')
            ->asArray()->all();
        $result = [];
        foreach ($actions as $key =>$action){
            if(!empty($action['accum'])){
                foreach ($action['accum'] as $keyAcum => $accum){
                    $result[$key]['action_id'] = $action['id'];
                    $result[$key]['param_value_id'] = $accum['action_param_value_id'];
                    $result[$key]['max_accumulation'] = $action['accum_value'];
                    $result[$key]['current_accumulation'] = $accum['current_value'];
                    if($accum['count_row'] == $accum['active_row']){
                        $result[$key]['spent'] = false;
                    }
                    else{
                        $result[$key]['spent'] = true;
                    }
                    $result[$key]['condition_value'] = $accum['actionParamValue']['condition_value'];
                    $result[$key]['area'] = substr($accum['actionParamValue']['param_id'], 1,1);
                }
            }
            else{
                foreach ($action['actionParamsValue'] as $keyAcum => $paramValue){
                    $result[$key]['action_id'] = $action['id'];
                    $result[$key]['param_value_id'] = $paramValue['id'];
                    $result[$key]['max_accumulation'] = $action['accum_value'];
                    $result[$key]['current_accumulation'] = 0;
                    $result[$key]['spent'] = false;
                    $result[$key]['condition_value'] = $paramValue['condition_value'];
                    $result[$key]['area'] = $paramValue['param']['area'];
                }
            }

        }
        /*
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        */
        return $result;
    }

    public function getResultPrice(){
        $result = !empty($this->basket->delivery_price) ? $this->basket->delivery_price : 0;
        return $result + $this->price;
    }

    public function applyActionOnFindBasket(Basket $basket){
        //применения акции для найденой корзины
        if(!empty($basket)){
            $this->basket = $basket;
            $this->basketProducts = $basket['products'];
            //print_r($this->basketProducts);
            foreach ($this->basketProducts as $key=>$product){
                $this->basketProducts[$key]['priceDiscount'] = $product['price'] - $product['bonus'];
            }
            $this->checkingAllActions();
            return $this->basket;
        }
        return false;

    }

    public function cashBackReturn(Basket $basket){
        if(!empty($basket)){
            $this->basket = $basket;
            $this->basketProducts = $basket['products'];
            foreach ($this->basketProducts as $key=>$product){
                $this->basketProducts[$key]['priceDiscount'] = $product['price'] - $product['bonus'];
            }
            $this->checkingAllActions();
            $cashBacks = [
                'money'=>$this->addRubl,
                //'percent'=>$this->addPercent,
                'bonus'=>$this->addBonus + $this->addPercent,
            ];
            return $cashBacks;
        }
        return false;

    }

    public function cashBackValues(){
        $cashBacks = [
            'money'=>$this->addRubl,
            //'percent'=>$this->addPercent,
            'bonus'=>$this->addBonus + $this->addPercent,
        ];
        return $cashBacks;
    }

    public function getActivActionsIdJsonArray(){
        $actions = Actions::find()->select('id as action_id')->where(['status'=>1])
            ->andWhere(['<', 'date_start', time()])
            ->andWhere(['>', 'date_end', time()])
            ->orderBy(['priority'=>SORT_ASC])
            ->asArray()->all();
        return json_encode($actions);
    }

    public function calcDeliveryPriceWithDiscont($summ = false, $goodType = false){
        if(!$summ){
            return 0;
        }
        $deliveryPrice = $summ;
        if(in_array($goodType, self::TYPE_GOOD)){
            $actions = Actions::find()->where(['status'=>1])
                ->andWhere(['<', 'date_start', time()])
                ->andWhere(['>', 'date_end', time()])
                ->orderBy(['priority'=>SORT_ASC])
                ->with('actionvalues')
                ->asArray()->all();
            //находим акции
            //перебираем все активные акции и в зависимости от объекта
            if(!empty($actions)){
                foreach ($actions as $action){
                    if(!empty($action['actionvalues'])){
                        if($this->checkPromo($action)){
                            foreach ($action['actionvalues'] as $actionValue){//проверить бы на не пустое
                                if($actionValue['param']['area']==self::ACTION_DELIVERY){
                                    $flag=false;
                                    $sum = 0;
                                    foreach ($this->basketProducts as $key => $product) {
                                        /*if($product->variant->product->type_id == $actionValue['condition_value']) {
                                            $flag= true;
                                        }*/
                                        //if($product->variant->product->type_id == 1003 /*|| $product->variant->product->type_id == 1005*/ || $product->variant->product->type_id == 1007 ) {
                                        if(in_array($product->variant->product->type_id, self::TYPE_GOOD)){
                                            $sum = $sum + ($product['price']*$product['count']);
                                            $flag = true;
                                        }
                                    }
                                    if(!empty($actions['accumalation'])){
                                        $accumVal = ActionsAccumulation::find()->select('basket_id, action_param_value_id, current_value')
                                            ->where(['user_id'=>Yii::$app->user->id, 'action_param_value_id'=>$actionValue['id'], 'status'=>self::STATUS_ENABLE])
                                            ->groupBy(['user_id', 'action_param_value_id'])
                                            ->one();
                                        if(!empty($accumVal)){
                                            $basketPrice = $accumVal['current_value'];//+$this->price;
                                        }
                                        else{
                                            $basketPrice = -1;//$this->price;
                                        }
                                        $curAction = Actions::find()->where(['id'=>$actionValue['action_id']])->asArray()->one();//сколько нужно накопить для того что бы заработала акция
                                        $basketConditionPrice = $curAction['accum_value'];
                                    }
                                    else{
                                        $basketConditionPrice = $actionValue['basket_price'];
                                        $basketPrice = $sum;//$this->price;
                                    }
                                    if(($basketPrice>=$basketConditionPrice) && ($flag)) {//цена корзины устраивает $actionValue['basket_price']
                                        if(!empty($actionValue['condition_value'])) {//если id есть то ищем по ID доставки хз как и что такое
                                            return false;// логика aka корзина
                                        }
                                        else{
                                            if($actionValue['param']['type']==1) {//скидка
                                                if($actionValue['param']['currency']==1){//Рубли
                                                    $deliveryPrice = $deliveryPrice - $actionValue['discont_value'];
                                                    if($deliveryPrice<0) {
                                                        $deliveryPrice = 0;
                                                    }
                                                }
                                                elseif($actionValue['param']['currency']==2) {//проценты
                                                    $deliveryPrice = floor($deliveryPrice *(1-($actionValue['discont_value']/100) ));
                                                    if($deliveryPrice<0){
                                                        $deliveryPrice = 0;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }
                        }

                        if($action['block']==self::STATUS_ENABLE && $this->checkPromo($action)){//
                            break; //закончили перебирать акции и пора уже вернуть конечную цену
                        }
                    }
                }

            }
        }

        return $deliveryPrice;

    }

    public function checkingAllActions(){
        $actions = Actions::find()->where(['status'=>1])
                        ->andWhere(['<', 'date_start', time()])
                        ->andWhere(['>', 'date_end', time()])
                        //->andWhere(['Accumulation'=>self::STATUS_DISABLE])
                        ->orderBy(['priority'=>SORT_ASC])
                        ->with('actionvalues')
                        ->asArray()->all();
        //print_r($actions);die();
        //находим акции
        //перебираем все активные акции и в зависимости от объекта
        if(!empty($actions)){
            foreach ($actions as $action){
                if( (empty($action['for_user_id'])) || (!empty($action['for_user_id']) && ($action['for_user_id']==Yii::$app->user->id) && ($action['count_for_user'] > 0) ) ){
                    if(!empty($action['actionvalues'])){
                        //print_r($action);
                        foreach ($action['actionvalues'] as $actionValue){//проверить бы на не пустое
                            //print_r($actionValue);
                            //TODO:45554
                            if($this->checkPromo($action)){
                                if(empty($actions['accumalation'])){
                                    $actions['accumalation']=false;
                                }
                                if(array_key_exists($actionValue['param']['area'], self::ACTION_ON)){
                                    $this->chekingProducts($actionValue['id'], $action['accumulation']);
                                }
                                /*
                                                        if($actionValue['param']['area']==self::ACTION_BASKET){
                                                            $this->chekingBasket($actionValue['id'], $actions['accumalation']);
                                                        }*/

                                if($actionValue['param']['area']==self::ACTION_DELIVERY){
                                    $this->chekingDelivery($actionValue['id'], $action['accumulation']);
                                }
                                if($actionValue['param']['area']==self::ACTION_GROUP){
                                    $this->chekingGroup($actionValue['id'], $action['accumulation']);
                                }
                            }
                        }
                        //$this->calcPrice();
                        if($action['block']==self::STATUS_ENABLE && $this->checkPromo($action)){//
                            break; //закончили перебирать акции и пора уже вернуть конечную цену
                        }
                    }
                }

            }
        }

        /*
        echo 'allactions</br>';
        foreach ($this->basketProducts as $key => $product) {
            echo $product['variant_id'] .' '. $product['priceDiscount'].'</br>';
        }
        echo '</br>';*/

        $this->CalcPromoCode(); //TODO:: НА один день
        $this->calcPrice();

        /*
        print_r($this->addBonus);echo '*';
        print_r($this->addPercent);echo '*';
        print_r($this->addRubl);echo '*';
        print_r($this->basket);echo '*';*/
        return false;


    }

    public function checkPromo($action){
        if(!empty($action['type_promo_code'])){
            if(!empty($this->basket->promo_code_id)) {
                $promocode = PromoCode::find()->where(['id' => $this->basket->promo_code_id, 'status' => self::STATUS_ENABLE])
                    ->andWhere(['>=', 'count', self::STATUS_ENABLE])
                    ->andWhere(['<', 'date_begin', Date('Y-m-d H:i:s', time())])
                    ->andWhere(['>', 'date_end', Date('Y-m-d H:i:s', time())])
                    ->with('type')
                    ->one();
                if($promocode->type_id == $action['type_promo_code']){
                    $this->promoFlag = true;
                    return true;
                }
            }
            return false;

        }
        return true;


    }

    private function chekingProducts($id = false, $accumalation=false){
        if(!$id){
            return false;
        }
        $this->calcPrice();// для получения актуальных значений стоимости корзины
        //print_r($this->price);
        $actionValue = ActionsParamsValue::find()->where(['id'=>$id])->with('param')->asArray()->one();
        if(!empty($actionValue)){
            foreach ($this->basketProducts as $key => $product) {
                if(!$accumalation){//проверяем накопительность
                    if($this->priceWithBonus >= $actionValue['basket_price']){
                        //промокод считаем отдельно
                        if(( !empty($actionValue['condition_value']) )&& (intval($actionValue['condition_value'])>0) ){//если id усть то ищем по товарам
                            $flag_fount = false;
                            if($actionValue['param']['area']==3){//category
                                $category =$product->product->category;
                                while(!empty($category)){
                                    if($actionValue['condition_value'] == $category->id){
                                        $flag_fount = true;
                                        break;
                                    }
                                    $category = $category->parent;
                                }
//                                if( $product->product->category['id']==$actionValue['condition_value'] && $product->product->category->parent->id ){
//                                    $flag_fount = true;
//                                }
                            }
                            elseif($actionValue['param']['area']==4){//type_id
                                if($product->variant->product->type_id == $actionValue['condition_value']){
                                    $flag_fount = true;
                                }
                            }
                            else{//other
                                if($product[self::ACTION_ON[$actionValue['param']['area']]] == $actionValue['condition_value']){
                                    $flag_fount=true;
                                }
                            }
                            if($flag_fount) {//нашелся товар со скидкой
                                if($actionValue['param']['type']==1) {//скидка
                                    $this->discontItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                                }
                                elseif ($actionValue['param']['type'] == 2) {//зачисление бонусов
                                    $this->cashBackItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                                }
                            }
                        }
                        else{//если условия нет то просто скидка по всем товарам корзины (по всем вариациям категориям и типам не важно на что создается)
                            if($actionValue['param']['type']==1) {//скидка
                                $this->discontItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                            }
                            elseif ($actionValue['param']['type'] == 2) {//зачисление бонусов
                                $this->cashBackItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                            }
                        }
                    }
                }
                else{
                    $accumVal = ActionsAccumulation::find()->select('user_id, action_param_value_id, SUM(current_value) as `current_value`, count(id) as `count_row`, sum(active) as `active_row`')
                        ->where(['user_id' => Yii::$app->user->id, 'action_param_value_id' => $actionValue['id'], 'status' => self::STATUS_ENABLE])
                        ->groupBy(['user_id', 'action_param_value_id'])
                        ->one();
                    //НЕ распространяется на текущую корзину
                    if (!empty($accumVal)) {
                        if($accumVal->count_row == $accumVal['active_row']){
                            $basketPrice = $accumVal->current_value;
                        }
                        else{
                            $basketPrice = -1;//$this->price;
                            //+деактивация существующих
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent'] =  1;
                        }
                    }
                    else {
                        $basketPrice = -1;
                    }
                    $curAction = Actions::find()->where(['id'=>$actionValue['action_id']])->asArray()->one();

                    //var_dump($basketPrice);
                    //var_dump($actionValue['action_id']);
                    //если накопили больше чем необходимо применяем акцию
                    if($basketPrice >= $curAction['accum_value']) {//цена коризны (товара) устраивает  $actionValue['basket_price']
                        //проверяем id товара и считаем накопление
                        $flag_fount = false;
                        if($actionValue['param']['area']==3){//categor
                            if( $product->product->category['id']==$actionValue['condition_value'] ){
                                $flag_fount = true;
                            }
                        }
                        elseif($actionValue['param']['area']==4){//type_id
                            if($product->variant->product->type_id == $actionValue['condition_value']){
                                $flag_fount = true;
                            }
                        }
                        else{//other
                            if($product[self::ACTION_ON[$actionValue['param']['area']]] == $actionValue['condition_value']){
                                $flag_fount=true;
                            }
                        }

                        if($flag_fount) {//нашелся товар со скидкой
                            if($actionValue['param']['type']==1) {//скидка
                                $this->discontItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                            }
                            elseif ($actionValue['param']['type'] == 2) {//зачисление бонусов
                                $this->cashBackItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                            }
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent'] =  1;
                        }
                        elseif(empty($actionValue['condition_value'])){//считаем накопительную акцию по всей корзине когда просто null  или можно добавить провеку типа promo
                            //добавить условие типа промокода сюда
                            if($actionValue['param']['type']==1) {//скидка
                                $this->discontItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                            }
                            elseif ($actionValue['param']['type'] == 2) {//зачисление бонусов
                                $this->cashBackItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                            }
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent'] =  1;
                        }

                    }
                    //сохраняем накопления при любом раскладе
                    //считаются наколения по найденому товару с учетом скидки и с учетом бонусов которыми оплатили товар
                    //if(empty($this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'])) {
                    //    $this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = 0;
                    //}
                    //$this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] + ($this->basketProducts[$key]['count']*($this->basketProducts[$key]['priceDiscount'] + $this->basketProducts[$key]['bonus']));
                    if($this->priceWithBonus >=  $actionValue['basket_price'] || (isset($this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent']) && $this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent']==1) ){//цена корзину устраивает сохраняем накопление
                        if(intval($curAction['count_purchase'])==1){
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $actionValue['basket_price'];//считаем по мин стоимости корзины //$this->priceWithBonus;//считаем стоимость по всей корзине и реализуем акциюю товар в подарок
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['count_purchase'] = true;
                        }
                        else{
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $this->priceWithBonus;//считаем стоимость по всей корзине и реализуем акциюю товар в подарок
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['count_purchase'] = false;
                        }
                        $this->accumulation[$actionValue['action_id']][$actionValue['id']]['currency_id'] =  $actionValue['param']['currency'];
                        $this->accumulation[$actionValue['action_id']][$actionValue['id']]['user_id'] =  Yii::$app->user->id;
                    }

                    //var_dump($this->accumulation);
                }
                //не нашелся ничего не делаем
            }
        }

        return true;
        //$last_names = array_column($this->basketProducts, 'priceDiscount', 'variant_id');
        //print_r($last_names);
    }

    private function discontItem($keyItem=false, $discont=false, $currency=false){

        if((!$discont) || (!$currency) ){
            return false;
        }
        if(in_array($this->basketProducts[$keyItem]['variant_id'], self::IGNORED_VARIATION_IS)){
            return false;
        }

        if($currency==1){//снижаем цену в рублях
            $this->basketProducts[$keyItem]['priceDiscount'] = $this->basketProducts[$keyItem]['priceDiscount']-$discont;
            if($this->basketProducts[$keyItem]['priceDiscount']<0){
                $this->basketProducts[$keyItem]['priceDiscount']=0;
            }
        }
        elseif($currency==2) {//снижение цены в процентах
            $this->basketProducts[$keyItem]['priceDiscount'] = floor($this->basketProducts[$keyItem]['priceDiscount']*( 1-$discont/100));
            if($this->basketProducts[$keyItem]['priceDiscount'] <0){
                $this->basketProducts[$keyItem]['priceDiscount']=0;
            }
        }
        elseif($currency==3) {//снижение цены в в бонусах
            return false;// или зачислять в свободные бонусы
            //доюавляем поле pciseBonusDisont цена для скидки при покупке бонусами КРОИЛОВО
        }
        //print_R($this->basketProducts[$keyItem]);
    }

    private function cashBackItem($keyItem=false, $discont=false, $currency=false, $promo=false){
        if( (!$discont) || (!$currency)){
            return false;
        }
        if(in_array($this->basketProducts[$keyItem]['variant_id'], self::IGNORED_VARIATION_IS)){
            return false;
        }

        if($currency==1){//рубль
            $this->addRubl = $this->addRubl+$discont*$this->basketProducts[$keyItem]['count'];
            $this->basketProducts[$keyItem]['rublBack'] = $this->basketProducts[$keyItem]['rublBack']+$discont*$this->basketProducts[$keyItem]['count'];
        }
        elseif($currency==2) {//процент
            if($promo){
                $this->basketProducts[$keyItem]['fee'] = ceil(($this->basketProducts[$keyItem]['price'] - $this->basketProducts[$keyItem]['bonus'])*($discont/100));//*$this->basketProducts[$keyItem]['count'];
            }
            else{
                $this->addPercent = $this->addPercent +ceil($this->basketProducts[$keyItem]['price']*($discont/100))*$this->basketProducts[$keyItem]['count'];
                $this->basketProducts[$keyItem]['bonusBack'] = $this->basketProducts[$keyItem]['bonusBack']+ceil($this->basketProducts[$keyItem]['price']*($discont/100))*$this->basketProducts[$keyItem]['count'];
            }

        }
        elseif($currency==3) {//Бонус
            $this->addBonus = $this->addBonus + $discont*$this->basketProducts[$keyItem]['count'];
            $this->basketProducts[$keyItem]['bonusBack'] = $this->basketProducts[$keyItem]['bonusBack'] + $discont*$this->basketProducts[$keyItem]['count'];
        }
        return false;
    }

    private function chekingGroup($id=false, $accumalation=false){
        if(!$id){
            return false;
        }
        $this->calcPrice();

        $actionValue = ActionsParamsValue::find()->where(['id'=>$id])->with('param')->asArray()->one();
        if(!empty($actionValue)){
            if($accumalation){
                $accumVal = ActionsAccumulation::find()->select('user_id, action_param_value_id, SUM(current_value) as `current_value`, count(id) as `count_row`, sum(active) as `active_row`')
                    ->where(['user_id' => Yii::$app->user->id, 'action_param_value_id' => $actionValue['id'], 'status' => self::STATUS_ENABLE])
                    ->groupBy(['user_id', 'action_param_value_id'])
                    ->one();
                if (!empty($accumVal)) {
                    if($accumVal->count_row == $accumVal->active_row){
                        $basketPrice = $accumVal->current_value;//+$this->price;
                    }
                    else{
                        $basketPrice = -1;//$this->price;
                        //+деактивация существующих
                        $this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent'] =  1;
                    }
                }
                else {
                    $basketPrice = -1;//$this->price;
                }
                $curAction = Actions::find()->where(['id'=>$actionValue['action_id']])->asArray()->one();//сколько нужно накопить для того что бы заработала акция
                $basketConditionPrice = $curAction['accum_value'];
            }
            else{
                $basketConditionPrice = $actionValue['basket_price'];
                $basketPrice = $this->priceWithBonus;
            }


            if($basketPrice>=$basketConditionPrice) {//цена корзины устраивает
                if (!empty($actionValue['condition_value'])) {//если id есть то ищем по ID группы
                    $group_products = ShopGroupVariantLink::find()->where(['shop_group_id' => $actionValue['condition_value']])->asArray()->all();
                    foreach ($group_products as $group_product) {
                        foreach ($this->basketProducts as $key => $product) {
                            if ($product['product_id'] == $group_product['product_id']) {
                                if ($actionValue['param']['type'] == 1) {//скидка
                                    $this->discontItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                                }
                                elseif ($actionValue['param']['type'] == 2) {//зачисление бонусов
                                    $this->cashBackItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                                }
                            }
                        }
                    }
                }
                else {//скидка по всей корзине
                    foreach ($this->basketProducts as $key => $product) {
                        if ($actionValue['param']['type'] == 1) {//скидка
                            $this->discontItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                        }
                        elseif ($actionValue['param']['type'] == 2) {//зачисление бонусов
                            $this->cashBackItem($key, $actionValue['discont_value'], $actionValue['param']['currency']);
                        }

                    }
                }
                if($accumalation){
                    $this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent'] =  1;
                }
            }
            //else{
            //сохраняем накопления при любом раскладе
            if(($accumalation) && ($this->priceWithBonus>=$actionValue['basket_price'])){
                if($curAction['count_purchase']==1){
                    $this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $actionValue['basket_price'];//считаем по мин стоимости корзины //$this->priceWithBonus;//считаем стоимость по всей корзине и реализуем акциюю товар в подарок
                    $this->accumulation[$actionValue['action_id']][$actionValue['id']]['count_purchase'] = true;
                }
                else{
                    $this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $this->priceWithBonus;//считаем стоимость по всей корзине и реализуем акциюю товар в подарок
                    $this->accumulation[$actionValue['action_id']][$actionValue['id']]['count_purchase'] = false;
                }
                //$this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $this->priceWithBonus;
                $this->accumulation[$actionValue['action_id']][$actionValue['id']]['currency_id'] =  $actionValue['param']['currency'];
                $this->accumulation[$actionValue['action_id']][$actionValue['id']]['user_id'] =  Yii::$app->user->id;
            }
            //}
        }
        return false;
    }

    private function chekingDelivery($id=false, $accumalation=false){
        if(!$id){
            return false;
        }
        $this->calcPrice();
        //поиск по Доставке
        $actionValue = ActionsParamsValue::find()->where(['id'=>$id])->with('param')->asArray()->one();
        //print_r($actionValue);die();
        if(!empty($actionValue)) {
            //$this->calcPrice();//перерасчет
            $flag=false;
            foreach ($this->basketProducts as $key => $product) {
                if(in_array($product->variant->product->type_id, self::TYPE_GOOD)){
                    $flag = true;
                }
            }
            if($flag){
                if ($accumalation) {
                    $accumVal = ActionsAccumulation::find()->select('user_id, action_param_value_id, SUM(current_value) as `current_value`, count(id) as `count_row`, sum(active) as `active_row`')
                        ->where(['user_id' => Yii::$app->user->id, 'action_param_value_id' => $actionValue['id'], 'status' => self::STATUS_ENABLE])
                        ->groupBy(['user_id', 'action_param_value_id'])
                        ->one();
                    if (!empty($accumVal)) {
                        if($accumVal->count_row == $accumVal->active_row){
                            $basketPrice = $accumVal->current_value;//+$this->price;
                        }
                        else{
                            $basketPrice = -1;//$this->price;
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent'] =  1;//деактивация существующих
                        }
                    }
                    else {
                        $basketPrice = -1;//$this->price;
                    }
                    $curAction = Actions::find()->where(['id'=>$actionValue['action_id']])->asArray()->one();//сколько нужно накопить для того что бы заработала акция
                    $basketConditionPrice = $curAction['accum_value'];
                }
                else {
                    $basketConditionPrice = $actionValue['basket_price'];
                    $basketPrice = $this->priceWithBonus;
                }
                if ($basketPrice >= $basketConditionPrice) {//цена корзины устраивает $actionValue['basket_price']
                    if (!empty($actionValue['condition_value'])) {//если id есть то ищем по ID доставки хз как и что такое
                        return false;// логика aka корзина
                    }
                    else {
                        if ($actionValue['param']['type'] == 1) {//скидка
                            if ($actionValue['param']['currency'] == 1) {//Рубли
                                $this->basket->delivery_price = $this->basket->delivery_price - $actionValue['discont_value'];
                                if ($this->basket->delivery_price < 0) {
                                    $this->basket->delivery_price = 0;
                                }
                            }
                            elseif ($actionValue['param']['currency'] == 2) {//проценты
                                $this->basket->delivery_price = floor($this->basket->delivery_price * (1 - ($actionValue['discont_value'] / 100)));
                                if ($this->basket->delivery_price < 0) {
                                    $this->basket->delivery_price = 0;
                                }
                            }
                            elseif ($actionValue['param']['currency'] == 3) {//бонусы
                                return false;
                            }
                        }
                        else {//зачисление бонусов
                            return false;
                        }
                        if($accumalation){
                            $this->accumulation[$actionValue['action_id']][$actionValue['id']]['spent'] =  1;
                        }
                    }
                }
                //else{
                //сохраняем накопления при любом раскладе
                if(($accumalation) && ($this->priceWithBonus>=$actionValue['basket_price'])){
                    if($curAction['count_purchase']==1){
                        $this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $actionValue['basket_price'];//считаем по мин стоимости корзины //$this->priceWithBonus;//считаем стоимость по всей корзине и реализуем акциюю товар в подарок
                        $this->accumulation[$actionValue['action_id']][$actionValue['id']]['count_purchase'] = true;
                    }
                    else{
                        $this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $this->priceWithBonus;//считаем стоимость по всей корзине и реализуем акциюю товар в подарок
                        $this->accumulation[$actionValue['action_id']][$actionValue['id']]['count_purchase'] = false;
                    }
                    //$this->accumulation[$actionValue['action_id']][$actionValue['id']]['current_value'] = $this->priceWithBonus;
                    $this->accumulation[$actionValue['action_id']][$actionValue['id']]['currency_id'] = $actionValue['param']['currency'];
                    $this->accumulation[$actionValue['action_id']][$actionValue['id']]['user_id'] = Yii::$app->user->id;
                }
                //}
            }


        }
        return false;
    }

    private function CalcDiscountMoney($promocode){
        //echo '<pre>'.print_r($this->basketProducts,1).'</pre>';die;
        $discountMoney = $promocode->money_discount;
        $shop_id = $promocode->shop_id;
        $basketProducts = $this->basketProducts;
        foreach ($basketProducts as $key => $product){
            //if($product->variant->product->shop_id == $shop_id){
            if($product->variant->shop->id == $shop_id){
                if($discountMoney >= ($product->count * $product->priceDiscount)){
                    $discountMoney = $discountMoney - $product->count * $product->priceDiscount;
                    $product->priceDiscount = 0;
                }else{
                    $product->priceDiscount =  $product->priceDiscount - floor($discountMoney/$product->count);
                    $discountMoney = 0;
                }
            }
        }
    }

    private function CalcPromoCode(){ //расчет промокода
        //скидка и накопление по всем товарам корзины
        //$this->calcPrice();
        if(!empty($this->basket->promo_code_id)){
            $promocode = PromoCode::find()->where(['id'=>$this->basket->promo_code_id, 'status'=>self::STATUS_ENABLE])
                ->andWhere(['>=', 'count', self::STATUS_ENABLE])
                ->andWhere(['<', 'date_begin', Date('Y-m-d H:i:s', time())])
                ->andWhere(['>', 'date_end',   Date('Y-m-d H:i:s', time())])
                ->with('type')
                ->one();
            if((!empty($promocode)) && (!empty($promocode->type))){

                if(!empty($promocode->type->shop_id) && Yii::$app->user->id == $promocode->user_id){
                    $this->CalcDiscountMoney($promocode->type);
                }
                //перебираем элементы корзины
                if(!empty($promocode->type->max_sum_fee)){
                    $currentFee = $promocode->calcCashBackByCode();
                }


                foreach ($this->basketProducts as $key => $product) {
                    if($product->product->discount){
                        //var_dump($currentFee);
                        //if($product->product->category['id'] != self::GOODS_EF) {//skidka po promokodu na tovary EF 17.11.2016
                        if(in_array($product->product->type_id, self::SPORT_FOOD)){//для отдельной скидки на спортпит
                            $this->discontItem($key, $promocode->type->discount_sport, 2);
                        }
                        else{
                            $this->discontItem($key, $promocode->type->discount, 2);
                        }
                        //}
                        //echo $product['variant_id'] .' '. $product['priceDiscount'].'</br>';
                        if(!empty($promocode->type->max_sum_fee)) {
                            if ($currentFee < $promocode->type->max_sum_fee) {
                                if(in_array($product->product->type_id, self::SPORT_FOOD)){//для отдельного кэшбэка на спортпит
                                    $this->cashBackItem($key, $promocode->type->fee_sport, 2, true);
                                }
                                else{
                                    $this->cashBackItem($key, $promocode->type->fee, 2, true);
                                }

                                if (($currentFee + ($this->basketProducts[$key]['fee'] * $this->basketProducts[$key]['count'])) <= $promocode->type->max_sum_fee) {
                                    $currentFee = $currentFee + ($this->basketProducts[$key]['fee'] * $this->basketProducts[$key]['count']);
                                }
                                else {
                                    $this->basketProducts[$key]['fee'] = floor(($promocode->type->max_sum_fee - $currentFee) / $this->basketProducts[$key]['count']);
                                    $currentFee = $currentFee + ($this->basketProducts[$key]['fee'] * $this->basketProducts[$key]['count']);
                                }
                            }
                        }
                        else{
                            if(in_array($product->product->type_id, self::SPORT_FOOD)){//для отдельного кэшбэка на спортпит
                                $this->cashBackItem($key, $promocode->type->fee_sport, 2, true);
                            }
                            else{
                                $this->cashBackItem($key, $promocode->type->fee, 2, true);
                            }
                        }
                        //var_dump($currentFee);
                        //echo $product['variant_id'] .' '. $product['priceDiscount'].'</br>';
                    }
                }
            }
        }
        //print_R($this->basketProducts);
        return false;
    }

    private function calcPrice(){
        $this->price = 0;
        $this->priceWithBonus = 0;
        $this->priceFull =0;
        foreach($this->basketProducts as $key=> $prod) {
            //echo '</br>' . $this->freeBonus . 'fb</br>';
            //echo 'start price'.$this->basketProducts[$key]['price'] . '*</br>';
            //echo 'start priceDiscount' . $this->basketProducts[$key]['priceDiscount'] . '*</br>';
            $this->price = $this->price + $prod['priceDiscount'] * $prod['count'];//итоговая цена в рублях к оплате
            if(in_array($prod->variant->product->type_id, self::TYPE_GOOD)){
                $this->priceWithBonus = $this->priceWithBonus + ($prod['priceDiscount']+$prod['bonus']) * $prod['count'];//цена с бонусами
            }
            $this->priceFull = $this->priceFull + $prod['price'] * $prod['count'];//полная цена
        }
        /*
        echo 'calcprice</br>';
        foreach ($this->basketProducts as $key => $product) {
            echo $product['variant_id'] .' price=' . $product['price'] . ' discont='. $product['priceDiscount'] . ' bonus=' . $product['bonus'] . '</br>';
        }
        echo '</br>';
        echo 'Итог'.$this->price.'</br>';
        */
        //var_dump($this->price . '*'. $this->priceWithBonus. '*' . $this->priceFull);
        return false;
    }

    /*
    private function calcBonus(){// расчет траты бонусов перед акцией
        //сперва платим бонусами потом счситаем скидку по акциям на остаточную стоимость
        foreach($this->basketProducts as $key=> $prod) {
           расчет стоимости корзины
            if ($prod['stickers']['bonus'] == 1) {//можно применять бонусы
                $this->basketProducts[$key]['bonus'] = floor($this->freeBonus / $prod['count']);
                $this->freeBonus = $this->freeBonus % $prod['count'];
                $this->basketProducts[$key]['priceDiscount'] = $prod['price']- $this->basketProducts[$key]['bonus'];
            }
        }

        echo 'calcBonus</br>';
        foreach ($this->basketProducts as $key => $product) {
            echo $product['variant_id'] .' price=' . $product['price'] . ' discont='. $product['priceDiscount'] . ' bonus=' . $product['bonus'] . '</br>';
        }
        echo '</br>';

    }*/
}

