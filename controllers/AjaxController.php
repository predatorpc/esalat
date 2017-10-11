<?php

namespace app\controllers;

use app\components\WProductItem;
use app\components\WVariantForm;
use app\modules\catalog\models\GalleryShop;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsImages;
use app\modules\catalog\models\GoodsImagesLinks;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsGroups;
use app\modules\common\controllers\FrontController;
use app\modules\common\models\Address;
use app\modules\common\models\Api;
use app\modules\common\models\User;
use app\modules\common\models\UserParams;
use app\modules\common\models\UserShop;
use app\modules\managment\models\ShopGroupVariantLink;
use app\modules\managment\models\ShopsImages;
use app\modules\managment\models\ShopsStores;
use app\modules\questionnaire\models\QuestionnaireAnswers;
use app\modules\questionnaire\models\QuestionnaireQuestions;
use app\modules\catalog\models\Category;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
use app\modules\shop\models\OrdersItems;
use app\modules\shop\models\OrdersItemsStatus;
use app\modules\shop\models\OrdersStatus;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\caching\Cache;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

// old sites


/**
 * BasketController implements the CRUD actions for Basket model.
 */
class AjaxController extends FrontController
{
    public function actionGetVariantFormForCms()
    {
        $form = new ActiveForm();
        $form->begin();
        $form->end();
        $productId = (!$_POST['product']) ? false : $_POST['product'];
        $index = (!$_POST['index']) ? false : $_POST['index'];
        $tagGroup = TagsGroups::getGroupsForCatalog();
        $tagValue = TagsGroups::getTagValueForCatalog();

        $shopGroup = ShopGroupVariantLink::find()->where(['product_id' => $productId])->one();
        $shopsList = [];
        $shopStoreCount = false;
        if (!$shopGroup) {

        } else {
            $shopsList = ShopsStores::getStoreList($shopGroup);

            if (!$shopsList) {

            } else {
                foreach ($shopsList as $item) {
                    $shopsListIds[] = $item->id;
                    $shopStoreCount[$index][$item->id] = new GoodsCounts();
                    $shopStoreCount[$index][$item->id]->count = 0;
                }
            }
        }
        print \app\components\WCmsVariant::widget([
            'variant' => new GoodsVariations(),
            'shopsList' => $shopsList,
            'shopStoreCount' => $shopStoreCount,
            'tagGroup' => $tagGroup,
            'tagValue' => $tagValue,
            'variantProperties' => false,
            'i' => $index,
            'form' => $form,
            'images' => [],
        ]);
    }

    public function actionSetSessionParamsOk()
    {
        //$_SESSION['orders-status-ok'] = 1;
    }

//    public function beforeAction($action)
//    {
//        if ($action == 'set-session-params-ok') {
//            Yii::$app->controller->enableCsrfValidation = false;
//        }
//
//        parent::beforeAction($action);
//    }

    public function actionGetVariantByHash($id = false, $hash = false)
    {
        $variant = $id ? GoodsVariations::find()->where(['id' => $id])->one() : new GoodsVariations();
        return $variant->uniqueHashParentString = $hash;
    }

    public function actionSellers()
    {
        $response['status'] = 'NOT';

        if (!empty($_POST['order_item_id']) && !empty(intval($_POST['order_item_id'])) && !empty($_POST['status']) && !empty(intval($_POST['status']))) {
            $orderItem = OrdersItems::findOne(intval($_POST['order_item_id']));

            if (empty($orderItem)) {
                return false;
            }

            $newStatus = intval($_POST['status']);
            $orderItem->status_id = $newStatus;
            if($orderItem->save()){
                //лог кто чего менял

                /*
                                $ordersItemsStatus = new OrdersItemsStatus();
                                $ordersItemsStatus->order_item_id = $orderItem->id;
                                $ordersItemsStatus->status_id = $orderItem->status_id;
                                $ordersItemsStatus->user_id = \Yii::$app->user->identity->id;
                                $ordersItemsStatus->date = date('Y-m-d H:i:s');
                                $ordersItemsStatus->status = 1;
                                $ordersItemsStatus->save();
                */
                //end log

                $response['status'] = 'OK';
                $response['statusHtml'] = '<span class="text-warning">' . $orderItem->statusTitle->name . '</span>';

                print json_encode($response);
            }else{
                return false;
            }
        }
    }

