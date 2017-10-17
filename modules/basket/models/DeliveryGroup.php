<?php

namespace app\modules\basket\models;
use app\modules\catalog\models\GoodsTypes;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\ShopStoresTimetable;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "basket".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $session_id
 * @property integer $last_update
 * @property integer $delivery_id
 * @property integer $delivery_price
 * @property integer $address_id
 * @property integer $payment_id
 * @property string $time_list
 * @property integer $status
 */
class DeliveryGroup
{
    const MAX_DELIVERY_HOUR = 8;
    private $products = false;
    private $deliveryId = 1003;
    private $dayStart;
    private $dayCurrent;
    private $dayPlus;
    private $availibleMinDayDelivery;


    public $productDeliveryGroup = [
        'address_0' => [],
        'address_1' => [],
        'address_2' => [],
        'address_3' => [],

        'club_0' => [],
        'club_1' => [],
        'club_2' => [],
        'club_3' => [],
        'club_4' => [],

        'farFarWay_0' => [],
        'farFarWay_1' => [],
        'farFarWay_2' => [],
        'farFarWay_3' => [],
    ];

    public $deliveryGroup = [
        'address_0' => [1003,1005,1007,1001, 1011,1014,],
        'address_1' => [1006],
        'address_2' => [1001],
        'address_3' => [1013],

        'club_0' => [1001,1005],
        'club_1' => [1003,1007, 1001,1014,],
        'club_2' => [1006],
        'club_3' => [1011],
        'club_4' => [1013],

        'farFarWay_0' => [1008],
        'farFarWay_1' => [1009],
        'farFarWay_2' => [1010,1014,],
        'farFarWay_3' => [1012,1014,],
    ];

    public $crazyTypeList = [
        'farFarWay_1' => -86400,
        'farFarWay_2' => -86400,
    ];
    public $crazyTypeListTwo = [
        'club_0' => 86400,
        'farFarWay_1' => 86400,
        'farFarWay_2' => 86400,
    ];


    public function setProducts($products){
        $this->products = $products;
    }

    public function setDeliveryId($deliveryId){
        $this->deliveryId = $deliveryId;
    }

    public function setProductDeliveryGroup(){
        $productsList = [];
        if(!empty($this->products)){
            foreach ($this->products as $product) {
                if(!empty($product->product->type_id))
                    $productsList[] = $product;
            }
        }
        if($this->deliveryId == 1003){
            if(empty($productsList)){

            }else{
                foreach ($productsList as $product) {
                    if(in_array($product->product->type_id,$this->deliveryGroup['club_0'])){
                        $this->productDeliveryGroup['club_0'][] = $product->product->type_id;
                    }
                    elseif(in_array($product->product->type_id,$this->deliveryGroup['club_1'])){
                        $this->productDeliveryGroup['club_1'][] = $product->product->type_id;
                    }
                    elseif(in_array($product->product->type_id,$this->deliveryGroup['club_3'])){ //TODO:: check
                        $this->productDeliveryGroup['club_3'][] = $product->product->type_id;
                    }
                    elseif(in_array($product->product->type_id,$this->deliveryGroup['club_4'])){ //TODO:: check
                        $this->productDeliveryGroup['club_4'][] = $product->product->type_id;
                    }
                    elseif(in_array($product->product->type_id,$this->deliveryGroup['farFarWay_0'])){
                        $this->productDeliveryGroup['farFarWay_0'][] = $product->product->type_id;
                    }
                    elseif(in_array($product->product->type_id,$this->deliveryGroup['farFarWay_1'])){
                        $this->productDeliveryGroup['farFarWay_1'][] = $product->product->type_id;
                    }
                    elseif(in_array($product->product->type_id,$this->deliveryGroup['farFarWay_2'])){
                        $this->productDeliveryGroup['farFarWay_2'][] = $product->product->type_id;
                    }
                    elseif(in_array($product->product->type_id,$this->deliveryGroup['farFarWay_3'])){
                        $this->productDeliveryGroup['farFarWay_3'][] = $product->product->type_id;
                    }
                    else{
                        $this->productDeliveryGroup['club_2'][] = $product->product->type_id;
                    }
                }
            }
        }else{
            if(empty($productsList)){

            }else{
                foreach ($productsList as $product) {
                    if(in_array($product->product->type_id,$this->deliveryGroup['address_0'])){
                        $this->productDeliveryGroup['address_0'][] = $product->product->type_id;
                    }elseif(in_array($product->product->type_id,$this->deliveryGroup['address_1'])){
                        $this->productDeliveryGroup['address_1'][] = $product->product->type_id;
                    }elseif(in_array($product->product->type_id,$this->deliveryGroup['address_3'])){
                        $this->productDeliveryGroup['address_3'][] = $product->product->type_id;
                    }elseif(in_array($product->product->type_id,$this->deliveryGroup['farFarWay_0'])){
                        $this->productDeliveryGroup['farFarWay_0'][] = $product->product->type_id;
                    }elseif(in_array($product->product->type_id,$this->deliveryGroup['farFarWay_1'])){
                        $this->productDeliveryGroup['farFarWay_1'][] = $product->product->type_id;
                    }elseif(in_array($product->product->type_id,$this->deliveryGroup['farFarWay_2'])){
                        $this->productDeliveryGroup['farFarWay_2'][] = $product->product->type_id;
                    }elseif(in_array($product->product->type_id,$this->deliveryGroup['farFarWay_3'])){
                        $this->productDeliveryGroup['farFarWay_3'][] = $product->product->type_id;
                    }else{
                        $this->productDeliveryGroup['address_2'][] = $product->product->type_id;
                    }
                }
            }
        }
        foreach ($this->productDeliveryGroup as $i => $item) {
            if(empty($item)){
                unset($this->productDeliveryGroup[$i]);
            }else{
                $this->productDeliveryGroup[$i] = array_unique($this->productDeliveryGroup[$i]);
            }
        }
    }

