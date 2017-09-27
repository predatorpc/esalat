<?php

namespace app\controllers;


use app\modules\common\controllers\FrontController;
use app\modules\wishlist\models\WishlistProducts;
use Yii;

/**
 * BasketController implements the CRUD actions for Basket model.
 */
class AjaxWishListController extends FrontController
{
    public function actionAddToWishList($good_id){
        if(WishlistProducts::find()->where(['product_id'=>$good_id,'user_id'=>Yii::$app->user->id])->One()){
            $model = WishlistProducts::find()->where(['product_id'=>$good_id,'user_id'=>Yii::$app->user->id])->One();
        }else{
            $model = new WishlistProducts();
            $model->product_id = $good_id;
            $model->user_id = Yii::$app->user->id;
        }
        $model->status = 1;
        if($model->save()){
            return 'added';
        }

        return 'error';
    }

    public function actionRemoveFromWishList($good_id){
            $model = WishlistProducts::find()->where(['product_id'=>$good_id,'user_id'=>Yii::$app->user->id])->One();
            $model->status = 0;
            if($model->save()){
                return 'deleted';
            }
    }
}