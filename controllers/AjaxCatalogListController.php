<?php

namespace app\controllers;

use app\modules\basket\models\CatalogProduct;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\Codes;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Lists;
use app\modules\catalog\models\ListsGoods;
use app\modules\common\controllers\FrontController;
use app\modules\common\models\Zloradnij;
use app\widgets\catalog\lists\SmallProductListInModal;
use Yii;

/**
 * BasketController implements the CRUD actions for Basket model.
 */
class AjaxCatalogListController extends FrontController
{
    //------------------

    public function actionRemoveListProduct(){
        $post = Yii::$app->request->post();
        $list = !empty($post['list']) ? intval($post['list']) : false;
        $variant = !empty($post['variant']) ? intval($post['variant']) : false;

        $productList = Yii::$app->session['catalog']['product-list'][$list];
        $variant = GoodsVariations::findOne($variant);
        $category = $variant->product->category->parent->title . ' / ' . $variant->product->category->title;

        unset($productList[$category][$variant->id]);
        if(count($productList[$category]) <= 0){
            unset($productList[$category]);
        }
        Lists::updateSessionList($list,$productList);
    }

    /*--------------------------*/

    public function actionChangeCountVariantListProduct(){
        $post = Yii::$app->request->post();
        $list = !empty($post['list']) ? intval($post['list']) : false;
        $category = !empty($post['category']) ? trim($post['category']) : false;
        $variantId = !empty($post['variant-id']) ? intval($post['variant-id']) : false;
        $count = !empty($post['count']) ? intval($post['count']) : false;

        $productList = Yii::$app->session['catalog']['product-list'][$list];
        $productList[$category][$variantId] = $count;
        Lists::updateSessionList($list,$productList);
    }

    public function actionChangeVariantListProduct(){
        $post = Yii::$app->request->post();
        $list = !empty($post['list']) ? intval($post['list']) : false;
        $category = !empty($post['category']) ? trim($post['category']) : false;
        $oldVariantId = !empty($post['old-variant-id']) ? intval($post['old-variant-id']) : false;
        $newVariantId = !empty($post['new-variant-id']) ? intval($post['new-variant-id']) : false;

        $productList = Yii::$app->session['catalog']['product-list'][$list];
        $oldVariant = GoodsVariations::findOne($oldVariantId);

        $productList[$category][$newVariantId] = $oldVariant->product->count_min;
         unset($productList[$category][$oldVariantId]);

         Lists::updateSessionList($list,$productList);
    }

    public function actionGetListProduct(){
        $response = $disableProducts = [];
        $post = Yii::$app->request->post();
        $category = !empty($post['category']) ? intval($post['category']) : false;
        $list = !empty($post['list']) ? intval($post['list']) : false;
        $productList = Yii::$app->session['catalog']['product-list'][$list];

        if(!empty($productList)){
            foreach ($productList as $item) {
                if(!empty($item)){
                    foreach ($item as $productId => $count) {
                        $disableProducts[] = $productId;
                    }
                }
            }
        }
        $productList = [];
        $disableProducts = GoodsVariations::find()->where(['IN','id',$disableProducts])->all();
        foreach ($disableProducts as $disableProduct) {
            $productList[] = $disableProduct->good_id;
        }

        $findProducts = Category::findOne($category);
        $findProducts = $findProducts->getCategoryActiveProducts([]);
        $findProducts = $findProducts->getModels();
        if(!empty($findProducts)){
            foreach ($findProducts as $findProduct) {
                if(!in_array($findProduct->id,$productList)){
                    $response[] = $findProduct;
                }
            }
        }

        return SmallProductListInModal::widget([
            'productList' => $response,
        ]);
    }

    public function actionGetListProductChangeItem(){
        $post = Yii::$app->request->post();

        $list = !empty($post['list']) ? intval($post['list']) : false;
        $variant = !empty($post['variant']) ? intval($post['variant']) : false;
        $changeVariant = !empty($post['change-variant']) ? intval($post['change-variant']) : false;
        $count = !empty($post['count']) ? intval($post['count']) : false;
        $categoryId = !empty($post['category']) ? intval($post['category']) : false;

        $productList = Yii::$app->session['catalog']['product-list'][$list];

        if(!empty($productList) && !empty($variant) && $variant > 0){
            foreach ($productList as $category => $item) {
                if(!empty($item[$variant])){
                    unset($productList[$category][$variant]);
                    $productList[$category][$changeVariant] = $count;
                }
            }
            Lists::updateSessionList($list,$productList);
        }else{
            $category = Category::findOne($categoryId);
            $productList[$category->parent->title . ' / ' . $category->title][$changeVariant] = $count;
            Lists::updateSessionList($list,$productList);
        }

        $discount = Yii::$app->user->isGuest ? 0 : \app\modules\common\models\User::findOne(Yii::$app->user->identity->getId())->discount;
        print \app\widgets\catalog\lists\ChangeableProductVersionOne::widget([
            'product' => new ListsGoods(['list_id' => $list,'variation_id' => $changeVariant,'amount' => $count]),
            'percent' => $discount,
        ]);
    }

