<?php

namespace app\components;

use yii\base\Widget;

class WBasketProductTest extends Widget
{
    public $product;

    public function init()
    {
        parent::init();
        if ($this->product === null) {
            $this->product = false;
        }
    }

    public function run(){
        if(!$this->product){
            return false;
        }else{
            $basketItem = \Yii::$app->session['basket-yii']['products'][$this->product];
            $activeVariants = \Yii::$app->session['basket-yii']['activeVariants'][$this->product];
            print $activeVariants.'<pre>';print_r($basketItem);print '</pre>';


            $result = '';
            $result .= '
            <tr data-basket-item="'./*$basketItem.*/'">
                <td class="image">'.(isset($basketItem['image'])?'<img src="'.$basketItem['image'].'_min.jpg" alt="'.$basketItem['productName'].'" />':'<img src="/templates/images/good.png" alt="" />').'</td>
                <td class="name">
                    <div class="group">'.(isset($basketItem['group_name'])?$basketItem['group_name']:'').'</div>
                    <div class="name"><a href="/catalog/'.$basketItem['productId'].'" class="variation-name">'.(isset($basketItem['productName'])?$basketItem['productName']:'').'<br />'./*(isset($basketItem['options'])?$basketItem['options']:'').*/'</a></div>
                    <div id="tags-'.$basketItem['productId'].'" data-good-id="'.$basketItem['productId'].'" data-variation-id="'.$basketItem['variantId'].'" class="tags options">

            ';
                    if(count($basketItem['price']) > 1){
                        $result .= '
                        <span class="select variation-tags">'.$basketItem['variantName'].'</span>
                        ';

                        if(isset($basketItem['variants'])){
                            $result .= '
                            <div id="good-items-'.$basketItem['productId'].'" class="items">';

                            foreach($basketItem['tagGroupName'] as $variationPropertyId => $variationPropertyTitle){
                                $result .= '
                                <div class="item" rel="'.$variationPropertyId.'">
                                    <span class="name">'.$variationPropertyTitle.'</span>';
                                    foreach($basketItem['tagGroupWithValue'][$variationPropertyId] as $key => $value){
                                        if(isset($basketItem['variants'][$basketItem['variantId']])){
                                            $tagsValues = [];
                                            foreach($basketItem['variants'][$basketItem['variantId']] as $tagValueId => $tagValueCount){
                                                $tagsValues[] = $tagValueId;
                                            }
                                        }
                                        $insertClass = '';
                                        if(in_array($key,$tagsValues)){
                                            $insertClass = ' open';
                                        }
                                        if($basketItem['tags'][$key]['active'] < 1){
                                            $insertClass = ' disabled';
                                        }

                                        $result .= '
                                        <span
                                            data-tag-id="'.$key.'"
                                            class="i'.$insertClass.'"
                                        >'.$value.'</span>';
                                    }
                                $result .= '
                                </div>';
                            }

                            $result .= '
                            </div>';
                        }
                    }


                    $result .= '
                    </div></td></tr>
                    ';



            /*
            $product = $this->product['product'];
            $variant = $this->product['variant'];
            $variantsAll = $this->product['variantsAll'];


            $result .= '
            <tr>
                <td class="image">'.(isset($variant['image'])?'<img src="'.$variant['image'].'_min.jpg" alt="'.$variant['name'].'" />':'<img src="/templates/images/good.png" alt="" />').'</td>
                <td class="name">
                    <div class="group">'.(isset($product['group_name'])?$product['group_name']:'').'</div>
                    <div class="name"><a href="/catalog/'.$product['id'].'" class="variation-name">'.$product['name'].'<br />'.(isset($product['options'])?$product['options']:'').'</a></div>
                    <div id="tags-'.$product['id'].'" data-good-id="'.$product['id'].'" data-variation-id="'.($variant['id']?$variant['id']:0).'" class="tags options">

                    </div></td></tr>
                    ';

            if($product['variations_counts'] > 1){
                if($variant){
                    $result .= '
                            <span class="select variation-tags">'.$variant['name'].'</span>
                            ';
                }else{
                    $result .= '
                            <span class="select variation-tags error">выбрать вариант2</span>
                            ';
                }
                if(isset($variantsAll)){
                    $result .= '
                            <div id="good-items-'.$product['id'].'" class="items">';

                    foreach($variantsAll as $variation){
                        $result .= '
                                <div class="item" rel="'.$variation['tags_groups_id'].'">
                                    <span class="name">'.$variation['tags_groups_name'].'</span>
                                ';
                        foreach($variation['values'] as $key => $value){
                            $result .= '
                                    <span
                                        data-tag-id="'.$key.'"
                                        class="i'.((isset($product['variation']['tags'][$key]))?' open':'').'"
                                    >'.$value.'</span>';
                        }
                        $result .= '
                                </div>';
                    }

                    $result .= '
                            </div>';
                }
            }

            $result .= '
                    </div>
                </td>
                <td class="count'.(!$variant?' no-variation':'').'">
                    <div class="count" data-variation-id="'.($variant['id']?$variant['id']:0).'" item="'.$product['id'].'">
                        <div class="minus yii-class"></div>
                        <span data-max-count="'.$variant['count_max'].'">'.$product['count'].' шт.</span>
                        <div class="plus yii-class"></div>
                    </div>
                </td>
                <td class="price'.(!$variant?' no-variation':'').'">
                    <div>';
            if($product['discount_sum'] > 0){
                $result .= '
                            <span class="through variation-price">'.($product['price']?$product['price']:'').' p.</span>
                            <span class="discount variation-discount-price">'.($product['discount_price']?$product['discount_price']:'').' p.</span>';
            }else{
                $result .= '
                            <span class="price variation-price">'.($variant['price']?$variant['price']:'').' p.</span>';
            }
            $result .= '
                    </div>
                </td>
                <td class="money'.(!$variant?' no-variation':'').'">
                    <div>';
            if($product['discount_sum'] > 0){
                $result .= '
                        <span class="through variation-money">'.$product['money'].' p.</span>
                        <span class="discount variation-discount-money">'.$product['discount_money'].' p.</span>';
            }else{
                $result .= '
                        <span class="variation-money">'.$product['money'].' p.</span>';
            }
            $result .= '
                    </div>
                </td>
                <td class="delete"><div class="yii-class" data-variation-id="'.($variant['id']?$variant['id']:0).'" item="'.$product['id'].'"></div></td>
            </tr>
            ';
            */

            return $result;
        }
    }
}