    public function actionSellersComment()
    {
//        $shopId = UserShop::getIdentityShop();
        $response['status'] = 'NOT';

        $orderItem = OrdersItems::findOne(intval($_POST['order_item_id']) * 1);
        if ($orderItem) {
            if (isset($_POST['comment']) && !empty($_POST['comment']) != '') {
                $orderItem->comments_shop = $_POST['comment'];
                if ($orderItem->save()) {
                    $response['status'] = 'OK';
                } else {
                    $response['error'] = $orderItem->errors();
                }
            }
        }

        print json_encode($response);
    }

    public function actionUpdateShopParam()
    {
        $userId = Yii::$app->user->getId();//UserShop::getIdentityUser();

        $responce['status'] = 'NOT';
        if (isset($_POST['updateParam']) && isset($_POST['newValue']) && !empty($_POST['updateParam']) && !empty($_POST['newValue'])) {
            $identUser = UserShop::findOne($userId);
            if ($identUser) {
                if ($_POST['updateParam'] == 'userPhone') {
                    $identUser->phone = trim($_POST['newValue']);
                    if ($identUser->save()) {
                        $responce['status'] = 'OK';
                        $responce['value'] = trim($_POST['newValue']);
                    }
                } elseif ($_POST['updateParam'] == 'shopDescription') {
                } elseif ($_POST['updateParam'] == 'userEmail') {
                    $identUser->email = trim($_POST['newValue']);
                    if ($identUser->save()) {
                        $responce['status'] = 'OK';
                        $responce['value'] = trim($_POST['newValue']);
                    }
                } elseif ($_POST['updateParam'] == 'methodNotification') {
                    $userParam = UserParams::find()->where(['user_id' => $userId])->andWhere(['title' => 'methodNotification'])->one();
                    if ($userParam) {

                    } else {
                        $userParam = new UserParams();
                        $userParam->user_id = $userId;
                        $userParam->title = 'methodNotification';
                        $userParam->status = 1;
                    }
                    $value = 0;
                    $responce['value'] = '';
                    foreach ($_POST['newValue'] as $newValue) {
                        $value += $newValue;
                        $responce['value'] .= Yii::$app->params['methodNotification'][$newValue] . ',&nbsp;';
                    }
                    $userParam->value = $value;

                    if ($userParam->save()) {
                        $responce['status'] = 'OK';
                    }
                }
            }
        }

        print json_encode($responce);
    }

    public function actionDeleteImageNow()
    {
        if (GoodsImages::imageDelete($_POST['image'])) {
            return 'OK';
        }

        return false;
    }

    public function actionDeleteImage($imageId)
    {
        if (GalleryShop::deleteImage($imageId)) {
            return true;
        }

        return false;
    }

    public function actionSetVariantImage($imageId, $variantId, $product)
    {
        $shopId = UserShop::getIdentityShop();

        $image = ShopsImages::find()->where(['shop_id' => $shopId])->andWhere(['id' => $imageId])->andWhere(['status' => 1])->one();
        //$variant = GoodsVariations::findOne($variantId);

        $productImagesLink = new GoodsImagesLinks();
        $productImagesLink->good_id = $product;
        $productImagesLink->variation_id = $variantId;
        $productImagesLink->image_id = $image->id;
        $productImagesLink->status = 1;

        if ($productImagesLink->save()) {
            return true;
        }
        return false;
    }

    public function actionDeleteVariantImage($imageId, $variantId, $product)
    {
        $shopId = UserShop::getIdentityShop();

        $productImagesLink = GoodsImagesLinks::find()->where(['variation_id' => $variantId])->andWhere(['image_id' => $imageId])->one();

        if ($productImagesLink->delete()) {
            return true;
        }
        return false;
    }

