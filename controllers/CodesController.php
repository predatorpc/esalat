<?php

namespace app\controllers;

use app\modules\basket\models\PromoCodeType;
use app\modules\catalog\models\CodesSearchIndex;
use app\modules\catalog\models\CodesTypesSearch;
use app\modules\catalog\models\TagsGroups;
use app\modules\common\controllers\BackendController;
use Yii;
use app\modules\catalog\models\Codes;
use app\modules\catalog\models\CodesSearch;
use app\modules\catalog\models\CodesTypes;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsSearch;
use app\modules\common\models\User;

use app\modules\catalog\models\Goods;
use yii\bootstrap\Modal;

class CodesController extends BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'create',
                            'update',
                            'view',
                            'index',
                            'getusername',
                            'add-type-promo',
                            'order',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager', 'HR', 'conflictManager'],
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

    public function actionGetusername($phone ='')
    {
        $types = CodesTypes::find()->all();
        $phone = Yii::$app->request->post('string');
        $id = Yii::$app->request->post('user_id');
        $model = new Codes();
        //$model = $this->findModel($id);
        echo $phone;
        $query = new \yii\db\Query;
        $users = $query
            ->select('id,name,phone')
            ->from('users')
            ->where(['status' => 1])
//			->andWhere(['phone' => $phone])
            ->andFilterWhere(['like','phone',$phone])
            //   ->one();
            //->limit(1)
            ->all();
        // Zloradnij::print_arr($users);die();
        if(!empty($phone)){
//			return $this->render('index',['phone' => 'empty']);
            return $this->render('/codes/create',[
                'id' => $id,
                'model' => $model,
                'types' => $types,
                'stringHash' => $users, //['name']
                'users' => $users,
            ]);
        }else{
            //    $users = [];
            return $this->render('/codes/create',[
                'id' => $id,
                'model' => $model,
                'types' => $types,
                'stringHash' => $users, //['name']
                'users' => $users,
            ]);
//            return $this->render('/shops/update',[ 'model' => $model, 'stringHash' => 'empty']);
        }
//        return $this->render('update', ['username' => date('H:i:s')]);
    }


    /**
     * Lists all Tags models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CodesSearchIndex();
        $dataProvider = $searchModel->searchCodes(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tags model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Tags model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
{
    $model = new Codes();
    $types = CodesTypes::find()->all();

    $users = User::find()
        ->select([
            'id',
            'CONCAT(name, ", ", phone) AS name',
        ])->where(['status' => 1])
        ->orderBy('name ASC')->all();

    if ($model->load(Yii::$app->request->post())) {

        $model->date_begin = date("Y-m-d H:i:s",strtotime("now"));
        $model->date_end = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + 365 day"));

        if($model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

    } else {
        return $this->render('create', [
            'id' => $id,
            'model' => $model,
            'types' => $types,
            'users' => $users,
        ]);
    }
    return $this->render('create', [
        'id' => $id,
        'model' => $model,
        'types' => $types,
        'users' => $users,
    ]);

}

    /**
     * Updates an existing Tags model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModelCodes($id);
        $types = CodesTypes::find()->all();
        $users = User::find()
            ->select([
                'id',
                'CONCAT(name, ", ", phone) AS name',
            ])->where(['status' => 1])
            ->orderBy('name ASC')->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->date_begin = date("Y-m-d H:i:s",strtotime("now"));
            $model->date_end = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + 365 day"));


            if($model->save()){
//                var_dump($model);die();

                return $this->redirect(['view', 'id' => $model->id]);
            }
            else{
                var_dump($model->errors);die();
            }
        } else {
            return $this->render('update', [
                'id' => $id,
                'model' => $model,
                'types' => $types,
                'users' => $users,
            ]);
        }
    }

    /**
     * Deletes an existing Tags model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = -1;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tags model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tags the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Codes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelCodes($id)
    {
        if (($model = Codes::find()->where('id = '.$id)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddTypePromo()
    {
        $model = new PromoCodeType();
        if ($model->load(Yii::$app->request->post())) {

            if($model->save())
            {
                return $this->redirect(['/codes/index']);
            }

        } else {
            return $this->render('newtype', [
                'model' => $model,
            ]);
        }
    }

    public function actionOrder(){


        $searchModel = new CodesTypesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('order', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


}
