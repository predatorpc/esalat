<?php

namespace app\controllers;

use app\modules\common\controllers\BackendController;
use app\modules\common\controllers\FrontController;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\coders\models\Logs;
use app\modules\coders\models\LogsSearch;

use app\modules\common\models\UserShop;

/**
 * LogsController implements the CRUD actions for Logs model.
 */
class LogsController extends BackendController
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'view',
                            'update',
                            'create',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Logs models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LogsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $catMgr = false; $seoMgr = false;
        $userId = UserShop::getIdentityUser();


        $userLevel = UserShop::find()
            ->where(['id' => $userId])->one();

        //var_dump($userLevel);die();

        //проверяем категорийный ли это менеджер или нет
        if($userLevel['level']==3 || $userLevel['level']==9){
            $catMgr = true;
        }
        if($userLevel['level']==7){
            $seoMgr = true;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userLevel' => $userLevel['level'],
            'userId' => $userId,

        ]);
    }

    /**
     * Displays a single Logs model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $catMgr = false; $seoMgr = false;
        $userId = UserShop::getIdentityUser();

        $userLevel = UserShop::find()
            ->where(['id' => $userId])->one();

        //проверяем категорийный ли это менеджер или нет
        if($userLevel['level']==3 || $userLevel['level']==9){
            $catMgr = true;
        }
        if($userLevel['level']==7){
            $seoMgr = true;
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'userLevel' => $userLevel['level'],
            'userId' => $userId,

        ]);
    }

    /**
     * Creates a new Logs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $catMgr = false; $seoMgr = false;
        $userId = UserShop::getIdentityUser();

        $userLevel = UserShop::find()
            ->where(['id' => $userId])->one();

        //проверяем категорийный ли это менеджер или нет
        if($userLevel['level']==3 || $userLevel['level']==9){
            $catMgr = true;
        }
        if($userLevel['level']==7){
            $seoMgr = true;
        }

        $model = new Logs();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'userIs' => $userId,
            ]);
        }
    }

    /**
     * Updates an existing Logs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $catMgr = false; $seoMgr = false;
        $userId = UserShop::getIdentityUser();

        $userLevel = UserShop::find()
            ->where(['id' => $userId])->one();

        //проверяем категорийный ли это менеджер или нет
        if($userLevel['level']==3 || $userLevel['level']==9){
            $catMgr = true;
        }
        if($userLevel['level']==7){
            $seoMgr = true;
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'userLevel' => $userLevel['level'],
                'userId' => $userId,
            ]);
        }
    }

    /**
     * Deletes an existing Logs model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Logs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Logs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Logs::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}