    public function getDeliveryGroupTitle(){
        $result = false;
        if(!$this->productDeliveryGroup){

        }else{
            foreach ($this->productDeliveryGroup as $key => $items) {
                if(!empty($items)){
                    $result[$key] = implode(', ',GoodsTypes::find()->where(['IN','id',$items])->select(['name'])->column());
                }
            }
        }
        return $result;
    }

    public function getDateList($key){
        $result = false;
        if($this->deliveryId == 1003){
            switch ($key){
                case 'address_0':
                    $result = $this->getDateListClubZero();
                    break;
                case 'address_1':
                    $result = $this->getDateListClubZero();
                    break;
                case 'address_2':
                    $result = $this->getDateListClubZero();
                    break;
                case 'address_3':
                    $result = $this->getDateListAddressThree();
                    break;


                case 'club_0':
                    $result = $this->getDateListClubZero();
                    break;
                case 'club_1':
                    $result = $this->getDateListClubOne();
                    break;
                case 'club_2':
                    $result = $this->getDateListClubTwo();
                    break;
                case 'club_3':
                    $result = $this->getDateListClubThree();
                    break;
                case 'club_4':
                    $result = $this->getDateListAddressThree();
                    break;

                case 'farFarWay_0':
                    $result = $this->getDateListAddressZero();
                    break;
                case 'farFarWay_1':
                    $result = $this->getDateListFarFarWayOne();
                    break;
                case 'farFarWay_2':
                    $result = $this->getDateListFarFarWayTwo();
                    break;
                case 'farFarWay_3':
                    $result = $this->getDateListFarFarWayThree();
                    break;
            }
        }else{
            switch ($key){
                case 'address_0':
                    $result = $this->getDateListAddressZero();
                    break;
                case 'address_1':
                    $result = $this->getDateListClubTwo();
                    break;
                case 'address_2':
                    $result = $this->getDateListAddressZero();
                    break;
                case 'address_3':
                    $result = $this->getDateListAddressThree();
                    break;

                case 'club_0':
                    $result = $this->getDateListClubZero();
                    break;
                case 'club_1':
                    $result = $this->getDateListClubOne();
                    break;
                case 'club_2':
                    $result = $this->getDateListClubZero();
                    break;
                case 'club_3':
                    $result = $this->getDateListClubZero();
                    break;
                case 'club_4':
                    $result = $this->getDateListAddressThree();
                    break;

                case 'farFarWay_0':
                    $result = $this->getDateListAddressZero();
                    break;
                case 'farFarWay_1':
                    $result = $this->getDateListFarFarWayOne();
                    break;
                case 'farFarWay_2':
                    $result = $this->getDateListFarFarWayTwo();
                    break;
                case 'farFarWay_3':
                    $result = $this->getDateListFarFarWayThree();
                    break;
            }
        }
//        foreach ($result as $key => $val){
//            krsort($result[$key], SORT_REGULAR);
//        }
        /*
        if(!empty($result[strtotime('04.11.2016')])){
            unset($result[strtotime('04.11.2016')]);
        }*/
        return $result;
    }

