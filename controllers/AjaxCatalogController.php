<?php

namespace app\controllers;

use app\components\WCatalogListButtonBlock;
use app\components\WProductItemOne;
use app\modules\basket\models\BasketProducts;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsVariations;
use app\modules\common\controllers\FrontController;
use Yii;

/**
 * BasketController implements the CRUD actions for Basket model.
 */
class AjaxCatalogController extends FrontController
{
    //------------------

    public function actionGetCatalogListProduct(){
        $productId = !empty(intval($_POST['productId'])) ? intval($_POST['productId']) : false;
        if(!$productId){
            return false;
        }else{
            return WProductItemOne::widget([
                'model' => Goods::findOne($productId),
            ]);
        }
    }

    public function actionGetCatalogDetailProduct(){
        $productId = !empty(intval($_POST['productId'])) ? intval($_POST['productId']) : false;
        if(!$productId){
            return false;
        }else{
            return \app\components\WProductDetailVariable::widget([
                'model' => Goods::findOne($productId),
            ]);
        }
    }

    public function actionChangeProductVariant(){
        $basketItem = !empty(intval($_POST['basketItemId'])) ? intval($_POST['basketItemId']) : false;
        $productId = !empty(intval($_POST['productId'])) ? intval($_POST['productId']) : false;
        $tagIds = !empty($_POST['tagIds']) ? $_POST['tagIds'] : false;

        if(!$basketItem || !$productId || !$tagIds){
            return false;
        }else{
            if(!isset($_POST['new']) || empty($_POST['new'])){
                $variant = $this->basketObject->findVariantByTags($productId,$tagIds);
                $findVariantInBasket = BasketProducts::find()->where(['variant_id' => $variant])->one();

                if(!$findVariantInBasket){
                    $basketProducts = BasketProducts::find()->where(['id' => $basketItem])->one();
                    if(!$basketProducts){
                        return false;
                    }else{
                        $basketProducts->variant_id = $variant;
                        if($basketProducts->save()){
                            $basketProducts->variant->setPriceValue();

                            return \app\components\WBasketProductVOne::widget([
                                'product' => $basketProducts,
                            ]);
                        }else{
                            return false;
                            //Zloradnij::print_arr($basketProducts);
                        }
                    }
                }else{
                    BasketProducts::find()->where(['id' => $basketItem])->one()->delete();
                    return false;
                }
            }
        }
    }

    public function actionChangeCountProduct(){
        $basketItem = !empty(intval($_POST['basketId'])) ? intval($_POST['basketId']) : false;
        $count = !empty(intval($_POST['count'])) ? intval($_POST['count']) : false;

        if(!$basketItem || !$count){
            return false;
        }else{
            $basketProducts = BasketProducts::find()->where(['id' => $basketItem])->one();
            $basketProducts->count = $count;
            if($basketProducts->save()){
                return true;
            }else{
                return false;
            }
        }
    }

    public function actionGetBasketVariantIds(){
        return json_encode(Yii::$app->basket->getBasketVariantIds());
    }

    public function actionGetBasketVariantButton(){
        $variantId = !empty($_POST['variantId']) ? $_POST['variantId'] : false;
        return $variantId ? Yii::$app->basket->displayButtonBlockForCatalogList($variantId) : false;
    }

    public function actionGetProductPage(){
        $result = '';
        $post = Yii::$app->request->post();
        $categoryId = !empty($post['category-id']) ? $post['category-id'] : false;
        $pageId = !empty($post['page-id']) ? $post['page-id'] : false;

        if($categoryId && intval($categoryId) > 0 && $pageId && intval($pageId) > 0){
            $model = Category::findOne($categoryId);
            $model->setPageId($pageId);

            if(!empty($model->allProductsClear)){
                foreach ($model->allProductsClear as $i => $product) {
                    $result .= WProductItemOne::widget([
                        'model' => $product,
                        'user' => Yii::$app->user->can('categoryManager'),
                        'categoryCurrent' => $model,
                    ]);
                }
            }
        }

        return $result;
    }

    // Редактирования цен;
    public function actionChangePrice(){
        if(Yii::$app->user->can('categoryManager') || Yii::$app->user->can('GodMode')){
            if(Yii::$app->request->isPost){
                $request = Yii::$app->request->post();
                if($variation = GoodsVariations::find()->where(['id'=>intval($request['variation_id'])])->One()){
                    if($request['new_price'] > 0){
                        $price = $request['new_price'];
                        if(isset($request['lenta']) && $request['lenta'] == 'on'){
                            $price = $price - $price/100*7;
                        }
                        $variation->price = $price;
                         $response = Yii::$app->response;
                          $response->format = \yii\web\Response::FORMAT_JSON;
                        //die('STOP');
                        if($variation->save()){

                            return $response->data = ['success'=>true,'price'=> $variation->priceValue,];
                        }else{
                            return json_encode($variation->getErrors());
                        }
                    }
                }
            }
        }
        return false;
    }

}
