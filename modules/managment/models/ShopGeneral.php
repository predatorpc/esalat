<?php
namespace app\modules\managment\models;

use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsVariations;
use app\modules\common\models\Zloradnij;

class ShopGeneral{

    public static function removeOldGoodsCountsItems($productId){
        $stores = Goods::getProductStoresQuery($productId);
        $stores = $stores->all();

        $counts = GoodsCounts::find()->where(['good_id' => $productId])->all(); // Выбираем все записи о количестве на складах

        $variants = GoodsVariations::find()->where(['good_id' => $productId])->indexBy('id')->all();    //Вы бираем все вариации товара

        if(!$stores){   //Если нет ни одного склада
            if(!$variants){

            }else{
                foreach ($variants as $variant) {   // Отключаем все вариации
                    $variant->status = 0;
                    $variant->save();
                }
            }
            if(!$counts){

            }else{
                foreach ($counts as $count) {   // Удаляем все записи о количестве
                    $count->delete();
                }
            }
        }else{
            $storeIds = [];
            foreach ($stores as $store) {
                $storeIds[] = $store->id;
            }
            $variantIds = [];
            foreach ($variants as $variant) {
                $variantIds[] = $variant->id;
            }
            foreach ($counts as $i => $count) {
                if(!in_array($count->store_id,$storeIds)){  //Если запись о количестве не привязана ни к одному действующему складу, грохаем её
                    $count->delete();
                    unset($counts[$i]);
                }elseif(!in_array($count->variation_id,$variantIds)){   //Если запись о количестве привязана к несуществующей вариации, грохаем её
                    $count->delete();
                    unset($counts[$i]);
                }else{
                    if($count->count < 1 && $variants[$count->variation_id]->status != 0){  //  Если количество на складе == 0, отключаем вариацию
                        $variants[$count->variation_id]->status = 0;
                        $variants[$count->variation_id]->save();
                    }
                }
            }
            if(count($stores) * count($variants) < count($counts)){ //Если записей о количестве меньше чем должно быть
                foreach ($variantIds as $variantId) {
                    foreach ($storeIds as $storeId) {
                        $isset = false;
                        foreach ($counts as $count) {
                            if($count->store_id == $storeId && $count->variation_id == $variantId){
                                $isset = true;
                            }
                        }
                        if(!$isset){
                            $countNew = new GoodsCounts();
                            $countNew->count = 0;
                            $countNew->good_id = $productId;
                            $countNew->store_id = $storeId;
                            $countNew->variation_id = $variantId;
                            $countNew->status = 1;
                            $countNew->update = date('Y-m-d H:i:s');
                            $countNew->save();
                        }
                    }
                }
            }
        }
    }

    public static function getProductStoresQuery($productId){
        return ShopsStores::find()
            ->leftJoin(Shops::tableName(),Shops::tableName().'.id = '.ShopsStores::tableName().'.shop_id')
            ->leftJoin(ShopGroupRelated::tableName(),ShopGroupRelated::tableName().'.shop_id = '.Shops::tableName().'.id')
//            ->leftJoin(ShopGroup::tableName(),ShopGroup::tableName().'.id = '.ShopGroupRelated::tableName().'.shop_group_id')
//            ->leftJoin(ShopGroupVariantLink::tableName(),ShopGroupVariantLink::tableName().'.shop_group_id = '.ShopGroup::tableName().'.id')
            ->leftJoin(ShopGroupVariantLink::tableName(),ShopGroupVariantLink::tableName().'.shop_group_id = '.ShopGroupRelated::tableName().'.shop_group_id')
            ->leftJoin('address', 'address.id = shops_stores.address_id')
            ->where([
                ShopGroupVariantLink::tableName().'.product_id' => $productId,
            ]);
    }

    public static function getVariantCountQuery($productId,$variantId = false){
        $query = self::getProductStoresQuery($productId);
        $query->leftJoin(GoodsCounts::tableName(),GoodsCounts::tableName().'.store_id = '.ShopsStores::tableName().'.id')
            ->addSelect([GoodsCounts::tableName().'.*'])
            ->andWhere([
                GoodsCounts::tableName().'.status' => 1,
                GoodsCounts::tableName().'.good_id' => $productId,
            ]);
        if(!$variantId){

        }else{
            $query->andWhere([GoodsCounts::tableName().'.variation_id' => $variantId]);
        }
        return $query;
    }

    public static function checkVariantCountQuery($productId,$variantId){
        $query = self::getVariantCountQuery($productId,$variantId);
        return $query
            ->addSelect([
                'MIN('.GoodsCounts::tableName().'.count) AS minCount',
            ]);
    }

    public static function issetActiveShopInGroup($shopId){
        $shopGroup = ShopGroupRelated::find()->where(['shop_id' => $shopId])->one();

        $shopList = Shops::find()
            ->leftJoin('shop_group_related','shop_group_related.shop_id = shops.id')
            ->leftJoin('shop_group','shop_group.id = shop_group_related.shop_group_id')
            ->where([
                'shop_group.status' => 1,
                'shops.status' => 1,
                'shop_group_related.id' => $shopGroup->id,
            ])
            ->count();

        if(!$shopList || $shopList < 2){
            return false;
        }else{
            return true;
        }
    }
}