    public function getAddressForBase($key,$addressId){
        return $key == 'farFarWay_0' ? 10000205 : $addressId;
    }

    public function getDeliveryForBase($key,$deliveryId){
        return $key == 'farFarWay_0' ? 1003 : $deliveryId;
    }

    public function getDateForBase($key,$time){
        $result = $time;
        if($this->deliveryId == 1003){
            switch ($key){
                case 'address_0':
                case 'address_1':
                case 'address_2':
                case 'address_3':
                case 'club_1':
                case 'farFarWay_0':
                case 'farFarWay_3':
                    $result = $time;
                    break;

                case 'club_0':
                case 'club_2':
                case 'farFarWay_1':
                case 'farFarWay_2':
                    $result = $time - 3600*24;
                    break;
            }
        }else{
            switch ($key){
                case 'address_0':
                case 'address_1':
                case 'address_2':
                case 'address_3':
                case 'club_0':
                case 'club_1':
                case 'club_2':
                case 'club_3':
                case 'farFarWay_0':
                case 'farFarWay_3':
                    $result = $time;
                    break;
                case 'farFarWay_1':
                case 'farFarWay_2':
                    $result = $time - 3600*24;
                    break;
            }
        }
        return $result;
    }

    public function getDateListFastOrder(){
        $times = [];
        $blockList = Yii::$app->params['blockList'];
        for ($i = 0; $i <= 5; $i++) {
            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
            if(!in_array($day,$blockList)){
                $maxTime = 13;
                $startTime = 8;
                if(date("w", $day) == 0){
                    $maxTime = 6;
                    $startTime = 10;
                }

                for ($j = 0; $j <= $maxTime; $j += 1) {
                    $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.($startTime + $j).':00:00'));
                    if((date("w", $time) != 6)){
                        if($day == strtotime('midnight') && date("H",$time) < date('H')+2){
                            continue;
                        }
                        $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + 3600);
                    }
                }
            }
        }

        return $times;
    }

    public function getDateListAddressZero(){
        if($this->checkFastOrderProduct()){
            $times = [];
            $blockList = Yii::$app->params['blockList'];

            $j =0;
            $z =2;

//            if(date('H') >= 20){
//                $j = 1;
//                $z =  2;
//            }
            //closed delivery on saturday
            //closed delivery on sunday  Date('w', strtotime('+'.$j.' day', time())) ==0
            while(Date('w', strtotime('+'.$j.' day', time())) ==6 || Date('w', strtotime('+'.$j.' day', time())) ==0){
                $j++;
                $z++;
            }

            for ($i = $j; $i < $z; $i++) {
                $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
                if(!in_array($day,$blockList)){
                    $maxTime = 20;
                    $startTime = 8;
                    $period = 3600;
                    if(date("w", $day) == 0){
                        $maxTime = 3;
                        $startTime = 10;
                        $period = 3600 * 3;
                    }

                    for ($j = $startTime; $j <= $maxTime; $j += 1) {
                        $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.($j).':00:00'));
//                        if((date("w", $time) != 6)){
                            if($day == strtotime('midnight') && date("H",$time) < date('H')+2){
                                continue;
                            }
                            $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + $period);
//                        }
                    }
                }
            }

            return $times;
        }
        $times = [];
        $blockList = Yii::$app->params['blockList'];

        //Заказ в воскресение на воскресение до 12-00 принимается
        if( date("w") == 0 && date("G") < 10){
            $a = 0;
        }
        else{
            $a = ((date("G") >= 8) ? 1 : 0);
        }

        for ($i = $a; $i <= 10; $i++) {
            //  (date("G") >= 8) ? 1 : 0 - до восьми утра ещё можно сделать заказ на текущий день

            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));

            if(!in_array($day,$blockList) && $day >= $this->availibleMinDayDelivery){
                $maxTime = self::MAX_DELIVERY_HOUR;
                $startTime = 12;
                if(date("w", $day) == 0){
                    $maxTime = 5;
                    $startTime = 15;
                }
                for ($j = 0; $j <= $maxTime; $j += 3) {

                    $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.($startTime + $j).':00:00'));

                    //if((date("w", $time) > 0)){
                    if((date("w", $time) != 6)){
                        //  Исключаем доставку в 18:00 в субботу
                        //if (date("w", $time) == 6 && date("H:00", $time) == '18:00'){
                        $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + 10800);
                    }

                }
            }
        }
        return $times;
    }
    public function getDateListAddressOne(){
        $blockList = Yii::$app->params['blockList'];
        $times = [];
        $i=2;
        while(empty($times)){
            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
            if((!in_array($day,$blockList)) && (date("w", $day) != 0) && (date("w", $day) != 6)){//день доставки не выходной и не воскресение
                $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' 08:00:00'));
                $times[$day][$time] = '8:00 - 22:00';
            }
            else{
                $i++;
            }
        }

        //$this->saveDefaultDeliveryTime($day, $time,'address_1');

        return $times;
    }
    public function getDateListAddressThree(){
        $times = [];
        $blockList = Yii::$app->params['blockList'];

        $i=7;
        while(empty($times)){
            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
            if((!in_array($day,$blockList)) && (date("w", $day) != 0) && (date("w", $day) != 6) && (date("w", $day) != 5)) {//день доставки не выходной и не воскресение и не суббота
                for ($j = 0; $j <= self::MAX_DELIVERY_HOUR; $j += 3){
                    $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.(12 + $j).':00:00'));
                    if((date("w", $time) > 0)){
                        //  Исключаем доставку в 18:00 в субботу
                        if (date("w", $time) == 6 && date("H:00", $time) == '18:00'){

                        }
                        else{
                            $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + 10800);
                        }
                    }
                }
            }
            else{
                $i++;
            }

        }

        return $times;
    }

    public function getDateListClubZero(){
        $times = [];
        $blockList = Yii::$app->params['blockList'];
        //$this->checkFastOrderProduct();

        for ($i = ((date("G") >= 8) ? 1 : 0); $i <= 10; $i++) {
            //  (date("G") >= 8) ? 1 : 0 - до восьми утра ещё можно сделать заказ на текущий день

            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));

            if(!in_array($day,$blockList)  && $day >= $this->availibleMinDayDelivery){
                //for ($j = 0; $j <= self::MAX_DELIVERY_HOUR; $j += 2) {
                //$time = strtotime('+' . $i . ' day', strtotime(date('Y-m-d') . ' ' . (12 + $j) . ':00:00'));
                $time = strtotime('+' . $i . ' day', strtotime(date('Y-m-d') . ' ' . 8 . ':00:00'));

                if (strtotime(date('Y-m-d')) + 3600 * 24 < $day){
                    if ((date("w", $time) == 2 or date("w", $time) == 4) ){//and $j >= 6) {
                        $times[$day][$time] = '8:00 - 22:00';
                    }
                }
                //}
            }
        }
        return $times;
    }

    private function checkFastOrderProduct(){
        /*foreach ($this->products as $product){
            //echo $product->product->type_id;
            if($product->product->type_id != 1014){
                return false;
            }
        }
        return true;*/
        $result = true;
        /*//rabotalo
        foreach ($this->products as $product){
            //echo $product->product->type_id;
            if($product->product->type_id != 1014 && $product->product->type_id != 1011 && $product->product->type_id != 1001){
                $result =  false;
                break;
            }
        }*/

        /*проверка на то что  сегодня ен суббота и не воскресение и застра не то и не жругое*/
        /*
        if(date('H') >= 20){
            if(Date('w', strtotime('1 day', time() ) ==6) || Date('w', strtotime('1 day', time() ) ==0)){
                $result = false;
            }
        }
        else{
            if((Date('w') ==6) || (Date('w') ==0)){
                $result = false;
            }
        }*/

        return $result;

    }

    public function getDateListClubOne(){
        if($this->checkFastOrderProduct()){
            $times = [];
            $blockList = Yii::$app->params['blockList'];
            $j =0;
            $z =2;
//            if(date('H') >= 20){
//                $j = 1;
//                $z =  2;
//            }
            //closed delivery on saturday
            //closed delivery on sunday  Date('w', strtotime('+'.$j.' day', time())) ==0
            while(Date('w', strtotime('+'.$j.' day', time())) ==6 || Date('w', strtotime('+'.$j.' day', time())) ==0){
                $j++;
                $z++;
            }
            for ($i = $j; $i < $z; $i++) {
                $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
                if(!in_array($day,$blockList)){
                    $maxTime = 20;
                    $startTime = 8;
                    $period = 3600;
                    if(date("w", $day) == 0){
                        $maxTime = 3;
                        $startTime = 12;
                        $period = 3600 * 3;
                    }

                    for ($j = $startTime; $j <= $maxTime; $j += 1) {
                        $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.($j).':00:00'));
//                        if((date("w", $time) != 6)){
                        if($day == strtotime('midnight') && date("H",$time) < date('H')+2){
                            continue;
                        }
                        $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + $period);
//                        }
                    }
                }
            }

            return $times;
        }
        $times = [];
        $blockList = Yii::$app->params['blockList'];

        //Заказ в воскресение на воскресение до 12-00 принимается
        if( date("w") == 0 && date("G") < 10){
            $a = 0;
        }else{
            $a = ((date("G") >= 8) ? 1 : 0);
        }

        for ($i = $a; $i <= 10; $i++) {
            //  (date("G") >= 8) ? 1 : 0 - до восьми утра ещё можно сделать заказ на текущий день

            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));

            if(!in_array($day,$blockList)  && $day >= $this->availibleMinDayDelivery){
                $maxTime = self::MAX_DELIVERY_HOUR;
                $startTime = 12;
                if(date("w", $day) == 0){
                    $maxTime = 5;
                    $startTime = 15;
                }
                for ($j = 0; $j <= $maxTime; $j += 3) {
                    $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.($startTime + $j).':00:00'));

