<?php

namespace app\modules\common\controllers;

use app\modules\actions\models\ActionsPresentSave;
use app\modules\basket\models\Basket;
use app\modules\basket\models\BasketShop;
use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\common\models\Zloradnij;
use app\modules\common\models\User;
use Yii;
use yii\db\Connection;
use yii\web\Controller;
/**
 * UserController implements the CRUD actions for Useradmin model.
 */
class GeneralShopController extends Controller
{
    public $layout = '@app/views/layouts/main';
    public $basket;
    public $userCatalogHide;

    public function init() {
        parent::init();
        $this->basket = (new BasketShop())->findCurrentBasket();
        $this->basket->start();
        //start демо версия добавления подарка TODO::ADD START PRESENT
        /*
        if(!empty(Yii::$app->params['present']) && isset(Yii::$app->user->id)){
            $searchPresent = ActionsPresentSave::find()->where(['user_id'=>Yii::$app->user->id, 'present'=>Yii::$app->params['present']])
                ->andWhere('card_number IS NOT NULL')
                ->andWhere(['like', 'update_date', Date('Y-m-', time())])
                ->one();
            if(empty($searchPresent)){
                if( $this->basket->priceGroups>=3000){
                    //добавить если нет
                    if(!$this->basket->getPresentInBasket()){//нет в текущей корзине
                        $presentSave = ActionsPresentSave::find()->where(['user_id'=>Yii::$app->user->id, 'basket_id'=>$this->basket->id, 'present'=>Yii::$app->params['present']])->one();
                        if(empty($presentSave)){
                            $this->basket->add(Yii::$app->params['present'], 1);
                            $presentSave = new ActionsPresentSave();
                            $presentSave->user_id = isset(Yii::$app->user->id) ? Yii::$app->user->id : NULL;
                            $presentSave->basket_id = $this->basket->id;
                            $presentSave->present = Yii::$app->params['present'][0];
                            $presentSave->update_date = Date('Y-m-d H:i:00', time());
                            $presentSave->save(true);
                        }
                        else{
                            if($presentSave->status==0){
                                $this->basket->add(Yii::$app->params['present'], 1);
                                $presentSave->update_date = Date('Y-m-d H:i:00', time());
                                $presentSave->status=1;
                                $presentSave->save(true);
                            }
                        }
                    }
                }
                else{
                    //удалить если есть
                    if($prodId = $this->basket->getPresentInBasket()){
                        $this->basket->removeProduct($prodId);
                        $presentSave = ActionsPresentSave::find()->where(['basket_id'=>$this->basket->id, 'status'=>1, 'present'=>Yii::$app->params['present']])->one();
                        if(!empty($presentSave)){
                            $presentSave->status=0;
                            $presentSave->save(true);
                        }
                        $this->basket = '';
                        $this->basket = (new BasketShop())->findCurrentBasket();
                        $this->basket->start();
                    }
                }
            }
            else{
                if($this->basket->getPresentInBasket()) {//есть в текущей корзине
                    $this->basket->removeProduct($prodId);
                    $this->basket = '';
                    $this->basket = (new BasketShop())->findCurrentBasket();
                    $this->basket->start();
                }
            }
        }
        if(!empty(Yii::$app->params['presentSport']) && isset(Yii::$app->user->id)){
            $searchPresent = ActionsPresentSave::find()->where(['user_id'=>Yii::$app->user->id, 'present'=>Yii::$app->params['presentSport']])
                ->andWhere('card_number IS NOT NULL')
                ->andWhere(['like', 'update_date', Date('Y-m-', time())])
                ->one();
            if(empty($searchPresent)){
                if( $this->basket->priceGroups>=5000){
                    //добавить если нет
                    if(!$this->basket->getPresentInBasket()){//нет в текущей корзине
                        $presentSave = ActionsPresentSave::find()->where(['user_id'=>Yii::$app->user->id, 'basket_id'=>$this->basket->id, 'present'=>Yii::$app->params['presentSport']])->one();
                        if(empty($presentSave)){
                            $this->basket->add(Yii::$app->params['present'], 1);
                            $presentSave = new ActionsPresentSave();
                            $presentSave->user_id = isset(Yii::$app->user->id) ? Yii::$app->user->id : NULL;
                            $presentSave->basket_id = $this->basket->id;
                            $presentSave->present = Yii::$app->params['present'][0];
                            $presentSave->update_date = Date('Y-m-d H:i:00', time());
                            $presentSave->save(true);
                        }
                        else{
                            if($presentSave->status==0){
                                $this->basket->add(Yii::$app->params['present'], 1);
                                $presentSave->update_date = Date('Y-m-d H:i:00', time());
                                $presentSave->status=1;
                                $presentSave->save(true);
                            }
                        }
                    }
                }
                else{
                    //удалить если есть
                    if($prodId = $this->basket->getPresentInBasket()){
                        $this->basket->removeProduct($prodId);
                        $presentSave = ActionsPresentSave::find()->where(['basket_id'=>$this->basket->id, 'status'=>1, 'present'=>Yii::$app->params['presentSport']])->one();
                        if(!empty($presentSave)){
                            $presentSave->status=0;
                            $presentSave->save(true);
                        }
                        $this->basket = '';
                        $this->basket = (new BasketShop())->findCurrentBasket();
                        $this->basket->start();
                    }
                }
            }
            else{
                if($this->basket->getPresentInBasket()) {//есть в текущей корзине
                    $this->basket->removeProduct($prodId);
                    $this->basket = '';
                    $this->basket = (new BasketShop())->findCurrentBasket();
                    $this->basket->start();
                }
            }
        }*/
        //new demo present start
        foreach (Yii::$app->params['presentAll'] as $key => $presentOption){
            if(!empty($presentOption) && isset(Yii::$app->user->id)) {
                $searchPresent = ActionsPresentSave::find()->where(['user_id'=>Yii::$app->user->id, 'present'=>$presentOption['present']])
                    ->andWhere('card_number IS NOT NULL')
                    ->andWhere(['like', 'update_date', Date('Y-m-', time())])
                    ->one();

                if(empty($searchPresent)){//подарков не было нет записей в бд
                    if( ($this->basket->getPriceGroupsAll($key)>=$presentOption['basketPriceMin']) && ( empty($presentOption['basketPriceMax']) || $this->basket->getPriceGroupsAll($key)<$presentOption['basketPriceMax'] ) ){
                        //добавить если нет
                        if(!$this->basket->getPresentInBasketAll($key)){//нет в текущей корзине
                            $presentSave = ActionsPresentSave::find()->where(['user_id'=>Yii::$app->user->id, 'basket_id'=>$this->basket->id, 'present'=>$presentOption['present']])->one();
                            if(empty($presentSave)){// добавлеям подарок в корзину и сохраняем запись со статусом 1
                                $this->basket->add($presentOption['present'], 1);
                                $presentSave = new ActionsPresentSave();
                                $presentSave->user_id = isset(Yii::$app->user->id) ? Yii::$app->user->id : NULL;
                                $presentSave->basket_id = $this->basket->id;
                                $presentSave->present = $presentOption['present'];
                                $presentSave->update_date = Date('Y-m-d H:i:00', time());
                                $presentSave->save(true);
                            }
                            else{
                                if($presentSave->status==0){// если статус 0 значит товар был не было в корзине то есть сумма удовлетворяла условиям а потом стала меньше то добавляем
                                    $this->basket->add($presentOption['present'], 1);
                                    $presentSave->update_date = Date('Y-m-d H:i:00', time());
                                    $presentSave->status=1;
                                    $presentSave->save(true);
                                }
                            }
                            $this->basket = '';
                            $this->basket = (new BasketShop())->findCurrentBasket();
                            $this->basket->start();
                        }
                    }
                    else{
                        //удалить если есть
                        if($prodId = $this->basket->getPresentInBasketAll($key)){
                            $this->basket->removeProduct($prodId);
                            $presentSave = ActionsPresentSave::find()->where(['basket_id'=>$this->basket->id, 'status'=>1, 'present'=>$presentOption['present']])->one();
                            if(!empty($presentSave)){
                                $presentSave->status=0;
                                $presentSave->save(true);
                            }
                            $this->basket = '';
                            $this->basket = (new BasketShop())->findCurrentBasket();
                            $this->basket->start();
                        }
                    }
                }
                else{//что бы не удалялся сертификат кино
                    if($prodId = $this->basket->getPresentInBasketAll($key)) {//есть в текущей корзине
                        if($key!=3){
                            $this->basket->removeProduct($prodId);
                            $this->basket = '';
                            $this->basket = (new BasketShop())->findCurrentBasket();
                            $this->basket->start();
                        }
                    }
                }
            }
        }
        //new demo present end
        //end демо версия добавления подарка


        // Мобильная версия;
        if (Yii::$app->devicedetect->version('Android') || Yii::$app->devicedetect->version('iPhone') || Yii::$app->devicedetect->version('iPad')) {
            Yii::$app->params['mobile'] = true;
        }

        // Заглушка транслит;
        if(Yii::$app->params['en']) {
            Yii::$app->language = 'en-US';
            Yii::$app->params['en'] = true;
        }

         // Отключения каталог для сотрудников;
        if(!empty(!Yii::$app->user->isGuest)) {
            $user = User::find()->where('id = ' . \Yii::$app->user->identity->getId())->one();
            \Yii::$app->params['userCatalogHide']['status'] = $user->staff;
        }
    }

    public function actionGoHome()
    {
        return $this->redirect('site/index');
    }
}
