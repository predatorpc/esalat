<?php
namespace app\components;

use app\modules\common\models\Zloradnij;
use app\modules\shop\models\OrdersItems;
use yii\base\Widget;
use yii\helpers\Url;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\Category;
use app\modules\common\models\ModFunctions;
use Yii;

/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Класс родительского блока "product-item" - якорь для javascript. НЕ УДАЛЯТЬ и не навешивать на него стили !!!!
class WProductItemOne extends Widget {
    public $model;
    public $categoryCurrent;
    public $user;

    public function init() {
        parent::init();
        if ($this->model === null) {
            $this->model = false;
        }
    }
    public function run(){
        if(!$this->model){
            return false;
        }else{
            $minVariation = [];
            $allVariations = $this->model->variationsCatalog;
            $firstVariant = (!empty($allVariations[0])) ? $allVariations[0] : false;
            //print_r($firstVariant);die();
            $propertyList = [];
            if(!empty($allVariations[0])){
                foreach ($allVariations as $i => $variation) {
                    if(!empty($variation->countOnStores)){
                        $minVariation[$variation->id] = -10;
                        foreach ($variation->countOnStores as $countOnStore) {
                            $minVariation[$variation->id] = ($minVariation[$variation->id] < 0 || $minVariation[$variation->id] > $countOnStore->count) ? $countOnStore->count : $minVariation[$variation->id];
                        }
                    }

                    if(!empty($minVariation[$variation->id]) && $minVariation[$variation->id] <= 0){
                        $variation->status = 0;
                        $variation->save();
                        unset($allVariations[$i]);
                    }else{
                        $minVariation[$variation->id] = null;
                    }
                }
            }
            if(!empty($allVariations[0])){

            }else{
                $this->model->status = 0;
                $this->model->save();
                return false;
            }

            foreach ($allVariations as $variation) {
                if(!$variation->propertiesFrontVisible){

                }else{
                    foreach ($variation->propertiesFrontVisible as $item) {
                        $propertyList[$variation->id][$item->group_id][$item->id] = $item->value;
                    }
                }
            }
            // Состояние окно;
            $session = Yii::$app->session;
            if(Yii::$app->session->get('shopMaster',0) > 0){
                $session['menuList'] = true;
            }else{
                if(!empty($session['menuList'])){
                    unset($session['menuList']);
                }
            }
            // Проверка на дочерн. категорий;
            $cat = Category::find()->where(['parent_id' =>  $this->categoryCurrent['id'], 'active'=>1])->count('id');

               // Цвет;
               $color = (!empty($this->model->color_bg)? $this->model->color_bg : (!empty($this->model->category->color) ? $this->model->category->color : (!empty($this->categoryCurrent['color']) ? $this->categoryCurrent['color'] : '')));
            ?>
            <div class="sort_item  <?=(!empty($session['menuList']) ? 'hidden' : '')?>" data-key="<?= $this->model->id?>"  >
                <div id="<?= $this->model->id?>" data-position="<?=$this->model->position?>"   data-product-id="<?= $this->model->id?>" class="item   <?=($this->model->type == 1 ? 'max':'')?> item-<?= $this->model->id?>  product-item js-product-item" >
                    <div class="block <?= (!empty($color) ? 'ef': '')?>"      <?php if(!empty($color)): ?>style="border-bottom-color:<?=strlen($color)<7?'#'.$color:$color ?>;" <?php endif; ?> >
                        <div class="images" onclick="return show_modal_compact('/catalog/compact',' ','<?=$this->model->id?>');" >
                            <a href="<?= Url::toRoute('/'.$this->model->category->catalogPath . $this->model->id)?>">
                                <img class="ad" src="http://www.esalad.ru<?=Goods::findProductImage($this->model->id,'min')?>" alt="<?= $this->model->name?>">
                            </a>

                            <div class="compact hidden" onclick="return show_modal_compact('<?= Url::toRoute('/'.$this->model->category->catalogPath . $this->model->id) ?>',' ');"><div>Быстрый просмотр</div></div><!--.Быстрый просмотр-->
                        </div>
                        <!--block max-->
                        <div class="block-t">
                            <div class="rating">
                                <div class="title-min">Рейтинг:</div>
                                <div class="rating-icon rating-<?=$this->model->rating?>"></div>
                            </div>
                            <a class="blue comment" href="#" onclick="return show_modal_compact('/catalog/comments','Оставить отзыв',<?=$this->model->id?>);">Отзывы <?=(count($this->model->goodsComments) > 0 ? '('.count($this->model->goodsComments).')' : '')?></a>
                            <?php
                            
                            // Количество покупки;
                        /*    $count_buy = 0;
                            $ordersItems = OrdersItems::find()->where(['status'=>1,'good_id'=> $this->model->id])->All();
                            
                            foreach ($ordersItems as $ordersItem){
                            $count_buy = $count_buy + $ordersItem->count;
                            }
                            */
                            ?>
                            <?php // if($count_buy > 0):?>
                               <!-- div class="purchased">Куплен: <?php //=ModFunctions::numberSize($count_buy)?> раз</div -->
                            <?php // endif; ?>

                        </div>  <!--/block max-->
                        <div class="clear"></div>
                        <div class="title">
                            <a class="black" title="<?= $this->model->name?>" href="<?= Url::toRoute('/'.$this->model->category->catalogPath . $this->model->id)?>"><?= $this->model->name?></a>
                        </div>
                        <div class="group"><?= $this->model->category->title?></div>
                        <div class="prices"><?php
                            $hiddenClass = '';
                            if(!empty($allVariations)){
                                foreach ($allVariations as $allVariation) {?>
                                    <div class="price-block-list <?= $hiddenClass?>"  data-variant-id="<?= $allVariation->id?>">
                                        <?php
                                        if($this->model->discount == 1 && !$this->model->discount){?>
                                            <span class="price discount variation-discount-price">
                                            <?= \app\modules\common\models\ModFunctions::money(floor($allVariation->priceValue * 0.95))?>*
                                            </span><?php
                                        }else{?>
                                          <span class="price normal variation-price">
                                             <?= \app\modules\common\models\ModFunctions::money($allVariation->priceValue)?>
                                          </span>
                                        <?php }?>
                                    </div><?php
                                    $hiddenClass = 'hidden';
                                }
                            }?>
                        </div>

                        <div class="row-private js-variants-select">
                            <div class="tag-value-list">
                                <div class="options tags-item select__form_multi">
                                </div>

                                <div class="clear"></div>
                                <div class="button-ajax" data-id="<?= $firstVariant->id?>"><div class="load"></div></div>
                                <div class="product-control-buttons">
                                    <?php
                                    $jsonList = [];
                                    if(isset($allVariations) && !empty($allVariations)){
                                        foreach($allVariations as $variantKey => $variantItem){
                                            $activeClass = ($variantItem->id == $firstVariant->id) ? 'active' : '';
                                            $dataJson = [];

                                            if(!empty($propertyList[$variantItem->id])){
                                                foreach($propertyList[$variantItem->id] as $key => $propertyId){
                                                    if(!empty($key) && !empty($propertyId)){
                                                        $dataJson[$key] = key($propertyId);
                                                    }
                                                }
                                            }else{
                                             //   print '<span class="qqq222" style="display: none;">';Zloradnij::print_arr($variation->propertiesFrontVisible);print '</span>';
                                            }

                                            if((!empty($dataJson) && !isset($jsonList[json_encode($dataJson)])) || empty($variation->propertiesFrontVisible)){
                                                $jsonList[json_encode($dataJson)] = 1;?>

                                                <div class="control-buttons-for-variant js-control-buttons-for-variant <?=$activeClass?>"
                                                     data-variant="<?=$variantItem->id?>"
                                                     data-first="<?=$firstVariant->id?>"
                                                    <?=(empty($dataJson)?'':"data-json='" . json_encode($dataJson)) . "'"?>
                                                >



                                                    <div
                                                        class="button-basket-icon center basket_button"
                                                        data-action="bay"
                                                        data-basket=""
                                                        data-product="<?= $this->model->id?>"
                                                        data-variant="<?= $variantItem->id?>"
                                                        data-count="<?= ($this->model->count_pack * $this->model->count_min)?>"
                                                        data-max="<?= $minVariation[$variation->id]?>"
                                                    >
                                                        <div>Купить</div>
                                                    </div>

                                                </div><?php
                                            }else{print '<span class="qqq11"></span>';}
                                        }
                                    }else{
                                       // print '<span class="qqq"></span>';
                                    }?>

                                </div>
                            </div>
                        </div>

                        <!--Стикеры-->
                        <div class="stickers stickers__com">
                            <?= !empty($this->model->bonus)?'<div class="stikers-icon bonus" title="бонус"></div>':''?>
                        </div><!--./Стикеры-->
                        <!-- Наклейки -->
                        <div class="stickers__new">
                            <?php
                            if(!empty($this->model->stickerLinks)){
                                foreach($this->model->stickerLinks as $key=>$stickers): ?>
                                       <div class="sticker-image" style="background-image: url('/files/sticker/<?=$stickers->sticker->id?>.png');"></div>
                                <?php endforeach;
                            }?>
                        </div> <!-- Наклейки -->
                        <!-- Управление -->
                        <?php if((\Yii::$app->user->can('categoryManager') || \Yii::$app->user->can('conflictManager') || \Yii::$app->user->can('GodMode'))): ?>

                        <div class="manager manager___shop list-good">
                            <div class="items">
                                <div class="i edit" title="Редактировать" onclick="return good_edit('<?=$this->model->id?>');"></div>
                                <div class="i position <?=$cat <= 0 ? 'js-position' : ''?> js-position-option" title="Смена позиций"></div>
                                <div class="clear"></div>
                            </div>
                            <?php if(false): ?>
                                <div class="option hidden">
                                    <input class="position-input res" type="text" value="<?=$this->model->position?>">
                                    <div class="btn btn-primary js-position-update res" data-id="<?=$this->model->id?>">Ок</div>
                                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                                    <div class="button_load"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                       <?php  endif;?>
                        <?php if($this->model->count_min > 1):?>
                           <div class="info-img"><?=$this->model->count_min?>x</div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <?php
        }
    }

}