//                    if((date("w", $time) > 0)){
//                        //  Исключаем доставку в 18:00 в субботу
//                        if (date("w", $time) == 6 && date("H:00", $time) == '18:00'){
//
//                        }else{
                    if((date("w", $time) != 6)){
                        $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + 10800);
                        //}
                    }
                }
            }
        }

        return $times;
    }

    public function getDateListClubTwo(){//NO GOODS TYPES
        /*$blockList = Yii::$app->params['blockList'];
        $this->dayStart = strtotime('07-07-2016');
        $this->dayCurrent = strtotime(date('d-m-Y'));
        $this->dayPlus = 0;
        if($this->dayStart > $this->dayCurrent){
            $this->dayPlus = ($this->dayStart - $this->dayCurrent) / 86400;
        }
        for ($i = ((date("G") >= 6) ? 1 : 0); $i <= (15 + $this->dayPlus); $i++) {
            //  (date("G") >= 6) ? 1 : 0 - до шести утра ещё можно сделать заказ на текущий день

            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
            if(!in_array($day,$blockList)  && $day >= $this->availibleMinDayDelivery){
                $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' 8:00:00'));

                if($this->dayStart <= $day){
                    if (date("w", $day) == 5){
                        //  Если пятница
                        if(empty($times[$day]))
                            $times[$day][$time] = '8:00 - 22:00';
                    }
                }
            }
        }

        return $times;*/
        $blockList=[];
        $times = [];
        $i=2;
        while(empty($times)){
            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
            if((!in_array($day,$blockList)) && (date("w", $day) != 0) && (date("w", $day) != 6)){//день доставки не выходной и не воскресение
                $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' 08:00:00'));
                $times[$day][$time] = '8:00 - 22:00';
            }
            else{
                $i++;
            }
        }

        //$this->saveDefaultDeliveryTime($day, $time, 'club_2');
        return $times;
    }

    public function getDateListClubThree(){

        $times = [];
        $blockList = Yii::$app->params['blockList'];

        for ($i = 0; $i <= 10; $i++) {
            //  (date("G") >= 8) ? 1 : 0 - до восьми утра ещё можно сделать заказ на текущий день


            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));

            if(!in_array($day,$blockList)  && $day >= $this->availibleMinDayDelivery){
                $maxTime = self::MAX_DELIVERY_HOUR;
                $startTime = 12;
                if(date("w", $day) == 0){
                    $maxTime = 5;
                    $startTime = 15;
                }
                for ($j = 0; $j <= $maxTime; $j += 3) {
                    $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.($startTime + $j).':00:00'));

