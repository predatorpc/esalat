<?php
use app\components\WProductItemOne;
use yii\widgets\ListView;
use yii\widgets\Breadcrumbs;
$this->title = $list->title;
$this->params['breadcrumbs'][] = ['label' => 'Каталог','url' => '/catalog/','template' => "{link}/ \n",];
$this->params['breadcrumbs'][] = ['label' => 'Списки','url' => '/catalog/list','template' => "{link}/ \n",];
$this->params['breadcrumbs'][] = ['label' => $this->title,'template' => "{link} \n",];
//(new \app\modules\catalog\models\ListsGoods())->sort
$lists = \app\modules\catalog\models\ListsGoods::find()->where(['status' => 1,'list_id' => $list->id])->orderBy('sort')->all();
?>
<div class="content">
    <!--Хлебная крошка-->
    <?= Breadcrumbs::widget(['options' => ['class' => 'path'],'tag' => 'div','homeLink' => ['label' => 'Главная', 'url' => '/','template' => "{link}/ \n"],'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]);?>
    <!--Хлебная крошка-->
    <div class="row">
        <div class="goods goods-list">
            <div style="padding: 10px;">
                <h3><?= $list->title?></h3>
            </div>
            <?php if(Yii::$app->params['mobile']!=true){?>
                <table cellpadding="0" cellspacing="0" border="0" class="my-table mob-table i-{$item.id} all-product-list-table" style="wifth:100%;">
                    <thead>
                    <tr class="grey res">

                        <th><input type="checkbox"  checked="checked" class="all-product-list-control"></th>
                        <th>№</th>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Количество</th>
                        <th>Цена</th>
                        <th>Сумма</th>
                    </tr>
                    </thead>
                    <tbody><?php
                    if(!empty($lists)){
                    $k = 0;
                    foreach ($lists as $i => $product) {
                        if(!empty($product->product) && $product->product->checkPay){
                            $k++;?>
                        <tr
                            data-variant-id="<?= $product->variant->id?>"
                            data-product-id="<?= $product->product->id?>"
                            data-count="<?= $product->amount?>"
                        >
                            <td class="checkbox">
                                <input class="go_basket" type="checkbox" checked="checked" name="Goods[<?= $product->id?>]" value="<?= $product->id?>" style="margin: 0px;position: relative">
                            </td>
                            <td class="number" item="{$i.id}"><?= $k?></td>
                            <td class="images"><a href="<?= $product->product->catalogUrl?>" class="no-border"><img src="http://www.esalad.ru<?= $product->product->imageSimple?>" alt="<?= $product->product->name?>" /></a></td>
                            <td class="name"><a href="<?= $product->product->catalogUrl?>"><?= $product->product->name?></a><br /><small class="select variation-tags"><?= $product->variant->titleWithProperties?></small></td>
                            <td class="number">

                                <div class="count" data-variation-id="0" item="{$good.id}">
                                    <span class="num"><?= $product->amount ? $product->amount : 1?> шт.</span>
                                    <div class="minus"></div>
                                    <div class="plus"></div>
                                </div><!--/Количество-->
                            </td>
                            <td class="money" ><?= \app\modules\common\models\ModFunctions::money($product->variant->priceValue)?></td>
                            <td class="discount_money" ><?= \app\modules\common\models\ModFunctions::money($product->variant->priceValue * $product->amount)?></td>
                            </tr><?php
                        }
                    }?>
                    <tr class="total-{$item.id}" style="background: #C0C0C0">
                        <td class="money">Итого:</td>
                        <td colspan="5"></td>
                        <td class="money"><?= \app\modules\common\models\ModFunctions::money($list->fullPrice)?></td>
                    </tr>
                    </tbody><?php
                    }?>

                </table>

            <?php } else { ?>

                <!-- table cellpadding="0" cellspacing="0" border="0" class="my-table mob-table i-{$item.id} all-product-list-table" style="wifth:100%;" -->

                <table cellpadding="0" cellspacing="0" border="0" class="my-table1 all-product-list-table1" style="width:90%; padding: 5px;">
                    <tr class="grey res">
                        <td></td>
                        <td>№</td>
                        <td>Фото</td>
                        <td>Название</td>
                        <td>Кол-во</td>
                        <td>Цена</td>
                        <td>Сумма</td>
                    </tr>

                    <tbody>
                    <?php
                    if(!empty($lists)){
                        $k = 0;
                        foreach ($lists as $i => $product) {
                            if(!empty($product->product) && $product->product->checkPay){
                                $k++;?>

                            <tr
                                data-variant-id="<?= $product->variant->id?>"
                                data-product-id="<?= $product->product->id?>"
                                data-count="<?= $product->amount?>"
                            >
                                <td style="width: 1px;">
                                    <input class="go_basket" type="checkbox" checked="checked" name="Goods[<?= $product->id?>]" value="<?= $product->id?>" style="margin: 0px; position: relative">
                                </td>

                                <td style="width: 1px;" item="{$i.id}"><?= $k?></td>
                                <td class="images"><a href="<?= $product->product->catalogUrl?>" class="no-border"><img style="width: 100px;" src="http://www.esalad.ru<?= $product->product->imageSimple?>" alt="<?= $product->product->name?>" /></a></td>
                                <td class="name"><a href="<?= $product->product->catalogUrl?>"><?= $product->product->name?></a><br /><small class="select variation-tags"><?= $product->variant->titleWithProperties?></small></td>
                                <td class="number" style="width: 50px;">

                                    <div class="count" data-variation-id="0" item="{$good.id}">
                                        <span class="num"><?= $product->amount ? $product->amount : 1?> шт.</span>
                                        <div class="minus"></div>
                                        <div class="plus"></div>
                                    </div><!--/Количество-->
                                </td>
                                <td class="money" style="width: 50px;"><?= \app\modules\common\models\ModFunctions::money($product->variant->priceValue)?></td>
                                <td class="discount_money" style="width: 50px;"><?= \app\modules\common\models\ModFunctions::money($product->variant->priceValue * $product->amount)?></td>

                                </tr><?php
                            }
                        }?>
                        <tr class="total-{$item.id}" style="background: #C0C0C0">
                            <td class="money">Итого:</td>
                            <!--td colspan="5"></td -->
                            <td class="money" colspan="6"><?= \app\modules\common\models\ModFunctions::money($list->fullPrice)?></td>

                        </tr>
                    <?php  }?>
                    </tbody>
                </table>

            <?php } ?>
            <br>
            <div class="button">
                <div class="button_oran all-product-list-in-basket"><div style="font-size: 14px;">Отправить в корзину</div></div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
