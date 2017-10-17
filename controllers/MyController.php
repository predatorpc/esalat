<?php

namespace app\controllers;

use app\models\BasketTest;
use app\modules\basket\models\BasketLg;
use app\modules\basket\models\Basket;
use app\modules\basket\models\PromoCode;
use app\modules\catalog\models\Codes;
use app\modules\catalog\models\Lists;
use app\modules\catalog\models\GoodsTypes;
use app\modules\catalog\models\ListsGoods;
use app\modules\coders\models\Payment;
use app\modules\coders\models\PaymentCenter;
use app\modules\coders\models\SberbankPayment;
use app\modules\coders\models\ShopPayment;
use app\modules\common\controllers\FrontController;
use app\modules\common\models\Address;
use app\modules\common\models\Messages;
use app\modules\common\models\User;
use app\modules\common\models\UserAdmin;
use app\modules\common\models\UserRoles;
use app\modules\common\models\UsersCards;
use app\modules\common\models\UsersPays;
use app\modules\common\models\Zloradnij;
use app\modules\my\models\Feedback;
use app\modules\my\models\MessagesImages;
use app\modules\my\models\OrdersHistory;
use app\modules\shop\models\Orders;
use app\modules\common\models\ModFunctions;
use app\modules\wishlist\models\WishlistProducts;
use yii\web\UploadedFile;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

class MyController extends FrontController
{
    public $defaultAction = 'my-address';
    public $enableCsrfValidation = false;