    public function actionGetListCategory(){
        $response = $disableCategories = $disableCategoryIds = [];
        $post = Yii::$app->request->post();
        $list = !empty($post['list']) ? intval($post['list']) : false;
        $productList = Yii::$app->session['catalog']['product-list'][$list];

        if(!empty($productList)){
            foreach ($productList as $item) {
                if(!empty($item)){
                    foreach ($item as $productId => $count) {
                        $response[] = $productId;
                    }
                }
            }
        }

        foreach (GoodsVariations::find()->where(['IN','id',$response])->all() as $variant) {
            $disableCategories[] = $variant->product->category->id;
        }
        $disableCategories = array_unique($disableCategories);

        $findCategory = Category::find()->where(['NOT IN','id',$disableCategories])->where(['active' => 1,'level' => 2])->orderBy('parent_id ASC, sort ASC')->asArray()->all();
//        Zloradnij::print_arr($findCategory);
    }

    /*--------------------------*/

    public function actionSaveProductList(){
        $error = 1;
        if(Yii::$app->user->identity){
            $post = Yii::$app->request->post();

            $listId = !empty($post['list-id']) ? intval($post['list-id']) : false;
            $listName = !empty($post['name']) ? trim($post['name']) : 'Без названия';

            $list = new Lists();

            $list->title = $listName;
            $list->user_id = Yii::$app->user->identity->id;
            $list->private = 1;
            $list->status = 1;
            $list->date_create = date('Y-m-d H:i:s');
            $list->date_update = date('Y-m-d H:i:s');
            $list->list_type = 1;

            if(!$list->save()){
                $error = 2;
                Zloradnij::print_arr($list->errors);
            }

            $productList = Yii::$app->session['catalog']['product-list'][$listId];

            if(!empty($productList)){
                $k = 0;
                foreach ($productList as $category => $items) {
                    foreach ($items as $item => $count) {
                        $k++;
                        $variant = GoodsVariations::findOne($item);

                        $product = new ListsGoods();
                        $product->list_id = $list->id;
                        $product->good_id = $variant->good_id;
                        $product->amount = $count;
                        $product->date_create = date('Y-m-d H:i:s');
                        $product->variation_id = $variant->id;
                        $product->sort = $k*10;
                        $product->status = 1;

                        if(!$product->save()){
                            $error = 3;
                            Zloradnij::print_arr($product->errors);
                        }
                    }
                }
            }
            return $list->id;
        }
    }

    public function actionSaveProductListFromBasket(){
        $error = 1;
        if(Yii::$app->user->identity){
            $post = Yii::$app->request->post();

            $listName = !empty($post['name']) ? trim($post['name']) : 'Без названия';

            $list = new Lists();

            $list->title = $listName;
            $list->user_id = Yii::$app->user->identity->id;
            $list->private = 1;
            $list->status = 1;
            $list->date_create = date('Y-m-d H:i:s');
            $list->date_update = date('Y-m-d H:i:s');
            $list->list_type = 1;

            if(!$list->save()){
                $error = 2;
                Zloradnij::print_arr($list->errors);
            }

            if(!Yii::$app->basket->emptyBasket()){
                $k = 0;
                foreach (Yii::$app->basket->basketProducts as $basketProduct) {
                    $k++;
                    $variant = GoodsVariations::findOne($basketProduct['variant_id']);

                    $product = new ListsGoods();
                    $product->list_id = $list->id;
                    $product->good_id = $variant->good_id;
                    $product->amount = $basketProduct['count'];
                    $product->date_create = date('Y-m-d H:i:s');
                    $product->variation_id = $variant->id;
                    $product->sort = $k*10;
                    $product->status = 1;

                    if(!$product->save()){
                        $error = 3;
                        Zloradnij::print_arr($product->errors);
                    }
                }
            }
            return $list->id;
        }
    }

    /* ------------------------------ */

    public function actionBuyProductList(){

        $error = 1;
        $post = Yii::$app->request->post();
        $listId = !empty($post['list-id']) ? intval($post['list-id']) : false;

        $productList = Yii::$app->session['catalog']['product-list'][$listId];
        foreach ($productList as $category => $items) {
            foreach ($items as $item => $count) {
                Yii::$app->controller->basket->add($item,$count);
            }
        }
        $list = Lists::find()->where(['id'=>$listId])->one();
        if(!empty($list)){
            $code = Codes::find()->where(['user_id'=>$list->user_id, 'status'=>1])->one();
            if(!empty($code)){
                Yii::$app->basket->setPromoCode($code->id);
            }
        }
        return $error;
    }

    //--------------------
}