    public function actionGetGalleryShop()
    {
        $shopId = UserShop::getIdentityShop();

        if (isset($_POST['variant']) && !empty($_POST['variant'])) {
            $variantImages = GoodsImagesLinks::find()->where(['variation_id' => $_POST['variant'] * 1])->andWhere(['status' => 1])->indexBy('image_id')->asArray()->all();
        }
        $gallery = ShopsImages::find()->where(['shop_id' => $shopId])->andWhere(['status' => 1])->select('id')->asArray()->all();
        foreach ($gallery as $key => $image) {
            $gallery[$key]['url'] = \Yii::$app->params['galleryPath'] . substr(md5($image['id']), 0, 2) . '/' . $shopId . '_' . $image['id'] . '_min.jpg';
            $gallery[$key]['check'] = 0;
            if (isset($variantImages[$image['id']])) {
                $gallery[$key]['check'] = 1;
            }
        }
        return json_encode(['gallery' => $gallery, 'selected' => $variantImages]);
    }

    public function actionAddRemoveVariantImage()
    {
        $responce = 'NOT';
        if (isset($_POST['image']) && isset($_POST['variant']) && isset($_POST['product'])) {
            $image = $_POST['image'] * 1;
            $variant = $_POST['variant'] * 1;
            $product = $_POST['product'] * 1;
            if ($_POST['action'] == 'add') {
                if ($this->actionSetVariantImage($image, $variant, $product)) {
                    $responce = 'OK';
                }
            } elseif ($_POST['action'] == 'remove') {
                if ($this->actionDeleteVariantImage($image, $variant, $product)) {
                    $responce = 'OK';
                }
            } elseif ($_POST['action'] == 'delete') {
                if ($this->actionDeleteImage($image)) {
                    $responce = 'OK';
                }
            }
        }

        print $responce;
    }

    public static function actionSaveFilesGallery()
    {

        $responceList = ['status' => 'ok'];
        $model = new GalleryShop();
        $uploadFile = $_FILES['file'];

        $_FILES = [];
        foreach ($uploadFile as $param => $value) {
            $_FILES['GalleryShop'][$param]['imageFiles'][] = $value;
        }


        $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
        //$model->imageFiles = UploadedFile::getInstances($model, 'file');

        $response = $model->upload();
        if ($response['status']) {
            $responceList['status'] = 'OK';
            $responceList['upload'] = 'OK';
            return json_encode($response);
        } else {
            $responceList = $_FILES;
            $responceList['upload'] = 'NOT';
            return json_encode($responceList);
        }
        return json_encode($responceList);
    }

    public static function actionSwithActivProduct()
    {
        $responce = 'NOT';
        if (isset($_POST['id']) && $_POST['id'] == $_POST['id'] * 1) {
            $id = (int)$_POST['id'];
            $product = Goods::findOne($id);
            $product->status = ($product->status == 0) ? 1 : 0;
            if ($product->save()) {
                $responce = 'OK';
            }
        }
        print $responce;
    }

    public function actionGetVariantFormForProvider()
    {
        $responce = ['status' => 'NOT'];
        if (isset($_POST['key']) && $_POST['key'] == $_POST['key'] * 1) {
            $key = (int)$_POST['key'];
            $responce['value'] = WVariantForm::widget(['key' => $key]);
            $responce['status'] = 'OK';
        }
        print json_encode($responce);

//        if(!empty($_POST['key'])){
//            $responce['value'] = \app\components\shopManagment\WidgetProductUpdateVariantBlock::widget([
//                'variant' => false,
//                'uniqueHashParent' => $_POST['key'],
//                'productStores' => $productStores ? $productStores: false,
//                'productCountInStores' => $productCountInStores ? $productCountInStores : false,
//                'tagList' => $tagList ? $tagList : false,
//                'tagsListValue' => $tagsListValue,
//                'form' => $form,
//            ]);
//            $responce['status'] = 'OK';
//        }
//        print $responce;
    }

    public function actionGetSessionId()
    {
//        Yii::$app->session->open();
//        Yii::$app->session->hasSessionId;
//        return Yii::$app->session->getId() . ' 123';
    }

