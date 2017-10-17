<?php

namespace app\components;

use yii\base\Widget;


class WDeliverySelect extends Widget{
    public $basket;
    public $sort;

    public function init(){
        parent::init();
        if($this->basket === null){
            $this->basket = false;
        }
        if($this->sort === null){
            $this->sort = 3;
        }
    }

    public function run(){
        $moneyDeliveryFree = \Yii::$app->basket->priceGroups;
        $currentValue = $this->basket->delivery_id . ':' . $this->basket->address_id;

        ?>

        <div id="basket-page-deliveries" class="my-deliveries <?= ((isset($_SESSION['modalDeliveryFree']) || $moneyDeliveryFree >= 3000) ? 'no' : '')?>" data-address-base="<?= $this->basket->delivery_id?>:<?= $this->basket->address_id?>">
            <div class="address"><?= WAddressAddModal::widget();?> </div>
<?php /* vibor dostavki na club
            <select
                name="delivery-address-select"
                class="delivery-club-address-list form-control select-input"
            >
                <option><?=\Yii::t('app','Адреса клубов');?></option><?php

            foreach(\Yii::$app->basket->deliveryClubsAddresses() as $item) {
                $deliveryId = !empty($item['delivery_id']) ? $item['delivery_id'] : $item['id'];?>

                <option
                    value="<?= $deliveryId.':'.$item['value']?>"
                    data-address-clear="<?= trim(str_replace('ExtremeFitness – ','',$item['address']))?>"
                    data-delivery-id="<?= $deliveryId?>"
                    data-address-id="<?= $item['value']?>"
                    id="delivery-<?= $item['value'].'-'.$item['id']?>"
                    class="radio"
                    <?= ($currentValue == $deliveryId .':'. $item['value'] ?' selected="selected"':'')?>
                ><?= $item['address']?></option><?php

            }?>
            </select><?php */

            foreach(\Yii::$app->basket->deliveryAddresses() as $item) {
                $deliveryId = !empty($item['delivery_id']) ? $item['delivery_id'] : $item['id'];
                ?>
                <div class="radio item">
                  <label class="radio__label my-deliveries-form" >
                    <input
                        data-address-clear="<?= $item['address']?>"
                        data-delivery-id="<?= $deliveryId?>"
                        data-address-id="<?= $item['value']?>"
                        id="delivery-<?= $item['value'].'-'.$item['id']?>"
                        type="radio"
                        class="radio"
                        value="<?= $deliveryId.':'.$item['value']?>"
                        <?= ($currentValue == $deliveryId .':'. $item['value'] ?' checked="checked"':'')?>
                        name="delivery-address-select" />
                         <span class="radio-checked"><?= $item['address']?></span>
                  </label>
                </div><?php
            }
            if(!empty($this->basket->products)) {
                $productType = [];
                foreach ($this->basket->products as $product) {
                    if (in_array($product->variant->product->type_id, [1003, 1005, 1007])) {
                        $productType[1003] = true;
                    } else {
                        $productType[$product->variant->product->type_id] = true;
                    }

                }
                if (count($productType) > 1) { ?>

                        <div class="text-min" style="margin: 30px 0px 10px;">
                            <?=\Yii::t('app','В корзине находятся товары из нескольких категорий')?>.
                            <?=\Yii::t('app','Они будут доставленны разными курьерами')?>.
                            <?=\Yii::t('app','Сроки доставки могут отличаться')?>.
                            <?=\Yii::t('app','Оплата каждой доставки производится отдельно')?>.
                        </div>
            <?php }
            /*
                echo '<pre>';
                print_r($productType);
                echo '</pre>';*/
            }
            ?>




        </div><!--/Адресс доставка--><?php
    }
}