//                    if((date("w", $time) > 0)){
//                        //  Исключаем доставку в 18:00 в субботу
//                        if (date("w", $time) == 6 && date("H:00", $time) == '18:00'){
//
//                        }else{
                    if((date("w", $time) != 6)){
                        $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + 10800);
                    }
//                    }
                }
            }
        }

        return $times;
    }

    public function getDateListFarFarWayZero(){//not used
        $this->dayStart = strtotime('07-07-2016');
        $this->dayCurrent = strtotime(date('d-m-Y'));
        $this->dayPlus = 0;
        if($this->dayStart > $this->dayCurrent){
            $this->dayPlus = ($this->dayStart - $this->dayCurrent) / 86400;
        }
        for ($i = ((date("G") >= 6) ? 1 : 0); $i <= (15 + $this->dayPlus); $i++) {
            //  (date("G") >= 6) ? 1 : 0 - до шести утра ещё можно сделать заказ на текущий день

            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
            $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' 8:00:00'));

            if($this->dayStart <= $day){
                if (date("w", $day) == 5){
                    //  Если пятница
                    if(empty($times[$day]))
                        $times[$day][$time] = '8:00 - 22:00';
                }
            }
        }

        return $times;
    }

    public function getDateListFarFarWayOne(){//NO GOODS TYPES
        $times = [];
        $currentTimeStamp = time();
        if ((date("w", $currentTimeStamp) > 2 && date("w", $currentTimeStamp) < 5) || (date("w", $currentTimeStamp) == 2 && date("Hi", $currentTimeStamp) >= 1400) || ( date("w", $currentTimeStamp) == 5 && date("Hi", $currentTimeStamp) <= 1400)) {
            $times[strtotime("next Tuesday")][mktime(8,0,0, date("n",strtotime("next Tuesday")), date("j",strtotime("next Tuesday")), date("Y"))] = '8:00 - 22:00';
        }else{
            $times[strtotime("next Tuesday")][mktime(8,0,0, date("n",strtotime("next Friday")), date("j",strtotime("next Friday")), date("Y"))] = '8:00 - 22:00';
        }

        return $times;
    }

    public function getDateListFarFarWayTwo(){
        // old calc dilevery time
        $blockList = Yii::$app->params['blockList'];
        $diapason = $this->deliveryId == 1006 ? '18:00 - 20:00':'8:00 - 22:00';
        $times = [];
        $currentTimeStamp = strtotime(date('Y-m-d H:i:s'));
        $delta = $this->calcDateListFarFarWayTwo($currentTimeStamp);

        while (in_array($delta, $blockList) || $delta< $this->availibleMinDayDelivery){
            $currentTimeStamp = $currentTimeStamp + 86400;
            $delta = $this->calcDateListFarFarWayTwo($currentTimeStamp);
        }
        if(time() < strtotime('29.12.2016')){
            $times[strtotime('10.01.2017')][mktime(8,0,0, date("n",strtotime('10.01.2017')), date("j",strtotime('10.01.2017')), date("Y",strtotime('10.01.2017')))] = $diapason;
        }
        $times[$delta][mktime(8,0,0, date("n",$delta), date("j",$delta), date("Y",$delta))] = $diapason;

        /*old old delivery time
         * if ((date("w", $currentTimeStamp) > 1 && date("w", $currentTimeStamp) < 4) || (date("w", $currentTimeStamp) == 1 && date("Hi", $currentTimeStamp) >= 1300)) {
            $times[strtotime("next Thursday", strtotime("next Thursday"))][mktime(8,0,0, date("n",strtotime("next Thursday", strtotime("next Thursday"))), date("j",strtotime("next Thursday", strtotime("next Thursday"))), date("Y"))] = $diapason;
        }
        elseif((date("w", $currentTimeStamp) == 4 && date("Hi", $currentTimeStamp) <= 1300)){
            $times[strtotime("next Thursday")][mktime(8,0,0, date("n",strtotime("next Thursday")), date("j",strtotime("next Thursday")), date("Y"))] = $diapason;
        }
        elseif((date("w", $currentTimeStamp) == 1 && date("Hi", $currentTimeStamp) <= 1300)){
            $times[strtotime("next Tuesday",strtotime("next Tuesday"))][mktime(8,0,0, date("n",strtotime("next Tuesday",strtotime("next Tuesday"))), date("j",strtotime("next Tuesday",strtotime("next Tuesday"))), date("Y"))] = $diapason;
        }
        else{
            $times[strtotime("next Tuesday",strtotime("next Tuesday"))][mktime(8,0,0, date("n",strtotime("next Tuesday",strtotime("next Tuesday"))), date("j",strtotime("next Tuesday",strtotime("next Tuesday"))), date("Y"))] = $diapason;
        }*/
        /*new calc time
         * $times = [];
        $blockList = [];

        for ($i = ((date("G") >= 8) ? 2 : 1); $i <= 3; $i++) {
            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
            if(!in_array($day,$blockList)){
                $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' 08:00:00'));
                if((date("w", $time) > 0)){
                    $times[$day][$time] = '8:00 - 22:00';
                }
            }
        }*/
        return $times;
    }

    public function getTimeWithDelta($group,$time){
        return in_array($group,$this->crazyTypeList) ? ($time + $this->crazyTypeList[$group]) : $time;
    }

    public function getTimeWithDeltaTwo($group,$time){
        return array_key_exists($group,$this->crazyTypeListTwo) ? Date('Y-m-d H:i:s', (strtotime($time) + $this->crazyTypeListTwo[$group])) : $time;
    }

    private function calcDateListFarFarWayTwo($currentTimeStamp){
        //if ((date("w", $currentTimeStamp) > 1 && date("w", $currentTimeStamp) < 4) || (date("w", $currentTimeStamp) == 1 && date("Hi", $currentTimeStamp) >= 1300)) {
        if ((date("w", $currentTimeStamp) > 1 && date("w", $currentTimeStamp) < 3) || (date("w", $currentTimeStamp) == 1 && date("Hi", $currentTimeStamp) >= 1300)) {
            //$delta=strtotime("next Thursday", strtotime("next Thursday", $currentTimeStamp));
            $delta=strtotime("next Thursday", strtotime("next Thursday", $currentTimeStamp));
        }
        elseif((date("w", $currentTimeStamp) == 3 && date("Hi", $currentTimeStamp) <= 1300)){
            //elseif((date("w", $currentTimeStamp) == 3 && date("Hi", $currentTimeStamp) <= 1300)){
            //$delta = strtotime("next Thursday", $currentTimeStamp);
            $delta = strtotime("next Thursday", strtotime("next Thursday", $currentTimeStamp));
        }
        elseif((date("w", $currentTimeStamp) == 1 && date("Hi", $currentTimeStamp) <= 1300)){
            $delta = strtotime("next Tuesday",strtotime("next Tuesday", $currentTimeStamp));
        }
        else{
            $delta = strtotime("next Tuesday",strtotime("next Tuesday", $currentTimeStamp));
        }
        return $delta;
    }

    public function getDateListFarFarWayThree(){
        // old calc dilevery time
        $blockList = Yii::$app->params['blockList'];
        $times = [];
        $i=2;
        while(empty($times)){
            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));
            if((!in_array($day,$blockList)) && (date("w", $day) != 0) && (date("w", $day) != 6)){//день доставки не выходной и не воскресение
                $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' 08:00:00'));
                $times[$day][$time] = '8:00 - 22:00';
            }
            else{
                $i++;
            }
        }

        $this->saveDefaultDeliveryTime($day, $time);
        return $times;
    }

    private function saveDefaultDeliveryTime($day, $time, $name='farFarWay_3'){
        $basket = \Yii::$app->controller->basket;
        $timeToBasket = !empty($basket->time_list) ? $basket->time_list : '';
        $timeToBasket = json_decode($timeToBasket,true);
        $timeToBasket[$name]['day'] = $day;
        $timeToBasket[$name]['time'] = $time;

        foreach ($timeToBasket as $ket => $item) {
            if(empty($item['day']) || empty($item['time'])){
                unset($timeToBasket[$ket]);
            }
        }
        $basket->time_list = json_encode($timeToBasket);
        if(!$basket->save()){
            return false;
        }
        return true;
    }

    public function setMinDayDelivery(){
//        if(date('h')<8){
        $minday = strtotime("midnight"); //начало текущего дня
//        }else{
//            $minday = strtotime("midnight") + 60*60*24; //начало завтрашнего дня
//        }
        $curTime = date('H:i');
        $arStoreId = []; //Массив ID складов
        foreach ($this->products as $product){
            if($product->store ){
                if(ShopStoresTimetable::find()->where(['store_id'=>$product->store->id,'status'=>1,])->count()>0) {
                    if (!in_array($product->store->id, $arStoreId)) { // Наполняем массив ID складов, уникальными значениями
                        $arStoreId[] = $product->store->id;
                    }
                }
            }
        }
        foreach ($this->products as $product){ //Перебираем продукты из корзины
            if($product->store ) {
                $startTimestampWeek = strtotime("midnight") - date("N") * 60 * 60 * 24; //По умолчанию работаем с текущей неделей
                $store_id = $product->store->id; //ИД склада поставщика
                $storeTimetable = ShopStoresTimetable::find()->where(['store_id' => $store_id, 'status' => 1])->All();// Рассписание работы склада
                if ($storeTimetable) {
                    if (ShopStoresTimetable::find()->where(['store_id' => $store_id, 'status' => 1])->asArray()->max('day') < date("N")) { //Если максимальный день работы склада меньше текущего дня, работаем со след.недели
                        $startTimestampWeek = $startTimestampWeek + 7 * 24 * 60 * 60;
                    }
                    foreach ($storeTimetable as $workDay) {//Перебираем рабочие дни недели
                        $checkingDay = $startTimestampWeek + $workDay->day * 60 * 60 * 24; //Вычислем timestamp начала рабочего склада
                        if(strtotime('now') > strtotime(date('Y-m-d '.$workDay->time_end.':00')) && $workDay->day == date('N') && $minday == strtotime('midnight')){
                            //echo '<br>fucking time 1<br>';
                            $minday = $minday + 24*60*60*2;
                        }elseif(strtotime('now') <  strtotime(date('Y-m-d '.$workDay->time_begin.':00')) && $workDay->day == date('N') && $minday == strtotime('midnight')){
                            //echo '<br>fucking time 2<br>';
                            $minday = $minday + 24*60*60;
                        }
                        //echo date('d-m-Y H:i:s',$minday).'<br>';
                        if ($checkingDay >= $minday) {//если рабочий день магазина совпадает или больше текущего минимального дня, то заменяем на проверяемую дату
                            if (ShopStoresTimetable::find()->where(['IN', 'store_id', $arStoreId])->andWhere(['status' => 1, 'day' => date('N', $checkingDay)])->count() == count($arStoreId)) { //Проверяем, работают ли остальные поставщики в этот день
                                $minday = $checkingDay;
                                break;
                            }
                        }
                    }
                    //echo date("d-m-Y",$minday).'|';
                }
            }
        }
        //var_dump($arStoreId);
        $this->availibleMinDayDelivery =  $minday;
    }

    public function getUpdatedDteList($dateList){
        if(!$this->checkFastOrderProduct()) {
            //echo '<pre>'.print_r($dateList,1).'</pre>';
            $arStoreId = []; //Массив ID складов
            foreach ($this->products as $product) {
                if ($product->store) {
                    if (!in_array($product->store->id, $arStoreId)) { // Наполняем массив ID складов, уникальными значениями
                        if (ShopStoresTimetable::find()->where(['store_id' => $product->store->id, 'status' => 1,])->count() > 0) {
                            $arStoreId[] = $product->store->id;
                        }

                    }
                }
            }
            $newDataList = [];

            foreach ($dateList as $key => $value) {

                if (ShopStoresTimetable::find()->where(['IN', 'store_id', $arStoreId])->andWhere(['status' => 1, 'day' => date('N', $key)])->count() == count($arStoreId)) { //Проверяем, работают ли остальные поставщики в этот день
                    $newDataList[$key] = $value;
                }
            }
            return $newDataList;
        }

        return $dateList;
    }
}
