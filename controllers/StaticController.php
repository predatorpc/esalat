<?php

namespace app\controllers;

use app\modules\basket\models\BasketOne;
use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\common\controllers\BackendController;
use app\modules\common\controllers\FrontController;
use app\modules\pages\models\Pages;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class StaticController extends FrontController
{
    public $catalogMenu;
    public $catalogHash;
    public $basket;

    public function init() {
        $this->catalogHash = Category::find()->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
        $urls = [];
        $this->catalogMenu = Catalog::buildTree($this->catalogHash,$urls);
//        $this->basket = BasketOne::initBasket();

        parent::init();
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionPage($p)
    {
        $pages = Pages::getPageRow($p);
        if(!empty($pages)){
            return $this->render($p, [ 'pages' => $pages,]);
        }else {
            $exception = Yii::$app->errorHandler->exception;
            return $this->render('/site/error',['exception' => $exception, 'name' => '404', 'message' => '404']);
        }
    }

}