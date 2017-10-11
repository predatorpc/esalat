<?php

namespace app\controllers;
use Yii;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsVariations;
use app\modules\common\controllers\FrontController;
use app\modules\common\models\Menu;
use app\modules\common\models\Zloradnij;
use app\modules\pages\models\CallForm;
use app\modules\pages\models\ContactForm;
use app\modules\pages\models\ForgotPasswordForm;
use app\modules\pages\models\LoginForm;
use app\modules\pages\models\RestorePasswordForm;
use app\modules\pages\models\SignupForm;
use app\modules\basket\models\PromoCode;
use app\modules\my\models\Feedback;
use app\modules\my\models\MessagesImages;
use yii\web\UploadedFile;
use yii\base\Security;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Query;
use app\modules\common\models\User;
use yii\data\Pagination;



class SiteController extends FrontController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => [
                                'logout',
                                'actionSaveSecretWord',
                             //   'promoes',
                                'promo',
                                'agree',
                                'showmap',
                                'fitlogin'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [
                            'login-god',

                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager'],//Не работает, ограничения по ролям написаны в action
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
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
//            'captcha' => [
//                'class' => 'yii\captcha\CaptchaAction',
//                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
//            ],
        ];
    }

    public function actionAgree(){

        $user = User::findOne(Yii::$app->user->getId());
        $user->agree = 1;
        $user->save();

        //Zloradnij::print_arr($user->errors);die();

        return $this->redirect('index');

    }

    // Загрузка категория товаров (рекурсия);
    public function TreeMainCategory(&$parents, $parent_id = false) {
        if($_parents = (new Query())
            ->select(['id','title'])
            ->from('category')
            ->where(['parent_id'=>$parent_id, 'active' => 1])
            ->orderby('sort ASC')
            ->column()) {
            foreach ($_parents as $key => $value) {
                $parents[] = $value;
                // Загрузка вложенных групп;
                $subparents[$key]['parents'] = $this->TreeMainCategory($parents, $value);
            }
        }
        return $_parents;
    }

    public function actionShowmap(){
        $this->view->RegisterJs('/js/polygon.js');
        $this->layout = "empty";
        return $this->render('showmap');
    }

    public function actionIndex()
    {
        $nameTemplate = 'index';

        $model = new LoginForm();
		$list = Menu::getStructure(10000006,true);




        /*Загрузка слайдер*/
        $main_slider = (new \yii\db\ActiveRecord())->find()->select('id,url,name')->from('banners')->where(['type'=>3,'status'=>1])->orderby('position ASC')->asarray()->all();

        // Шаблон мобильная версия;
        /*
        if(!empty(Yii::$app->params['mobile'])){
            $nameTemplate = 'mobile-index';
        }*/
      //  $this->layout =  '@app/views/layouts/main_en';






        return $this->render($nameTemplate, [
            'model'=> $model,
            'list' => $list,
            'slider'=>$main_slider,
            'main_category'=>(isset($main_category) && !empty($main_category)  ? $main_category : '')
        ]);

    }

    public function actionPromoes(){
        return $this->render('promoes');
    }

	public function actionLogin()
    {

        $model = new LoginForm();
        return $this->renderAjax('login', [
            'model' => $model,
        ]);
    }

    public function actionLoginFull()
    {
        $model = new LoginForm();
        return $this->render('login-full', [
            'model' => $model,
        ]);
    }

	public function actionSubmitlogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->post());

     
      	if($model->login()){ 
            //save the password
            return json_encode(array('flag' => true, 'username' => Yii::$app->user->identity->name));
        }
        else
        {
            //return to login if authorization is failed
            return $this->renderPartial('login', [
            'model' => $model,
            ]);
        }
    }
    

	public function actionSignup()
    {

        $this->layout = "empty";
//        print_r($this->layout);

        $model = new SignupForm();
//        return $this->render('signup', [
//            'model' => $model,
//        ]);
        return $this->renderAjax('signup', [
            'model' => $model,
        ]);
    }

	public function actionSubmitsignup()
    {
        $this->layout = "empty";

        $model = new SignupForm();
        $model->load(Yii::$app->request->post());
        $model->registration = date("Y-m-d H:i:s");

      	if($user = $model->signup()){ 
				if(Yii::$app->getUser()->login($user)){
				        return json_encode(array('flag' => true, 'username' => Yii::$app->user->identity->name));
				}
        }
        else
        {
            //$model = new SignupForm();
            //$this->layout = "empty";
//            print_r($this->layout);
            return $this->render('signup', [
                    'model' => $model,
                ]);
        }
    }

    public function actionLogout()
    {
        $test = 'werew';
        Yii::$app->session['reloadSessId'] = 'Y';
        Yii::$app->user->logout();

        return $this->goHome();
    }
    // Доставка
    public function actionSity()
    {
        return $this->renderPartial('sity');
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
    // EN - Version;
    /*
    public function actionAbout()
    {
        if(Yii::$app->params['en']) {
            return $this->render('service');
        }else{
            $exception = Yii::$app->errorHandler->exception;
            return $this->render('/site/error',['exception' => $exception, 'name' => '404', 'message' => '404']);
        }
    }*/
    // EN - Version;
    public function actionService()
    {
        if(Yii::$app->params['en']) {
            return $this->render('service');
        }else{
            $exception = Yii::$app->errorHandler->exception;
            return $this->render('/site/error',['exception' => $exception, 'name' => '404', 'message' => '404']);
        }
    }
    // EN - Version;
    public function actionPolicy()
    {
        if(Yii::$app->params['en']) {
            return $this->render('privacy-policy');
        }else{
            $exception = Yii::$app->errorHandler->exception;
            return $this->render('/site/error',['exception' => $exception, 'name' => '404', 'message' => '404']);
        }
    }

    public function actionMap()
    {
        return $this->render('map');
    }

    // Страница отзывы;
    /*
    public function actionFeed()
    {
        // Отрпавить данные;
        $model = new Feedback();
        if(Yii::$app->request->post()) {
            // Обработка потс данные;
            $feedback = Yii::$app->request->post('Feedback');
            $rating = intval($feedback['rating']);
            $order = intval($feedback['order']);
           // $topic = $feedback['topic'];
            $text = $feedback['text'];
            // Обработка полей;
            $model->rating = $rating;
            $model->order = $order;
            //$model->topic = '';
            $model->text = $text;
            $model->type_id = 1003;
            $model->status = 0;
            // Добавляем запись;
            if($model->save()) {
                // Загрузка фотография!;
                $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
                if (!empty($model->imageFiles)) {
                    $model->upload($model->id);
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Ваше сообщение отправлено. Спасибо!'));
                return $this->refresh();
            }else{
                return $model->errors;
            }
        }
        // Погрузка контент;
        $limit = '1';
        if(isset($_POST['limit']) && $_POST['limit'] > 0) {
            $limit = '1';
        }
        // Загрузхка уведомления:
        $notice = Feedback::find()->where(['type_id'=>1003,'status'=>1])->orderby(['id'=>SORT_DESC]);
        // делаем копию выборки
        $countQuery = clone $notice;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;
        $models = $notice->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('feed', [
            'model' => $model,
            'notice'=>(isset($models) ? $models : ''),
            'pages' => $pages,
        ]);
    }*/

    public function actionSaveSecretWord(){

        $userId = Yii::$app->user->getId();
        $model = User::findOne($userId);

        //Zloradnij::print_arr($model);
//        Zloradnij::print_arr(Yii::$app->request->post('secretWord'));
      //  die();

        $model->secret_word = Yii::$app->request->post('secretWord');
        $model->save();

        return $this->redirect('index');
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
    public function actionCall(){
        $model = new CallForm();

        if($model->load(Yii::$app->request->post()) and $model->save(true)) {
             Yii::$app->session->setFlash('success','Спасибо! Мы обязательно Вам перезвоним.');
        }
        // Указываем шаблон;
        return $this->renderPartial('call', [
            'model' => $model,
        ]);
    }
    // Адресс доставки;
    public function actionDeliveryAddress()
    {


        return $this->renderPartial('delivery-address');
    }
    // Страница промокод;
    public function actionPromo()
    //public function actionPromo()
    {

        if(!Yii::$app->user->isGuest) {

            $staff = User::find()->where('id = '.\Yii::$app->user->getId())->one()->staff;
            if(!empty($staff)) {

                //var_dump($_SERVER);die();
                $promo = PromoCode::find()->select('code')->where(['user_id' => Yii::$app->user->id, 'status' => 1])->one();
                if (empty($promo)) {
                    $newCode = new PromoCode();
                    $code = $newCode->generatePromocode();
                    $newCode->code = $code;
                    $newCode->user_id = Yii::$app->user->id;
                    $newCode->count = 10000;
                    $newCode->type_id = 1008;
                    $newCode->status = 1;
                    $newCode->date_begin = date("Y-m-d H:i:s");
                    $oneYearOn = date('Y-m-d', strtotime(date("Y-m-d", time()) . " + 365 day"));
                    $newCode->date_end = $oneYearOn;

                    //Zloradnij::print_arr($newCode);die();

                    if ($newCode->save()) {

                        $promo = PromoCode::find()->select('code')->where(['user_id' => Yii::$app->user->id, 'status' => 1])->one();
                        return $this->render(
                            'promo', [
                                'promo' => $promo,
                            ]
                        );
                    } else {
                        //Zloradnij::print_arr($newCode);die();
                        return $this->render('promo');
                    }
                } else {
                    return $this->render(
                        'promo', [
                            'promo' => $promo,
                        ]
                    );
                }
            }
            else{
                return $this->render('error',['name' => Yii::t('app', 'Ошибка'), 'message' => Yii::t('app','У вас недостаточно привелегий для самостоятельного получения промо-кода!')]);
            }
        }else{
            return $this->render('promo');
        }

    }

    public function actionForgotPassword(){
        $model = new ForgotPasswordForm();
        if($model->load(Yii::$app->request->post())){
            if ($model->validate()){
                $model->setResetPasswordToken();
                return $this->redirect('restore-password');
            }
        }

        return $this->render('forgot-password', [
            'model' => $model,
        ]);
    }

    public function actionRestorePassword(){
        $model = new RestorePasswordForm();

        if($model->load(Yii::$app->request->post())){
            if ($model->validate()){
//                $findUser = User::find()->where(['password_reset_token' => md5($model->code)])->andWhere(['>=','updated_at',(time() - 60*7)])->one();
        $findUser = User::find()->where(['password_reset_token' => $model->code])->andWhere(['>=','updated_at',(time() - 60*7)])->one();

                if(!$findUser){

                }else{
                    if($model->updatePassword($findUser,$model->password)){
                        return $this->render('restore-password', [
                            'model' => $model,
                            'response' => 'OK',
                        ]);

                        //return $this->redirect('/site/index');
                    }else{

                    }
                }

            }else{

            }
        }else{

        }

        return $this->render('restore-password', [
            'model' => $model,
            'response' => 'empty',
        ]);
    }

    public function actionQuicklyview($id=null){
        if(intval($id)){
            $variant = GoodsVariations::find()->where(['id'=>$id])->one();
            if(!empty($variant)){
                $product = Goods::find()->where(['id'=>$variant['good_id']])->one();
                if(!empty($product)){
                    $this->layout = '@app/views/layouts/empty.php';
                    return $this->render('quicklyview',[
                        'product'=>$product,
                        'variant'=>$variant,
                    ]);
                }
            }
        }
    }

    public function actionLoginGod()
    {
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        if(isset($role['GodMode']) || isset($role['categoryManager']) || isset($role['callcenterOperator'])){
            $model = new LoginForm();
            if(Yii::$app->request->post()){
                $model->load(Yii::$app->request->post());
                $model->loginGod();
            }
            return $this->render('login-god', [
                'model' => $model,
            ]);
        }
        return $this->redirect('/');
    }

    public function actionFitlogin($login, $key){
        //авторизация по aithKey
        if(!empty($login) && !empty($key) && is_numeric($login)){

            $user = User::find()->where(['extremefitness'=>$login, 'auth_key'=>$key])->one();
            if(!empty($user)){
                $_SESSION['basket-session-id'] = Yii::$app->session->id;
                Yii::$app->user->login($user, 3600);
                return $this->redirect('/basket');
            }
        }
        return $this->redirect('http://extremefitness.ru/my');
    }

}