    public function actionSetNewTagVariant()
    {
        $tag = new Tags();
        $tag->group_id = $_POST['tag_group'];
        $tag->value = $_POST['tag_name'];
        $tag->status = 1;
        if ($tag->save()) {
            print $tag->id;
        } else {
            print 'error';
        }
    }

    // Способ оплаты заказа;
    public function actionSetPayment()
    {
        if (isset($_POST['payment_id'])) {
            // Сохранение способа оплаты заказа;
            //$session = \Yii::$app->session['basket'];
            //$_SESSION['basket']['payment_id'] = $_POST['payment_id'];
        }
    }

//    public function actionAddBasket(){
//        $responce = '';
//        // Проверка данных;
//        if (isset($_POST['good_id']) and isset($_POST['count'])) {
//            $session = \Yii::$app->session['basket']['goods'];
//
//            if($_POST['count'] > 0){
//                $_SESSION['basket']['goods'][$_POST['good_id'].':'.$_POST['variation_id']] = $_POST['count'];
//                $basket = new Basket() ;
//                $product = $basket->getBasketProduct($_POST['good_id'],$_POST['variation_id'],$_POST['count']);
//                $responce .= \app\components\WBasketProductTest::widget([
//                    'product' => $product,
//                ]);
//            }else{
//                unset($_SESSION['basket']['goods'][$_POST['good_id'].':'.$_POST['variation_id']]);
//                $responce = '';
//            }
//        }
//
//        print $responce;
//    }

    // Дата получения товара;
//    public function actionSetTime(){
//        $response = '';
//        if (isset($_POST['type_id']) and isset($_POST['set_time'])) {
//            // Сохранение даты доставки товара;
//            $session = \Yii::$app->session['basket']['goods'];
//            $_SESSION['basket']['times'][$_POST['type_id']] = $_POST['set_time'];
//
//            $types = Basket::getProductsTypes();
//            $removeDate = Basket::getRemoveDeliveryDate();
//            $htmlDtata = Basket::getTime($types,\Yii::$app->session['delivery_id']);
//
//            $responce = WBasketTimeDelivery::widget([
//                'time' => $htmlDtata,
//                'typeName' => GoodsTypes::find()->indexBy('id')->all(),
//            ]);
//        }
//
//        print $response;
//    }

    // Способ получения товара;
    public function actionSetDelivery()
    {
        if (isset($_POST['delivery'])) {
            // Обработка данных;
            $_POST['delivery'] = explode(":", $_POST['delivery']);
            // Сохранение способа получения товара;
            $session = \Yii::$app->session['basket'];
            //$_SESSION['basket']['delivery_id'] = $_POST['delivery'][0];
            //$_SESSION['basket']['address_id'] = $_POST['delivery'][1];
        }
    }

    public function actionGetWindowAddress()
    {
        $responce = \app\components\WWindowSetAddress::widget();

        print $responce;
    }


    //
    //
    //  Какого хрена дублируется функция, а Русланчик??????
    //  predator_pc
    //


    // Добавления адрес доставки;
    public function actionAddBasketAddress()
    {
        $result = '';

        if (isset($_POST['form_address'])) {
            // Проверка данных;
            if (!$_POST['district_id'] or !$_POST['street'] or !$_POST['phone']) {
                $result = 'Пожалуйста, заполните обязательные поля';
            } elseif (!eregi("^[0-9]{10}$", $_POST['phone'])) {
                $result = 'Неверный номер телефона';
            } else {
                //$result = "OK ".\Yii::$app->request->post('delivery');

                // Обработка данных;
                $district_id = intval($_POST['district_id']);
                $street = trim($_POST['street']);
                $house = trim($_POST['house']);
                $room = trim($_POST['room']);
                $comments = trim($_POST['comments']);
                $phone = '+7' . trim($_POST['phone']);
                // Добавить адрес;
                $address = new Address();

                $address->user_id = \Yii::$app->user->identity->id;
                $address->district_id = $district_id;
                $address->delivery_id = intval(\Yii::$app->request->post('delivery'));
                $address->street = $street;
                $address->house = $house;
                $address->room = $room;
                $address->comments = $comments;
                $address->phone = $phone;
                $address->date = date('Y-m-d H:i:s');
                $address->status = 1;
                $address->save();
            }
        }
        print $result;
    }

