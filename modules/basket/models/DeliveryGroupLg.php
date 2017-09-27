<?php

namespace app\modules\basket\models;
use app\modules\catalog\models\GoodsTypes;
use app\modules\common\models\Zloradnij;

/**
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
class DeliveryGroupLg
{
    const MAX_DELIVERY_HOUR = 8;

    private $products = false;
    private $deliveryId = 1003;
    private $dayStart;
    private $dayCurrent;
    private $dayPlus;


    public $productDeliveryGroup = [
        'address_0' => [],
        'address_1' => [],
        'address_2' => [],

        'club_0' => [],
        'club_1' => [],
        'club_2' => [],

        'farFarWay_0' => [],
        'farFarWay_1' => [],
        'farFarWay_2' => [],
    ];

    public $deliveryGroup = [
        'address_0' => [1003,1005,1007],
        'address_1' => [1006],
        'address_2' => [1001],

        'club_0' => [1001,1005],
        'club_1' => [1003,1007],
        'club_2' => [1006],

        'farFarWay_0' => [1008],
        'farFarWay_1' => [1009],
        'farFarWay_2' => [1010],
    ];

    public $crazyTypeList = [
        'farFarWay_1' => -86400,
        'farFarWay_2' => -86400,
    ];
    public $crazyTypeListTwo = [
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
        if($this->deliveryId == 1003){
            if(empty($this->products)){

            }
            else{
                foreach ($this->products as $product) {
                    $product = $product->product;
                    if(in_array($product->type_id,$this->deliveryGroup['club_0'])){
                        $this->productDeliveryGroup['club_0'][] = $product->type_id;
                    }
                    elseif(in_array($product->type_id,$this->deliveryGroup['club_1'])){
                        $this->productDeliveryGroup['club_1'][] = $product->type_id;
                    }
                    elseif(in_array($product->type_id,$this->deliveryGroup['farFarWay_0'])){
                        $this->productDeliveryGroup['farFarWay_0'][] = $product->type_id;
                    }
                    elseif(in_array($product->type_id,$this->deliveryGroup['farFarWay_1'])){
                        $this->productDeliveryGroup['farFarWay_1'][] = $product->type_id;
                    }
                    elseif(in_array($product->type_id,$this->deliveryGroup['farFarWay_2'])){
                        $this->productDeliveryGroup['farFarWay_2'][] = $product->type_id;
                    }
                    else{
                        $this->productDeliveryGroup['club_2'][] = $product->type_id;
                    }
                }
            }
        }
        else{
            if(empty($this->products)){

            }
            else{
                foreach ($this->products as $product) {
                    $product = $product->product;
                    if(in_array($product->type_id,$this->deliveryGroup['address_0'])){
                        $this->productDeliveryGroup['address_0'][] = $product->type_id;
                    }
                    elseif(in_array($product->type_id,$this->deliveryGroup['address_1'])){
                        $this->productDeliveryGroup['address_1'][] = $product->type_id;
                    }
                    elseif(in_array($product->type_id,$this->deliveryGroup['farFarWay_0'])){
                        $this->productDeliveryGroup['farFarWay_0'][] = $product->type_id;
                    }
                    elseif(in_array($product->type_id,$this->deliveryGroup['farFarWay_1'])){
                        $this->productDeliveryGroup['farFarWay_1'][] = $product->type_id;
                    }
                    elseif(in_array($product->type_id,$this->deliveryGroup['farFarWay_2'])){
                        $this->productDeliveryGroup['farFarWay_2'][] = $product->type_id;
                    }
                    else{
                        $this->productDeliveryGroup['address_2'][] = $product->type_id;
                    }
                }
            }
        }
        foreach ($this->productDeliveryGroup as $i => $item) {
            if(empty($item)){
                unset($this->productDeliveryGroup[$i]);
            }
            else{
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

                case 'club_0':
                    $result = $this->getDateListClubZero();
                    break;
                case 'club_1':
                    $result = $this->getDateListClubOne();
                    break;
                case 'club_2':
                    $result = $this->getDateListClubTwo();
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

                case 'club_0':
                    $result = $this->getDateListClubZero();
                    break;
                case 'club_1':
                    $result = $this->getDateListClubOne();
                    break;
                case 'club_2':
                    $result = $this->getDateListClubZero();
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
            }
        }
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
                case 'club_1':
                case 'farFarWay_0':
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
                case 'club_0':
                case 'club_1':
                case 'club_2':
                case 'farFarWay_0':
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

    public function getDateListAddressZero(){
        $times = [];
        $blockList = [strtotime('04.11.2016')];

        for ($i = ((date("G") >= 8) ? 1 : 0); $i <= 10; $i++) {
            //  (date("G") >= 8) ? 1 : 0 - до восьми утра ещё можно сделать заказ на текущий день

            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));

            if(!in_array($day,$blockList)){
                for ($j = 6; $j >= 0; $j -= 2) {
                    $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.(12 + $j).':00:00'));

                    if((date("w", $time) > 0)){
                        //  Исключаем доставку в 18:00 в субботу
                        if (date("w", $time) == 6 && date("H:00", $time) == '18:00'){

                        }else{
                            $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + 7200);
                        }
                    }
                }
            }
        }

        return $times;
    }

    public function getDateListClubZero(){
        $times = [];
        $blockList = [strtotime('04.11.2016')];

        for ($i = ((date("G") >= 8) ? 1 : 0); $i <= 10; $i++) {
            //  (date("G") >= 8) ? 1 : 0 - до восьми утра ещё можно сделать заказ на текущий день

            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));

            if(!in_array($day,$blockList)){
                for ($j = 0; $j <= 6; $j += 2) {
                    $time = strtotime('+' . $i . ' day', strtotime(date('Y-m-d') . ' ' . (12 + $j) . ':00:00'));

                    if (strtotime(date('Y-m-d')) + 3600 * 24 < $day){
                        if ((date("w", $time) == 2 or date("w", $time) == 5) and $j >= 6) {
                            $times[$day][$time] = '8:00 - 22:00';
                        }
                    }
                }
            }
        }

        return $times;
    }

    public function getDateListClubOne(){
        $times = [];
        $blockList = [strtotime('04.11.2016')];

        for ($i = ((date("G") >= 8) ? 1 : 0); $i <= 10; $i++) {
            //  (date("G") >= 8) ? 1 : 0 - до восьми утра ещё можно сделать заказ на текущий день

            $day = strtotime('+'.$i.' day', strtotime(date('Y-m-d')));

            if(!in_array($day,$blockList)){
                for ($j = 0; $j <= 6; $j += 2) {
                    $time = strtotime('+'.$i.' day', strtotime(date('Y-m-d').' '.(12 + $j).':00:00'));

                    if((date("w", $time) > 0)){
                        //  Исключаем доставку в 18:00 в субботу
                        if (date("w", $time) == 6 && date("H:00", $time) == '18:00'){

                        }else{
                            $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + 7200);
                        }
                    }
                }
            }
        }

        return $times;
    }

    public function getDateListClubTwo(){
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

    public function getDateListFarFarWayZero(){
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

    public function getDateListFarFarWayOne(){
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
        $diapason = $this->deliveryId == 1006 ? '18:00 - 20:00':'8:00 - 22:00';
        $times = [];
        $currentTimeStamp = strtotime(date('Y-m-d H:i:s'));
        if ((date("w", $currentTimeStamp) > 1 && date("w", $currentTimeStamp) < 4) || (date("w", $currentTimeStamp) == 1 && date("Hi", $currentTimeStamp) >= 1300)) {
            $times[strtotime("next Thursday", strtotime("next Thursday"))][mktime(8,0,0, date("n",strtotime("next Thursday", strtotime("next Thursday"))), date("j",strtotime("next Thursday", strtotime("next Thursday"))), date("Y"))] = $diapason;
        }
        elseif((date("w", $currentTimeStamp) == 4 && date("Hi", $currentTimeStamp) <= 1300)){
            //$times[strtotime("next Thursday", strtotime("next Thursday"))][mktime(8,0,0, date("n",strtotime("next Thursday", strtotime("next Thursday"))), date("j",strtotime("next Thursday", strtotime("next Thursday"))), date("Y"))] = $diapason;
            $times[strtotime("next Thursday")][mktime(8,0,0, date("n",strtotime("next Thursday")), date("j",strtotime("next Thursday")), date("Y"))] = $diapason;
        }
        elseif((date("w", $currentTimeStamp) == 1 && date("Hi", $currentTimeStamp) <= 1300)){
            $times[strtotime("next Tuesday",strtotime("next Tuesday"))][mktime(8,0,0, date("n",strtotime("next Tuesday",strtotime("next Tuesday"))), date("j",strtotime("next Tuesday",strtotime("next Tuesday"))), date("Y"))] = $diapason;
        }
        else{
            $times[strtotime("next Tuesday",strtotime("next Tuesday"))][mktime(8,0,0, date("n",strtotime("next Tuesday",strtotime("next Tuesday"))), date("j",strtotime("next Tuesday",strtotime("next Tuesday"))), date("Y"))] = $diapason;
        }

        return $times;
    }

    public function getTimeWithDelta($group,$time){
        return in_array($group,$this->crazyTypeList) ? ($time + $this->crazyTypeList[$group]) : $time;
    }
    public function getTimeWithDeltaTwo($group,$time){
        return array_key_exists($group,$this->crazyTypeList) ? Date('Y-m-d H:i:s', (strtotime($time) + $this->crazyTypeListTwo[$group])) : $time;
    }
}
