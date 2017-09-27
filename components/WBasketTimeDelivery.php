<?php

namespace app\components;

use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Widget;

class WBasketTimeDelivery extends Widget
{
    public $basket;
    public $sort;
    public $time = false;

    public function init()
    {
        parent::init();
        if ($this->basket === null) {
            return false;
        }
        if($this->sort === null){
            $this->sort = 2;
        }

    }

    public function run(){
        $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
        $deliveryGroup->setProducts(Yii::$app->basket->getBasketProducts());
        $deliveryGroup->setDeliveryId($this->basket->delivery_id);
        $deliveryGroup->setProductDeliveryGroup();
        //$deliveryGroup->setMinDayDelivery();
        $deliveryGroupTitle = $deliveryGroup->getDeliveryGroupTitle();
        if(empty($this->basket->time_list)){
            $this->basket->time_list = '';
        }
        $basketTimeList = json_decode($this->basket->time_list,true);

        foreach ($deliveryGroup->productDeliveryGroup as $key => $productDeliveryGroup) {
            $dateList = $deliveryGroup->getDateList($key);//получаем список дат и времени
            reset($dateList);
            $this->time = current($dateList);
            $basketTimeList = Yii::$app->basket->getDeliveryList();
            if(!empty($basketTimeList[$key]['day'])){
                if(empty($dateList[$basketTimeList[$key]['day']])){
                    unset($basketTimeList[$key]['day']);
                }
                else{
                    $this->time = $dateList[$basketTimeList[$key]['day']];
                }
            }
            //$dateList = $deliveryGroup->getUpdatedDteList($dateList);


//            echo '<pre>'.print_r($dateList,1).'</pre>';
            ?>

            <div class="times" id="basket-page-time">
                <div class="time-item" rel="+">
                    <div class="description-normal"><?=\Yii::t('app','Дата и время доставки для');?> <b><?= $deliveryGroupTitle[$key]?></b></div><?php
                    foreach($productDeliveryGroup as $id){?>
                        <input class="timeForProductType" type="hidden" data-type="<?= $id?>" value="<?= key($this->time)?>" /><?php
                    }

                    if(in_array(Yii::$app->params['blockedSelectDeliveryTimeProductGroup'],$productDeliveryGroup)){
                        print Yii::$app->params['blockedSelectDeliveryTimeProductText'];
                    }
                    else{?>
                        <!--Дата-->
                        <div class="form_block block_inline form-date-flag-block date">
                            <div class="select__form min time_select">
                                <div class="container-select"><div class="option-text"><?= (!empty($basketTimeList[$key]['day'])) ? date('d.m.Y',$basketTimeList[$key]['day']) : Yii::t('app','Выберите дату доставки')?></div><div class="selectbox"></div></div>
                                <div class="row"><?php

                                    foreach($dateList as $day => $times) {?>
                                        <div
                                            class="option date-variation<?= (!empty($basketTimeList[$key]['day']) && $basketTimeList[$key]['day'] == $day) ? ' selected' : ''?>"
                                            data-day="<?= $day?>"
                                            data-product-group-key="<?= $key?>"
                                        ><?= date('d.m.Y',$day)?></div><?php
                                    }?>
                                </div>
                            </div>
                        </div> <!--./Дата-->

                        <?php

                        if(!empty($basketTimeList[$key]['day'])){?>
                            <div class="form_block block_inline time">
                                <div class="select__form min time_select">
                                    <div class="container-select"><div class="option-text"><?= (!empty($basketTimeList[$key]['time']) && !empty($this->time[$basketTimeList[$key]['time']])) ? $this->time[$basketTimeList[$key]['time']] : Yii::t('app','Выберите время доставки')?></div><div class="selectbox"></div></div>
                                    <div class="row"><?php
                                        foreach($this->time as $time_key => $time_value) {?>
                                            <div
                                            class="option time-variation<?= (!empty($basketTimeList[$key]['time']) && $basketTimeList[$key]['time'] == $time_key) ? ' selected' : ''?>"
                                            data-day="<?= $basketTimeList[$key]['day']?>"
                                            data-time="<?= $time_key?>"
                                            data-product-group-key="<?= $key?>"
                                            ><?= $time_value?></div><?php
                                        }?>
                                    </div>
                                </div>
                            </div><!--/Время--><?php
                        }
                    }?>
                </div>
            </div><?php
        }
    }
}