    public function actionGetTagsGroupsValueList()
    {
        $tagGroup = $_POST['tag_group'];
        $tag = $_POST['tag'];
//        Zloradnij::print_arr($_POST);

        if (!$tagGroup || !$tag) {
            return false;
        }

        $tagGroup = (int)trim($tagGroup);
        $tag = trim($tag);
        $tagsGroups = TagsGroups::find()->where(['type' => 1])->andWhere(['status' => 1])->orderBy('position')->select(['id'])->indexBy('id')->asArray()->all();
//Zloradnij::print_arr($tagsGroups);
        if (!is_array($tagsGroups[$tagGroup]) || $tag == '') {
            return false;
        }

        return json_encode(Tags::find()->where(['group_id' => $tagGroup])->andWhere(['status' => 1])->andWhere(['LIKE', 'value', $tag . '%', false])->orderBy('value')->limit(10)->asArray()->all());
    }

    public function actionClearCache()
    {
        if (Yii::$app->cache->flush()) {
            return 1;
        }
//        $post = Yii::$app->request->post();
//        if(!empty($post['cacheKey'])){
//            $buldKey = ['yii\widgets\FragmentCache', trim($post['cacheKey'])];
//
//            $leftMenuKey = explode('-',trim($post['cacheKey']));
//            $buldKeyLeftMenu = ['yii\widgets\FragmentCache', 'WLeftCatalogMenu_' . $leftMenuKey[1]];
//            Yii::$app->cache->delete($buldKeyLeftMenu);
//
//            $buldKeyTopMenu = ['yii\widgets\FragmentCache', 'layoutGeneralCatalogMenu'];
//            Yii::$app->cache->delete($buldKeyTopMenu);
//
//            if(Yii::$app->cache->delete($buldKey)){
//                return 1;
//            }
//        }
    }

    // Обновления  мастер покупка;
    public function actionMasterHelp()
    {
        // Параметры пост данные;
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $session->open();
        if ($request->post('helperBasketUpdate')) {
            Yii::$app->params['menuListId']['id'] =$request->post('id');
            Yii::$app->params['menuListId']['catalog_id'] = $request->post('catalog_id');
            Yii::$app->params['menuListId']['parent_id'] = $request->post('catalog_id');

            return \app\components\html\WMasterHelp::widget(['menuId'=>Yii::$app->params['menuListId']['id']]);
        }
    }

    //viewed = 0 - Окно было закрыто
    //viewed = 1 - ответ из каталога
    //viewed = 2 - ответ из корзины
    //Вывод вопроса
    public function actionAskQuestion($section = ''){
        if(Yii::$app->user->id > 0) {
            $answer = new QuestionnaireAnswers();

                $user_id = Yii::$app->user->id;
                $answer->user_id = $user_id;
                $arAnswers = QuestionnaireAnswers::find()->where(['user_id' => $user_id])->asArray()->All();
                $arAnswers = ArrayHelper::getColumn($arAnswers, 'question_id');
                $question = QuestionnaireQuestions::find();
                if (count($arAnswers) > 0) {
                    $question = $question->where(['NOT IN', 'id', $arAnswers]);
                }
                $question = $question->orderBy('RAND()')->One();
                $answer->question_id = $question->id;
                return $this->renderPartial('question-form', ['question' => $question, 'answer' => $answer]);
        }
    }
    //Сохрание ответа
    public function actionSaveAnswer($section = ''){
        $answer = new QuestionnaireAnswers();
        if ($answer->load(Yii::$app->request->post())) {
            $answer->user_id = Yii::$app->user->id;
            $answer->date = date("Y-m-d h:i:s");
            $answer->basket_id = $user = User::find()->where(['id'=>Yii::$app->user->id])->One()->basket->id;
            if($section == 'catalog'){
                $answer->viewed = 1;
            }else if ($section == 'basket'){
                $answer->viewed = 2;
            }

            if ($answer->save()) {
                return 'asked';
            } else {
                $question = QuestionnaireQuestions::find()->where(['id' => $answer->question_id])->One();
                return $this->renderPartial('question-form', ['question' => $question, 'answer' => $answer]);
            }
        }
    }