    public $basketObject;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'ok-payment',
                            'fail-payment',
                            'response-payment-center',
                            'sberresponse',
                            'autoresponse',
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'set-roles',
                            'test-order',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode'],
                    ],
                    [
                        'actions' => [
                            'address-delete',
                            'my-address',
                            //*'balance-operation',
                            //*'orders-history',
                            'order-report',
                            'order-payment',
                            //*'promo',
                            'signup',
                            'submitsignup',
                            'logout',
                            'contact',
                            'feedback',
                            'about',
                            'form-submission',
                            'success-payment-center',
                            'fail-payment-center',
                            //*'product-list',
                            //*'wish-list',
                             'orders-pdf',
                            //'translating',
                            //'translating2',
                            'sberresponse',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'address-delete' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionOrderReport($id = false)
    {
        /*
        if(!Yii::$app->user->identity){
            return $this->redirect('/');
        }
        $user = User::findOne(Yii::$app->user->identity->id);

        //$this->basketObject = Yii::$app->basket->getBasket();
        //$resultPrice = \Yii::$app->basket->getResultPrice();
        $this->basketObject = \Yii::$app->action->applyActions();
        $resultPrice = \Yii::$app->action->getResultPrice();
        if (($resultPrice - $user->money) > 0) {
            // Сумма доплаты;
            $moneyTest = number_format(($resultPrice - $user->money), 2, '.', '');
        }
        else {
            // Сумма платежа;
            $moneyTest = number_format($resultPrice, 2, '.', '');
        }

        $postParams = $errorParams = false;

        if(!empty(Yii::$app->request->post())){
            $postParams = Yii::$app->request->post();
            $postParams['order-status'] = 'new';
        }

        if(!$id && !empty($postParams['order-status']) && $postParams['order-status'] == 'new'){
            if(empty($postParams['StoreList'])){
                $errorParams[] = 'ERROR Store List';
            }
            if($this->basketObject->payment_id == 3){
                if(empty($postParams['card_id'])){
                    $errorParams[] = 'ERROR Card Id';
                }
            }
        }
        elseif(!empty($id)) {
            if (!empty($postParams['order-status']) && $postParams['order-status'] == 'new') {
                $errorParams[] = 'ERROR New Order In Old Order';
            }
        }
        else{
            $errorParams[]= 'ERROR empty';
        }

        if(!empty($errorParams)){
            foreach ($errorParams as $i => $errorParam) {
                Yii::$app->session->addFlash('ERROR '.$i, $errorParam);
            }
            return $this->redirect('/basket/');
        }
        else{
            if(!$id){
//                $connection = \Yii::$app->db;
//                $transaction = $connection->beginTransaction();

                $order = new Orders();

                $orderReport = $order->createNewOrder($this->basketObject,[
                    'order-status' => $postParams['order-status'],
                    'order_comments' => !empty($postParams['order_comments']) ? $postParams['order_comments'] : '',
                    'store-list' => $postParams['StoreList'],
                ]);

                Yii::$app->session['shop'];
                $_SESSION['shop']['new-order'] = $orderReport;

                if(!$orderReport){
//                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/errorParams2.txt',var_export($errorParams,true));
                    $this->redirect('/basket/');
                }
                else{
                    // Проверка доплаты;
                    //if ((\Yii::$app->basket->getResultPrice() - $user->money) > 0) {
                    if ((\Yii::$app->action->getResultPrice() - $user->money) > 0) {
                        // Сумма доплаты;
                        //$money = number_format((\Yii::$app->basket->getResultPrice() - $user->money), 2, '.', '');
                        $money = number_format((\Yii::$app->action->getResultPrice() - $user->money), 2, '.', '');
                    }
                    else {
                        // Сумма платежа;
                        //$money = number_format(\Yii::$app->basket->getResultPrice(), 2, '.', '');
                        $money = number_format(\Yii::$app->action->getResultPrice(), 2, '.', '');
                    }

                    $payment = new Payment([
                        'save_card' => !empty($postParams['save_card']) ? 1 : 0,
                        'orderReport' => $orderReport,
                        'amount' => $moneyTest,//$money,
                        'orderDescription' => 'Оплата заказа',
                        'cardId' => !empty($postParams['card_id']) ? $postParams['card_id'] : false,
                        'rebillAnchor' => !empty($postParams['card_id']) ?
                            UsersCards::find()
                                ->where(['id' => $postParams['card_id'],'user_id' => Yii::$app->user->identity->id,'status' => 1])
                                ->select(['rebill_anchor'])
                                ->scalar() :
                            false,
                    ]);
                    $payment->setUser($user);

                    // Обработка способа оплаты (с сохраненной банковской карты);
                    if ($this->basketObject->payment_id > 1 && $this->basketObject->payment_id < 6) {
                        $payment->orderId = $payment->fillEmptyAccount(1,$moneyTest);

                        echo "<meta http-equiv='refresh'  content='0; URL=".$payment->getPaymentUrl()."'>";
                    }
                    else{
                        $payment->setOrder();
                        $payment->orderPayment();
                        //$order->checkMetroItems($orderReport, $this->basketObject);//отправка заказа Metro в helper

                        //зачисления по акциям
                        $cashBaks = \Yii::$app->action->cashBackValues();
                        //print_r($cashBaks);die();
                        if(!empty($cashBaks)){
                            if($cashBaks['money']>0){
                                $payment->fillAccount(32, $cashBaks['money']);
                            }
                            if($cashBaks['bonus']>0){
                                $payment->fillAccountBonus(6, $cashBaks['bonus']);
                            }
                        }
                        $this->redirect('http://www.extremeshop.ru/my/orders-history/' . '?ORDER=' . $orderReport);
                    }
                }
            }
        }
*/
        //----------NEW--------------

        if(!Yii::$app->user->identity){
            return $this->redirect('/');
        }
        $user = User::findOne(Yii::$app->user->identity->id);
        $this->basketObject = \Yii::$app->action->applyActions();
        $resultPrice = \Yii::$app->action->getResultPrice();
        $cashBaks = \Yii::$app->action->cashBackValues();

        if (($resultPrice - $user->money) > 0) {
            $money = number_format(($resultPrice - $user->money), 2, '.', '');// Сумма доплаты;
        }
        else {
            $money = number_format($resultPrice, 2, '.', '');// Сумма платежа;
        }
        $postParams = $errorParams = false;
        if(!empty(Yii::$app->request->post())){
            $postParams = Yii::$app->request->post();
            $postParams['order-status'] = 'new';
        }

        if(!$id && !empty($postParams['order-status']) && $postParams['order-status'] == 'new'){
            if(empty($postParams['StoreList'])){
                $errorParams[] = 'ERROR Store List';
            }
            if($this->basketObject->payment_id == 3){
                if(empty($postParams['card_id'])){
                    $errorParams[] = 'ERROR Card Id';
                }
            }
        }
        elseif(!empty($id)) {
            if (!empty($postParams['order-status']) && $postParams['order-status'] == 'new') {
                $errorParams[] = 'ERROR New Order In Old Order';
            }
        }
        else{
            $errorParams[]= 'ERROR empty';
        }

        if(!empty($errorParams)){
            foreach ($errorParams as $i => $errorParam) {
                Yii::$app->session->addFlash('ERROR '.$i, $errorParam);
            }
            return $this->redirect('/basket/');
        }
        else{
            if(!$id){
                $order = new Orders();
                $orderReport = $order->createNewOrder($this->basketObject,[
                    'order-status' => $postParams['order-status'],
                    'order_comments' => !empty($postParams['order_comments']) ? $postParams['order_comments'] : '',
                    'store-list' => $postParams['StoreList'],
                ]);

                Yii::$app->session['shop'];
                $_SESSION['shop']['new-order'] = $orderReport;
                if(!$orderReport){
                    $this->redirect('/basket/');
                }
                else{
                    // Обработка способа оплаты (с сохраненной банковской карты);
                    if ($this->basketObject->payment_id > 1 && $this->basketObject->payment_id < 6) {
                        //$money=10;//убрать к хуям
                        $sber = new SberbankPayment();
                        $sber->setOrder(Orders::find()->where(['id'=>$orderReport])->one());
                        $sber->setUser($user);
                        if($redirect = $sber->registerOrder($money)){
                            $this->redirect($redirect);
                        }
                        else{
                            $this->redirect('/basket/');
                        }
                    }
                    else{
                        //по модели ShopPayment //TODO::провертиь
                        $payment = new ShopPayment();
                        $payment->setUser($user);
                        $payment->setOrder(Orders::find()->where(['id'=>$orderReport])->one());
                        $payment->setBasket($this->basketObject);
                        $payment->orderPayment();

                        //зачисления по акциям
                        if(!empty($cashBaks) && !\Yii::$app->action->getPromoFlag()){
//                            if(!\Yii::$app->action->getPromoFlag()){
//                                $order = Orders::find()->where(['id'=>$orderReport])->One();
//                                $code = Codes::find()->where(['id'=>$order->code_id])->One();
//                                $ownerPromo = User::find()->where(['id'=>$code->user_id])->One();
//                                $payment->setUser($ownerPromo);
//                            }
                            if($cashBaks['money']>0){
                                $payment->fillAccount(32, $cashBaks['money']);
                            }
                            if($cashBaks['bonus']>0){
                                $payment->fillAccountBonus(6, $cashBaks['bonus']);
                            }
//                            if(!\Yii::$app->action->getPromoFlag()){
//                                $payment->setUser($user);
//                            }
                        }
                        $this->redirect('/my/orders-history/?ORDER='.$orderReport);
                        //OLD
                        /*
                        $payment = new Payment([
                            'save_card' => !empty($postParams['save_card']) ? 1 : 0,
                            'orderReport' => $orderReport,
                            'amount' => $money,//$money,
                            'orderDescription' => 'Оплата заказа',
                            'cardId' => !empty($postParams['card_id']) ? $postParams['card_id'] : false,
                            'rebillAnchor' => !empty($postParams['card_id']) ?
                                UsersCards::find()
                                    ->where(['id' => $postParams['card_id'],'user_id' => Yii::$app->user->identity->id,'status' => 1])
                                    ->select(['rebill_anchor'])
                                    ->scalar() :
                                false,
                        ]);
                        $payment->setUser($user);
                        $payment->setOrder();
                        $payment->orderPayment();
                        //зачисления по акциям

                        //print_r($cashBaks);die();
                        if(!empty($cashBaks)){
                            if($cashBaks['money']>0){
                                $payment->fillAccount(32, $cashBaks['money']);
                            }
                            if($cashBaks['bonus']>0){
                                $payment->fillAccountBonus(6, $cashBaks['bonus']);
                            }
                        }*/
                        //$this->redirect('http://www.extremeshop.ru/my/orders-history/' . '?ORDER=' . $orderReport);
                    }
                }
            }
        }
    }

    public function actionFailPayment(){
        if(!empty($_POST['ErrorCode'])){
            $emptyTransaction = \app\modules\common\models\UsersPays::find()->where(['id' => intval($_POST['OrderId']), 'status' => 0]);
            if ($emptyTransaction->count() == 1) {
                $transactionArray = $emptyTransaction->asArray()->one();
            }elseif($emptyTransaction->count() > 1){
                $transactionArray = 'count > 1';
            }else{
                $transactionArray = 'count == 0';
            }

            $order = \app\modules\shop\models\Orders::findOne($transactionArray['order_id']);

            $payment = new \app\modules\coders\models\Payment([
                'orderReport' => $order ? $order->id : false,
                'orderId' => $transactionArray['id'],
                'amount' => $_POST['Amount'],
                'orderDescription' => $_POST['OrderDescription'],
            ]);

            if ($payment->checkSecurityKey($_POST['SecurityKey'],$_POST)) {
                $user = \app\modules\common\models\User::findOne($transactionArray['user_id']);
                $payment->setUser($user);

                $payment->checkEmptyAccount($transactionArray['id'],$_POST['TransactionID'],$_POST['ErrorCode']);
            }
        }
    }

    public function actionOkPayment(){
        /*$_POST['OrderId']= 1000207866;
        $_POST['TransactionID']= 78825387;
        $_POST['Amount'] = 1557;
        $_POST['OrderDescription']='';*/

        if(!empty($_POST['OrderId']) && !empty($_POST['TransactionID']) && !empty($_POST['Amount']) && empty($_POST['ErrorCode'])) {
            $emptyTransaction = \app\modules\common\models\UsersPays::find()->where(['id' => intval($_POST['OrderId']), 'status' => 0]);
//            if ($emptyTransaction->count() == 1) {
//                $transactionArray = $emptyTransaction->asArray()->one();
//            }elseif($emptyTransaction->count() > 1){
//                $transactionArray = 'count > 1';
//            }else{
//                $transactionArray = 'count == 0';
//            }
            $transactionArray = $emptyTransaction->asArray()->one();

            $order = \app\modules\shop\models\Orders::findOne($transactionArray['order_id']);

            $basket = Basket::find()->where(['id' => $order->basket_id])->one();
            $basket->start();
            $basket = \Yii::$app->action->applyActionOnFindBasket($basket);

            $payment = new \app\modules\coders\models\Payment([
                'orderReport' => $order ? $order->id : false,
                'orderId' => $transactionArray['id'],
                'amount' => $_POST['Amount'],
                'orderDescription' => $_POST['OrderDescription'],
            ]);

            // Проверяем ключ
            if ($payment->checkSecurityKey($_POST['SecurityKey'], $_POST)) {

//                $connection = \Yii::$app->db;
//                $transaction = $connection->beginTransaction();
//
//                try {
                // Устанавливаем юзера платежа
                $user = \app\modules\common\models\User::findOne($transactionArray['user_id']);
                $payment->setUser($user);

                if ($payment->checkEmptyAccount($transactionArray['id'], $_POST['TransactionID'])) {

                } else {
//                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/controllerCheckEmptyAccount.txt',var_export($transactionArray,true));
                }

//                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/controllerCheckEmptyAccountPOST.txt',var_export($_POST,true));

                if (!$order) {

                } else {
                    $payment->setOrder();
                    $payment->orderPayment();
                    //зачисления по акциям
                    $cashBaks = \Yii::$app->action->cashBackValues();
                    if (!empty($cashBaks)) {
                        if ($cashBaks['money'] > 0) {
                            $payment->fillAccount(32, $cashBaks['money']);
                        }
                        if ($cashBaks['bonus'] > 0) {
                            $payment->fillAccountBonus(6, $cashBaks['bonus']);
                        }
                    }
                }

                if (!empty($_POST['RebillAnchor'])) {
                    //  Сохраняем карту
                    $card = \app\modules\common\models\UsersCards::find()
                        ->where([
                            'user_id' => $user->id,
                            'card_number' => $_POST['CardNumber'],
                            'status' => 1,
                        ])
                        ->one();
                    if (!$card) {
                        $card = new \app\modules\common\models\UsersCards();
                        $card->user_id = $user->id;
                        $card->card_number = $_POST['CardNumber'];
                        $card->rebill_anchor = $_POST['RebillAnchor'];
                        $card->date = date('Y-m-d H:i:s');
                        $card->status = 1;
                        if ($card->save()) {

                        } else {
//                            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/errorSaveCard.txt',var_export($card->errors,true));
//                            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/errorSaveCard1.txt',var_export($_POST,true));
                        }
                    }
                }


//                    $transaction->commit();
//                } catch (\Exception $e) {
//                    $transaction->rollBack();
//                    throw $e;
//                }
            } else {
//                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/errorCheckSecurityKey.txt',var_export($_POST,true));
            }
        }
        $this->redirect('/my/orders-history/');
    }

    public function actionFailPaymentCenter(){
        return $this->render('fail-payment-center',[

        ]);
    }

    public function actionSuccessPaymentCenter(){
        return $this->render('success-payment-center',[

        ]);
    }
    public function beforeAction($action)
    {
        if ($action->id == 'response-payment-center') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }
    public function actionResponsePaymentCenter(){
        //$basket2 = Basket::find()->where(['id' => 153837, 'status' => 0,])->one();
        //$basket2->start();
        //$order = new Orders();
        //$order->createNewOrderFromBasket(Yii::$app->action->applyActionOnFindBasket($basket2));

        //Yii::$app->action->applyActionOnFindBasket($basket2);
        //print_r(Yii::$app->action->applyActionOnFindBasket($basket2));
        //die();
        $pairs = file_get_contents("php://input");
        $hash_hmac = base64_encode(hash_hmac('sha256',$pairs,\Yii::$app->params['payment-center']['secret_key'],true));
        if(!empty($pairs)){
            $headers = getallheaders();

            if($hash_hmac == $headers['Signature']){
                $response = json_decode($pairs);

                if($response->Event == 'Payment' && $response->IsTest == 0){
                    $basket2 = Basket::find()
                        ->where([
                            'id' => intval($response->Order_Id),
                            'status' => 0,
                        ])
                        ->one();

                    if(!$basket2){
                        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter0.txt', var_export($response,true) . ' ----------22--');
                    }else{
                        $basket2->start();
                        $basket2->status = 1;
                        if($basket2->save()){
                            $order = new Orders();
                            $orderReport = $order->createNewOrderFromBasket(Yii::$app->action->applyActionOnFindBasket($basket2));

                            if(!$orderReport){
                                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter0000.txt', 'NOT ORDER REPORT ');
                                return false;
                            }else{
                                $payment = new PaymentCenter([
                                    'save_card' => 0,//!empty($postParams['save_card']) ? 1 : 0,
                                    'orderReport' => $orderReport,
                                    'amount' => $response->Amount,
                                    'orderDescription' => 'Оплата заказа',
                                    'cardId' => false,//!empty($postParams['card_id']) ? $postParams['card_id'] : false,
                                ]);
                                $payment->setUser($basket2->user);
                                $payment->fillAccount(1,$response->Amount,$response->Transaction_Id);
                                $payment->setOrder();
                                $payment->orderPayment();
                                //$order->checkMetroItems($orderReport, $this->basketObject);//отправка заказа Metro в helper
                                //зачисления по акциям
                                $cashBaks = \Yii::$app->action->cashBackReturn($basket2);
                                if(!empty($cashBaks)){
                                    if($cashBaks['money']>0){
                                        $payment->fillAccount(32, $cashBaks['money']);
                                    }
                                    if($cashBaks['bonus']>0){
                                        $payment->fillAccountBonus(6, $cashBaks['bonus']);
                                    }
                                }
                            }
                        }else{
                            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter0000.txt', 'BASKET ERROR '.var_export($basket2->errors, true));
                        }
                    }
                }else{
                    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter2.txt', var_export($response, true) . ' - '.$response->IsTest . ' ' .$response->Event);
                }
            }else{
                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter4.txt', var_export($headers, true).' '.$hash_hmac);
            }
        }
    }

    public function actionMyAddress()
    {//my/my-address
        $address = new ActiveDataProvider([
            'query' => Address::find()
                ->where(['user_id' => Yii::$app->user->identity->getId()])
                ->andWhere('status = 1'),
        ]);
        $command = new Address();
        //
        //
        if(Yii::$app->request->post()) {

        }
         /*
        if($model->load(Yii::$app->request->post())) {
            print_arr( Yii::$app->request->post());
        }*/
        return $this->render('my-address', [
            'address'=> $address,
        ]);
    }

    public function actionAddressDelete($id){
//        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/123.txt',var_export($_POST,true));
//        print_r($_POST);
        $address = Address::findOne($id);
        if(!$address){
            return false;
        }
        if(!empty(\Yii::$app->user->identity) && $address->user_id == \Yii::$app->user->identity->id){
            $address->status = 0;
            $address->save();
        }
        return $this->redirect(['/my/']);
    }

    public function actionBalanceOperation()
    {
        $user = User::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
        $pays = UsersPays::find()->where(['user_id'=>Yii::$app->user->id, 'status'=>1])->orderBy(['id'=>SORT_DESC])->limit(15)->all();

        return $this->render('balance-operation', [
            'user'=> $user,
            'pays'=>$pays,
//            'basket' => $this->basketObject,
        ]);
    }
    // Обратная связь;
    public function actionFeedback()
    {
        // Отрпавить данные;
        $model = new Feedback();

       if($model->load(Yii::$app->request->post()) && $model->save() ) {

           // Загрузка фотография!;
           $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
           if(!empty($model->imageFiles)) {
               $model->upload($model->id);
           }
           Yii::$app->session->setFlash('success',Yii::t('app','Ваше сообщение отправлено. Спасибо!'));
           return $this->refresh();
        }
        // Загрузхка уведомления:
        $notice = Feedback::find()->where(['type_id'=>1002,'user_id'=>Yii::$app->user->identity->id,'status'=>1])->orderby(['id'=>SORT_DESC])->asArray()->all();
        // Загрузка изображения;
        foreach($notice as $key => $value) {
            $notice[$key]['images']  = MessagesImages::find()->where(['message_id'=>$value['id']])->orderby(['id'=>SORT_DESC])->asArray()->all();
        }
        return $this->render('feedback', [
            'model' => $model,
            'notice'=>(isset($notice) ? $notice : ''),
        ]);

    }
    // 10013181;
    // Промокод! Yii::$app->user->identity->id
    public function actionPromo()
    {
        $db = Yii::$app->getDb();
        // Загрузка промо код;
        $sql = "SELECT * FROM `codes` WHERE `user_id` = '".Yii::$app->user->identity->id."' AND `status` = '1'  LIMIT 1";
        if ($promo = $db->createCommand($sql)->queryOne()) {
                    // Загрузка заказов;
                    $sql = "SELECT `orders`.`id`, `orders`.`date`, `users`.`name` AS `user_name`  FROM `orders` JOIN `users` ON `users`.`id` = `orders`.`user_id` WHERE `orders`.`code_id` = '".$promo['id']."' AND  (DATE(`orders`.`date`) BETWEEN '".(isset($_SESSION['report']['date_begin']) ?  $_SESSION['report']['date_begin'] : date('Y-m-01'))."' AND '".(isset($_SESSION['report']['date_end']) ?  $_SESSION['report']['date_end'] : date('Y-m-d'))."')  AND `orders`.`status` = '1' ORDER BY `orders`.`date` DESC";
                    if ($promo['orders'] = $db->createCommand($sql)->queryAll()) {
                        // Итого суммы вознаграждения;
                        $promo['fee'] = 0;
                        $promo['total'] = 0;
                        foreach($promo['orders'] as $key => $value ) {
                            $promo['orders'][$key]['money_total'] = 0;
                            $sql = "SELECT `goods`.`id` AS `good_id`, `goods`.`name` AS `good_name`, `goods`.`order`, `orders_items`.`fee`,`orders_items`.`bonusBack`, `orders_items`.`price`, `orders_items`.`discount`, `orders_items`.`count`, '0' AS `money`,`shops_stores`.`name` AS `store_name`, `orders_items`.`release` , `orders_items`.`status`, `shops`.`id` AS `shop_id` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `shops` ON `shops`.`id` = `goods`.`shop_id` LEFT JOIN `shops_stores` ON `shops_stores`.`id` = `orders_groups`.`store_id` WHERE `orders_groups`.`order_id` = '".$value['id']."' AND `orders_items`.`status` = '1' AND `orders_groups`.`status` = '1' ORDER BY `orders_items`.`id` ASC";
                            if ($promo['orders'][$key]['promo'] = $db->createCommand($sql)->queryAll()) {
                                   foreach($promo['orders'][$key]['promo'] as $k=>$v) {
                                           // Рассчет стоимости;
                                           $promo['orders'][$key]['promo'][$k]['money'] = ($v['price'] - $v['discount']) * $v['count'];
                                           // Итого суммы;
                                           $promo['total'] += ($v['price'] - $v['discount']) * $v['count'];
                                          // $promo['total_bonus'] += ($v['fee'] + $v['bonusBack']) * $v['count'];
                                           // Проверка вознаграждения;
                                           //if ($v['fee'] > 0) {
                                               // Сумма вознаграждения;
                                               $promo['orders'][$key]['promo'][$k]['fee'] = $v['fee'] * $v['count'];
                                               // Итого суммы вознаграждения;
                                               $promo['fee'] += ($v['fee'] + $v['bonusBack'])* $v['count'];
                                           //}
                                        $promo['orders'][$key]['money_total'] += $promo['orders'][$key]['promo'][$k]['money'];
                                   }
                            }

                        }

                    }

           // print_arr($promo);
            // Сформировать дата от и до;
            if (isset($_POST['report'])) {
                if(!$_POST['date_begin']);
                if(!$_POST['date_end']);
                $_SESSION['report']['date_begin'] = $_POST['date_begin'];
                $_SESSION['report']['date_end'] = $_POST['date_end'];
                header('location: /my/promo/');
                die();
            }

        }
      //  die('STOP');
        // Указываем шаблон;
        return $this->render('promo', [
            'promo'=> $promo,

        ]);
    }
    // История заказов;
    public function actionOrdersHistory()
    {
        //  Переход из корзины или страницы оплаты при оформлении заказа
        if(!empty(Yii::$app->request->get('ORDER')) && !empty(Yii::$app->session['shop']['new-order']) && Yii::$app->session['shop']['new-order'] == Yii::$app->request->get('ORDER')){
            $order = Orders::findOne(Yii::$app->request->get('ORDER'));
            //  Если заказ не оплатился
            if(!$order || $order->status == 0){
                // error
                $emptyTransaction = \app\modules\common\models\UsersPays::find()->where(['order_id' => Yii::$app->request->get('ORDER'), 'user_id' => Yii::$app->user->identity->id, 'status' => 0])->one();
                $errorCode = 0;
                if(!$emptyTransaction || empty($emptyTransaction->error_code)){

                }else{
                    $errorCode = $emptyTransaction->error_code;
                }
                unset($_SESSION['shop']['new-order']);
                return $this->render('orders-create-error', [
                    'errorCode' => $errorCode,
                ]);

                //  Заказ оплачен - обнуляем корзину
                //  Показываем, что с заказом клиента всё ок
            }else{
                $this->basketObject = new BasketLg();
                $this->basketObject = $this->basketObject->findCurrentBasket();
                // ok
                if($this->basketObject->status == 0){
                    $this->basketObject->status = 1;
                    $this->basketObject->save();
                }
                unset($_SESSION['shop']['new-order']);
                return $this->render('orders-create-success', [
                    'order'=> $order,
                ]);
            }

            //  Просто страница истории заказов
        }else{
            // Класс история заказов;
//            $orders = OrdersHistory::findOrdersHistory();
            // Указываем шаблон;
//            return $this->render('orders-history', [
//                'orders'=> $orders,
//            ]);
        }

        $orders = Orders::find()->where(['user_id' => Yii::$app->user->identity->id,'status'=>1])->orderBy('id DESC')->all();



        return $this->render('orders-history-test',[
            'orders' => $orders,
        ]);
    }
    // Печать pdf
    public function actionOrdersPdf($id) {
        if(!empty($id) && intval($id)) {

            // Oбработка страница;
            $this->layout = 'pdf';
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            $headers = Yii::$app->response->headers;
            $headers->add('Content-Type', 'application/pdf');

            // Загрузка история заказа;
            $order = Orders::find()->where(['user_id' => Yii::$app->user->identity->id, 'id' => $id, 'status' => 1])->orderBy('id DESC')->one();

            // Генерация pdf;
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
                'content' => $this->renderPartial('orders-pdf', ['order' => $order]),
                'cssInline' => 'table th{font-size:12px;text-align:center;},table td{padding:5px;}',
                'destination' => Pdf::DEST_BROWSER,
                'options' => [
                    'title' => 'Накладная №'.$id.' от '.date('d.m.Y',strtotime($order->date)),
                    'subject' => ''
                ],
                'methods' => [
                    'SetHeader' => ['www.Esalad.ru ' . date("r")],
                   // 'SetFooter' => ['|Page {PAGENO}|'],
                ]
            ]);
            return $pdf->render();
            /*
            return $this->render('orders-pdf',[
                'order'=>$order
            ]);*/
        }
    }
    public function actionSignup()
    {
        $model = new SignupForm();
        return $this->renderPartial('signup', [
            'model' => $model,
        ]);
    }

    public function actionSubmitsignup()
    {

        $model = new SignupForm();
        $model->load(Yii::$app->request->post());

        if($user = $model->signup()){
            if(Yii::$app->getUser()->login($user)){
                return json_encode(array('flag' => true, 'username' => Yii::$app->user->identity->name));
            }
        }
        else
        {
            return $this->renderPartial('signup', [            'model' => $model,            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        unset($_SESSION['basket-session-id']);
        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionFormSubmission()
    {
        $security = new Security();
        $string = Yii::$app->request->post('string');
        $stringHash = '';
        if (!is_null($string)) {
            $stringHash = $security->generatePasswordHash($string);
        }
        return $this->render('index', [
            'stringHash' => $stringHash,
        ]);
    }

    public function actionSetRoles(){
        $userIds = UserRoles::find()->where(['status' => 1])->select('user_id')->column();
        Zloradnij::print_arr($userIds);
//        $users = User::find()->where(['IN','id',$userIds])->all();
//
//        if(!$users){
//
//        }else{
//            $auth = Yii::$app->authManager;
//            $ownerRole = $auth->getRole('shopOwner');
//            foreach ($users as $user) {
//                $roles = $auth->getRolesByUser($user->id);
//                if(!empty($roles)){
//
//                }else{
//                    $auth->assign($ownerRole, $user->id);
//                }
//            }
//        }
    }

    public function actionProductList(){

        //print_r(Yii::$app->request->post());die();

        if(Yii::$app->request->isPost){
            $usersToAdd = Yii::$app->request->post('users');
            $listOfLists = Yii::$app->request->post('list');
            foreach ($usersToAdd as $userToAdd){
                $trener = User::find()->where(['id'=>$userToAdd, 'status'=>1])->one();
                if(!empty($trener) && !empty($listOfLists)){
                    //перебираем списки и добавляем к пользователю
                    foreach ($listOfLists as $listOfList){
                        $listDefault = $listDef = Lists::find()->where(['id'=>$listOfList])->one();
                        if(!empty($listDefault)){
                            $listAdd = new Lists();
                            $listAdd->title = $listDefault->title;
                            $listAdd->private = 1;
                            $listAdd->status = 1;
                            $listAdd->date_create = date('Y-m-d H:i:s');
                            $listAdd->date_update = date('Y-m-d H:i:s');
                            $listAdd->list_type = 1;
                            $listAdd->user_id = $trener->id;
                            //print_r($listAdd);
                            if($listAdd->save(true)){
                                //Добавление списка продуктов
                                $productsOfList = $listDefault->listsGoods;

                                foreach ($productsOfList as $listProduct) {
                                    $product = new ListsGoods();
                                    $product->list_id = $listAdd->id;

                                    $product->good_id = $listProduct->good_id;
                                    $product->amount = $listProduct->amount;
                                    $product->date_create = date('Y-m-d H:i:s');
                                    $product->variation_id = $listProduct->variation_id;
                                    $product->sort = $listProduct->sort;
                                    $product->status = 1;
                                    $product->save(true);

                                }
                            }
                        }
                    }
                }
            }
        }

        $lists = Lists::findAll(['private' => 0,'status' => 1]);
        if(Yii::$app->user->identity){
            $lists = Lists::findAll(['user_id' => Yii::$app->user->identity->id,'private' => 1,'status' => 1]);
        }

        $users = User::find()->where(['status'=>1, 'typeof'=>[2,3], ])->asArray()->all();
        // Поиск тренер;
        /*$phone = Yii::$app->request->post('phone');
        if(is_numeric($phone)) {
            $phone = '+7'.substr($phone, -10);
            $searchUser = User::find()->where(['status'=>1, 'typeof'=>[2,3], ])->andWhere(['like', 'phone', $phone])->all();

            //print_r($searchUser);die();
            //$code = PromoCode::findOne(['status'=>1,'user_id'=>$searchUser->id]);
             // Ответ данные JSON-формат;
             //$response = Yii::$app->response;
             //$response->format = \yii\web\Response::FORMAT_JSON;
             //return $response->data = ['id' => $searchUser->id,'name'=>$searchUser->name,'code'=>$code->code];
        }*/

        return $this->render('product-list',[
            'lists' => $lists,
            'users' =>$users,
        ]);
    }

    // Список желание;
    public function actionWishList(){
        $user_id = Yii::$app->user->id;
//        // Lists id;
//        $id = 10001192;
//        // Загрузка скписок;
//        $list = Lists::findOne($id);
//        if(empty(\Yii::$app->session['catalog']['product-list'][$id])){
//            $list->removeOldProducts();
//            $list->setSessionList();
//        }
//        $products = $list->getSessionList();
        $products = WishlistProducts::find()->where(['user_id'=>$user_id,'status'=>1])->All();
        return $this->render('wish-list',[
            //'model'=>$list,
            'products'=>$products,
        ]);
    }
    public function actionTestOrder(){
        $o = Orders::findOne(10051900);
        print $o->money;
        print '<br />';
        print $o->deliveryPrice;



        if(!empty($_POST['OrderId']) && !empty($_POST['TransactionID']) && !empty($_POST['Amount']) && empty($_POST['ErrorCode'])) {
            $emptyTransaction = \app\modules\common\models\UsersPays::find()->where(['id' => intval($_POST['OrderId']), 'status' => 0]);

            $transactionArray = $emptyTransaction->asArray()->one();

            $order = \app\modules\shop\models\Orders::findOne($transactionArray['order_id']);

            $payment = new \app\modules\coders\models\Payment([
                'orderReport' => $order ? $order->id : false,
                'orderId' => $transactionArray['id'],
                'amount' => $_POST['Amount'],
                'orderDescription' => $_POST['OrderDescription'],
            ]);

            // Проверяем ключ
            if ($payment->checkSecurityKey($_POST['SecurityKey'],$_POST)) {
                // Устанавливаем юзера платежа
                $user = \app\modules\common\models\User::findOne($transactionArray['user_id']);
                $payment->setUser($user);

                if($payment->checkEmptyAccount($transactionArray['id'],$_POST['TransactionID'])){

                }

                if (!$order) {

                } else {
                    $payment->setOrder();
                    $payment->orderPayment();
                }

                if (!empty($_POST['RebillAnchor'])) {
                    //  Сохраняем карту
                    $card = \app\modules\common\models\UsersCards::find()
                        ->where([
                            'user_id' => $user->id,
                            'card_number' => $_POST['CardNumber'],
                            'status' => 1,
                        ])
                        ->one();
                    if (!$card) {
                        $card = new \app\modules\common\models\UsersCards();
                        $card->user_id = $user->id;
                        $card->card_number = $_POST['CardNumber'];
                        $card->rebill_anchor = $_POST['RebillAnchor'];
                        $card->date = date('Y-m-d H:i:s');
                        $card->status = 1;
                        if($card->save()){

                        }
                    }
                }
            }
        }
    }

    public function actionSberresponse(){
        $file = "----------------------------------------------------\n------------------------START-----------------------\n";
        $fileName =  'sberbank_'.time().'_'.rand(0, 1000).'.txt';
        $file.=  time(). '--'.Date('Y.m.d H:i:s'."\n", time()). "\n";
        $file.= var_export(Yii::$app->request->post(), true);
        $file = "----------------------------------------------------\n-------------------------GET------------------------\n";
        $file.= var_export(Yii::$app->request->get(), true);

        $dirName =$_SERVER['DOCUMENT_ROOT'] . '/logs/api/'.Date('Y-m-d', time());
        if(!file_exists($dirName)){
            mkdir($dirName);
        }
        file_put_contents($dirName.'/'.$fileName, $file."\n");
        $redirect='';
        $orderId = Yii::$app->request->get('orderId');
        if(!empty($orderId)){
            //определяем статус транзакции
            $payment = new SberbankPayment();
            $transactionStatus = $payment->getTransactionStatus($orderId, true);
            if(!empty($transactionStatus)){
                if(isset($transactionStatus['OrderNumber'])){
                    $order = \app\modules\shop\models\Orders::find()->where(['id'=>$transactionStatus['OrderNumber']])->one();
                }
            }
            /*
            //позьзователя ищем по транзакции и устанавливаем его
            //заказа берем из транзакции
            $transReg = UsersPays::find()->where(['transaction_id'=>$orderId, 'status'=>0])->one();
            if(!empty($transReg)){
                $user = User::findOne($transReg->user_id);//установили пользователя платежа
                $order = \app\modules\shop\models\Orders::find()->where(['id'=>$transReg->order_id])->one();
                if(!empty($order)){
                    $basket = Basket::find()->where(['id' => $order->basket_id])->one();
                    $basket->start();
                    $basket = \Yii::$app->action->applyActionOnFindBasket($basket);
                    if(!empty($basket)){
                        $payment = new SberbankPayment();
                        $transactionStatus = $payment->getTransactionStatus($orderId, true);
                        if(!empty($transactionStatus)){
                            if($transactionStatus['OrderNumber'] == $order->id){
                                $payment->setUser($user);
                                $payment->setOrder($order);
                                $payment->setBasket($basket);
                                $payment->fillAccountUpdate($orderId);//зачисление на счет пользователя

                                $payment->orderPayment();
                                //допилить
                                //зачисления по акциям
                                //$cashBaks = \Yii::$app->action->cashBackReturn($basket);
                                $cashBaks = \Yii::$app->action->cashBackValues();
                                if(!empty($cashBaks)){
                                    if($cashBaks['money']>0){
                                        $payment->fillAccount(32, $cashBaks['money']);
                                    }
                                    if($cashBaks['bonus']>0){
                                        $payment->fillAccountBonus(6, $cashBaks['bonus']);
                                    }
                                }
                                $redirect = '?ORDER='.$order->id;
                            }
                        }
                    }
                }
            }*/
        }
        //if(empty($redirect)){
        if(empty($order)){
            //$this->redirect('/basket/');
            return $this->render('@app/modules/basket/views/default/index', [
                'typeProducts' => GoodsTypes::find()->select(['id','name'])->where(['status' => 1])->indexBy('id')->all(),
            ]);
        }
        else{
            //$this->redirect('/my/orders-history/'.$redirect);
            return $this->render('orders-create-success', [
                'order'=> $order,
            ]);
        }

    }

    public function actionAutoresponse(){
        $file = "----------------------------------------------------\n------------------------START-----------------------\n";
        $fileName =  'auto_sberbank_'.time().'_'.rand(0, 1000).'.txt';
        $file.=  time(). '--'.Date('Y.m.d H:i:s'."\n", time()). "\n";
        $file.= var_export(Yii::$app->request->post(), true);
        $file = "----------------------------------------------------\n-------------------------GET------------------------\n";
        $file.= var_export(Yii::$app->request->get(), true);

        $dirName =$_SERVER['DOCUMENT_ROOT'] . '/logs/api/'.Date('Y-m-d', time());
        if(!file_exists($dirName)){
            mkdir($dirName);
        }
        file_put_contents($dirName.'/'.$fileName, $file."\n");


        $orderId = Yii::$app->request->get('mdOrder');
        //$operation = Yii::$app->request->get('operation');
        //$statusOperation = Yii::$app->request->get('status');
        //$shopId = Yii::$app->request->get('orderNumber');
        if(!empty($orderId)){
            //проверяем статус транзакции
            $payment = new SberbankPayment();
            $transactionStatus = $payment->getTransactionStatus($orderId, true);
            if(!empty($transactionStatus)){
                //заказ и пользователя берем из транзакции
                $transReg = UsersPays::find()->where(['transaction_id'=>$orderId, 'status'=>0])->one();
                if(!empty($transReg)){
                    $user = User::findOne($transReg->user_id);//установили пользователя платежа
                    $order = \app\modules\shop\models\Orders::find()->where(['id'=>$transReg->order_id])->one();
                    if(!empty($order)){
                        $basket = Basket::find()->where(['id' => $order->basket_id])->one();
                        $basket->start();
                        $basket = \Yii::$app->action->applyActionOnFindBasket($basket);
                        if(!empty($basket)){
                            if($transactionStatus['OrderNumber'] == $order->id){
                                $payment->setUser($user);
                                $payment->setOrder($order);
                                $payment->setBasket($basket);
                                $payment->fillAccountUpdate($orderId);//зачисление на счет пользователя

                                $payment->orderPayment();
                                //допилить
                                //зачисления по акциям
                                //$cashBaks = \Yii::$app->action->cashBackReturn($basket);
                                $cashBaks = \Yii::$app->action->cashBackValues();
                                if(!empty($cashBaks) && !\Yii::$app->action->getPromoFlag()){
//                                    if(\Yii::$app->action->getPromoFlag()){
//                                        $order = Orders::find()->where(['id'=>$order->id])->One();
//                                        $code = Codes::find()->where(['id'=>$order->code_id])->One();
//                                        $ownerPromo = User::find()->where(['id'=>$code->user_id])->One();
//                                        $payment->setUser($ownerPromo);
//                                    }
                                    if($cashBaks['money']>0){
                                        $payment->fillAccount(32, $cashBaks['money']);
                                    }
                                    if($cashBaks['bonus']>0){
                                        $payment->fillAccountBonus(6, $cashBaks['bonus']);
                                    }
//                                    if(\Yii::$app->action->getPromoFlag()){
//                                        $payment->setUser($user);
//                                    }
                                }
                                $redirect = '?ORDER='.$order->id;
                            }
                        }
                    }
                }
            }

        }

        //петя
        /*if (isset($_REQUEST['orderNumber']) and isset($_REQUEST['status']) and isset($_REQUEST['checksum'])) {
  	// Логирование;
	debug($_REQUEST);
	// Номер платежа;
	$pay_id = intval($_REQUEST['orderNumber']);
	// Загрузка платежа;
    $sql = "SELECT * FROM `clients_pays` WHERE `id` = '".$pay_id."' AND `status` = '0' LIMIT 1";
    if ($pay = $db->row($sql)) {
  		// Логирование;
		debug($pay);
		// Обработка мерчанта;
	    if ($merchant = $api->pay_merchant($pay['merchant_id'])) {
	  		// Логирование;
			debug($merchant);
			// Расчет контрольной суммы;
			$data = 'mdOrder;'.$_REQUEST['mdOrder'].';operation;'.$_REQUEST['operation'].';orderNumber;'.$_REQUEST['orderNumber'].';status;'.$_REQUEST['status'].';';
	  		$crc = hash_hmac('sha256', $data, $merchant['token']);
			$crc = strtoupper($crc);
			// Проверка контрольной суммы;
			if ($_REQUEST['checksum'] == $crc) {
				// Реквизиты платежа;
				$_pay_id = $_REQUEST['orderNumber'];
				$_transaction_id = $_REQUEST['mdOrder'];
				$_status = ($_REQUEST['operation'] == 'deposited' and $_REQUEST['status'] == 1) ? 1 : 0;
			}
		}
	}
}
*/
    }

}