    //Проверка, а можно ли задавать вопрос
    public function actionCheckAskedQuestion($section){
        $user_id = Yii::$app->user->id;
        if($user_id > 0){
            $user = User::find()->where(['id'=>$user_id])->One();
            $basket_id = $user->basket->id;
            if($section == 'catalog' || $section == 'basket'){
                $answer = QuestionnaireAnswers::find()->where(['user_id'=>$user_id,'basket_id'=>$basket_id]);
                $totalAnswer = $answer->count();
                if($section == 'catalog'){
                    $answer = $answer->andWhere(['viewed'=>1]);
                }else if($section == 'basket'){
                    $answer = $answer->andWhere(['viewed'=>2]);
                }
                $answer = $answer->count();
                $questions = QuestionnaireQuestions::find()->count();
                if($answer>= 1 || $answer >= $questions || $totalAnswer >=2){
                    return false;
                }else if(count($questions) == 0){
                    return false;
                }
                return 'ask';
            }
        }
        return false;
    }

    //Сохрание, если окнос вопросом было закрыто
    public function actionViewQuestion($question_id,$section){
        $answer = QuestionnaireAnswers::find()->where(['user_id'=>Yii::$app->user->id,'question_id'=>$question_id])->One();
        if($answer == NULL){
            $answer = new QuestionnaireAnswers();
            $answer->question_id = $question_id;
            $answer->user_id = Yii::$app->user->id;
            $answer->date = date("Y-m-d h:i:s");
            if($section == 'catalog'){
                $answer->viewed = 1;
            }else if($section == 'basket'){
                $answer->viewed = 2;
            }
            //$answer->viewed = 0;
            $answer->basket_id = $user = User::find()->where(['id'=>Yii::$app->user->id])->One()->basket->id;
            if(!$answer->save()){
                print_r($answer->getErrors());
            }
        }

    }

    // Обнвления таймер
    public function actionTimerAjax(){
        $session = Yii::$app->session;
        $session->open();
        if (Yii::$app->request->post('time_start')) {
            // Обработка времени;
            $time_start = (!empty($_SESSION['time_start']) ? $_SESSION['time_start'] : false);
            // интервал секнунд 90 сек;
            $interval = \Yii::$app->params['intervalTimer'];
            $_SESSION['time_start'] = time();
            // Скрипт обратный отчет;
            $time_set = ($interval - (time() - $time_start));
            $time_set = $time_set > 0 ? $time_set : 0;
            return \app\components\html\WTimer::widget();
        }
    }

    // Мастер помощьник;
    public function actionMasterHelpAjax(){

        if (Yii::$app->request->post('master_help')) {
            setcookie('master_help', true, time() + 3600 * 24 * 30, '/');
            return false;
        }
    }

    // Main goods all;
    public function actionMainAllGoods()
    {

        if (Yii::$app->request->post('goods')) {
            // Загрузка категориt level 0;
            $categories = Category::find()->where(['active' => 1,'level'=>0])->orderBy('level, sort')->limit(1)->all();


            return \app\components\WCatalogProductItem::widget(['categories'=>$categories]);
        }
    }

    // Подгрузка товар в контент;
    public function actionMainLoadGoods()
    {
        if (Yii::$app->request->post('goodsLoad')) {
            $cat_limit = !empty(Yii::$app->request->post('cat_col')) ? Yii::$app->request->post('cat_col') : 0;
            $categories = Category::find()->where(['active' => 1,'level'=>0])->orderBy('level, sort')->limit(1)->offset($cat_limit)->all();
            $limit = !empty(Yii::$app->request->post('col')) ? Yii::$app->request->post('col') : 0;
            return \app\components\WCatalogProductItem::widget(['categories'=>$categories,'limit'=>$limit]);
        }
    }



}
