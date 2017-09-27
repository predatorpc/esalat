<?php

namespace app\controllers;

use app\modules\common\controllers\BackendController;
use app\modules\common\models\UserAdmin;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\ShopsCallback;
use Yii;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\common\models\UserShop;
use app\modules\common\models\User;
use app\modules\common\models\UserRoles;
use app\modules\managment\models\ShopsStores;
use app\modules\managment\models\ShopsStoresTimes;
use app\modules\common\models\Address;
use yii\filters\AccessControl;


/**
 * ShopsController implements the CRUD actions for Shops model.
 */
class ShopsController extends BackendController
{
    //public $layout = 'shops-owner';
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
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
                            'storeupdate',
                            'storeadd',
                            'useradd',
                            'userupdate',
                            'getusername',
                            'addcallback',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager', 'conflictManager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    protected function findModel($id)
    {
        if (($model = Shops::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /////////////////////////////////////////////////////////////
//  Shops commander begin
////////////////////////////////////////////////////////////

    public function actionUserupdate()
    {
        $id = $_POST['role_id'];
        $modelId = $_POST['shop_id'];

        //   var_dump($id);die();
        $userId = UserShop::getIdentityUser();
        $model = UserRoles::find()->where(['id' => $id])->one();

        if($model->load(Yii::$app->request->post())){
            if($model->save()) {
                $model = $this->findModel($modelId);
                return $this->redirect(
                    ['update',
                     'id'    => $model->id,
                     'model' =>$model,
                     'error' => 'Успешное сохранение',
                    ]
                );
            }
            else
            {
                $this->findModel($modelId);
                return $this->redirect(['update',
                                        'id'=>$model->id,
                                        'model' => $model,
                                        'error' => 'Ошибка сохранения',
                ]);
            }
        }

        $this->findModel($modelId);
        return $this->redirect(['update',
                                'id'=>$model->id,
                                'error' => 'Ошибка',
                                'model' => $model,
                                'username' => 'Глобальная ошибка',
        ]);
    }

    public function actionUseradd($id, $shop_id)
    {
        // var_dump($_POST);die();
        //$id = $_POST['UserRoles']['shop_id'];

        $model = new UserRoles;
        $model->user_id = $id;
        $model->shop_id = $shop_id;
        $model->status = 1;

        if($model->save()){
/*
            $userId = UserShop::getIdentityUser();
            $auth = Yii::$app->authManager;

            $modelUser = User::find()->where('id = '.$id)->one();
            //it brake role assignment
//          $role = $auth->getRole('shopOwner');
//          $auth->revokeAll($modelUser->id);
//          $auth->assign($role, $modelUser->id);

            //Zloradnij::print_arr($auth);
            //Zloradnij::print_arr($modelUser);die();
            // print var_export($model,true);die();

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    $model = $this->findModel($shop_id);
                    return $this->redirect(['update',
                        'id' => $model->id,
                        'model' => $model,
                    //  'error' => '',
                    ]);
                }
            }

*/
        $model = Shops::find()->where('id = '.$shop_id)->one();
        
	    return $this->redirect(['update',
		'id' => $shop_id,
		'model' => $model,
		]);

        }
        else
        {
            $error = 'Ошибка добавления пользователя';

            return $this->redirect(['update',
                                    'id' => $model->id,
                                    'model' => $model,
                                    'error' => $error,
            ]);
        }

    }

    public function actionIndex()
    {

        $searchModel = new ShopsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $userId = UserShop::getIdentityUser();

        Yii::info(date("Y-m-d H:i:s").' UserID: '.$userId.' Action: SEARCH','Shops');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userId' => $userId,
        ]);
    }

    public function actionStoreadd()
    {
        $id = $_POST['ShopsStores']['shop_id'];

        // var_dump($_POST);die();

        $model= new ShopsStores();

        $modelAddress= new Address();
        $modelAddress->date = date("Y-m-d H:i:s");


        $userId = UserShop::getIdentityUser();


        if($modelAddress->load(Yii::$app->request->post()) && $modelAddress->save())
        {

            $model->address = $modelAddress->street." ".$modelAddress->house." ".$modelAddress->room;
            $model->address_id = $modelAddress->id;

            if($model->load(Yii::$app->request->post()))
            {
                if($model->save())
                {
                    $model = $this->findModel($id);
                    return $this->redirect(['/shops/update',
                                            'id' => $model->id,
                                            'model' => $model,
                                            'error' => '',
                    ]);

                }
                else{
                    $error = 'Ошибка добавления склада 2';
                    return $this->redirect(['/shops/update',
                        'id' => $model->id,
                        'model' => $model,
                        'error' => $error,
                    ]);

                }

            }
            else
            {
                $error = 'Ошибка добавления склада 1';
                return $this->redirect(['/shops/update',
                    'id' => $model->id,
                    'model' => $model,
                    'error' => $error,
                ]);
            }

        }
        else{
            $error = 'Ошибка добавления склада 0';

      //      Zloradnij::print_arr($modelAddress);
            Zloradnij::print_arr($modelAddress->errors);die();

            return $this->redirect(['/shops/update',
                'id' => $model->id,
                'model' => $model,
                'error' => $error,
            ]);

        }

    }

    public function actionGetusername($phone ='')
    {
        $phone = Yii::$app->request->post('string');
        $id = Yii::$app->request->post('shop_id');
        $model = $this->findModel($id);

        echo $phone;

        if(!empty($phone)){
            $query = new \yii\db\Query;
            $users = $query
                ->select('id,name,phone')
                ->from('users')
                ->where(['status' => 1])
//			->andWhere(['phone' => $phone])
                ->andFilterWhere(['like','phone',$phone])
                //   ->one();
                ->all();

//			return $this->render('index',['phone' => 'empty']);
            return $this->render('update',[
                'model' => $model,
                'stringHash' => $users, //['name']

            ]);
        }else{
            return $this->render('/shops/update',[ 'model' => $model, 'stringHash' => 'empty']);
        }

//        return $this->render('update', ['username' => date('H:i:s')]);

    }

    public function actionStoreupdate()
    {
        $userId = UserShop::getIdentityUser();
        $id = $_POST['shop_id'];
        $modelId = $_POST['id'];
        $modelStore = ShopsStores::find()->where('shop_id = '.$id.' and id ='.$modelId.' and (status <> -1)')->one();

        if(!empty($_POST['ShopsStores'])){
            foreach ($_POST['ShopsStores'] as $model) {
                $modelStore->id = $model['id'];
                $modelStore->address = $model['address'];
                $modelStore->phone = $model['phone'];
                $modelStore->status = $model['status'];

                if ($modelStore->save()) {
                    return $this->redirect(
                        ['/shops/update',
                         'id'    => $id,
                         'model' => $model = $this->findModel($id),
                         'error' => 'Успешное сохранение',
                        ]
                    );
                } else {
                    return $this->redirect(
                        ['/shops/update',
                         'id'    => $id,
                         'model' => $this->findModel($id),
                         'error' => 'Ошибка сохранения',
                        ]
                    );
                }
            }
        }

        return $this->redirect(['/shops/update',
                                'id'=>$id,
                                'error' => 'Глобальная ошибка',
                                'model' => $this->findModel($id),
        ]);
    }

    public function actionStoredelete($id, $shop_id)
    {
        $model = ShopsStores::find()->where(['id' => $id])->one();
        $model->status=-1;
        $model->save();

        return $this->redirect(
            ['/shops/update',
             'id'    => $shop_id,
             'model' => $model = $this->findModel($shop_id),
             'error' => '',
            ]
        );
    }

    public function actionView($id)
    {
        $userId = UserShop::getIdentityUser();
        $model = $this->findModel($id);
        return $this->render('/shops/view', [
            'model' => $model,
            'userId' => $userId,
        ]);
    }

    public function actionCreate()
    {
        $model = new Shops();
        $userId = UserShop::getIdentityUser();


        if ($model->load(Yii::$app->request->post())) {

            $model->registration = date("Y-m-d H:i:s");

            if($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('/shops/create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $userId = UserShop::getIdentityUser();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // Отключение/включение показа на сайте
            if ($model->show == 1) {
                $shop = Shops::findOne($model->id)->disableShopProducts(1);
            } elseif ($model->show == 0) {
                $shop = Shops::findOne($model->id)->disableShopProducts(0);
            }

            return $this->redirect(['view', 'id' => $model->id, 'stringHash' => '',]);
        } else {
            return $this->render('/shops/update', [
                'model' => $model,
                'error' => '',
                'stringHash' => '',
            ]);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = -1;
        $model->save();

        return $this->redirect(['index']);
    }

    public function actionAddcallback()
    {
        $request = Yii::$app->request->post();
        $model = new ShopsCallback();
        $model->user_id = Yii::$app->user->id;;
        $model->shop_id = $request['shop_id'];
        $model->date = date("Y-m-d h:i:s");
        $model->comment = $request['comment'];
        $model->contact = $request['contact'];
        $model->phone = $request['phone'];
        $model->action = $request['action'];
        $model->status = 1;
        $shop_id = $request['shop_id'];
        if($model->save()){
            $model = $this->findModel($shop_id);
            return $this->redirect(['update',
                'id' => $model->id,
                'model' => $model,
                //'error' => '',
            ]);

        }

    }

/////////////////////////////////////////////////////////////
//  Shops commander end
////////////////////////////////////////////////////////////


//    /**
//     * Lists all Shops models.
//     * @return mixed
//     */
//    public function actionIndex()
//    {
//        $searchModel = new ShopsSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }
//
//    /**
//     * Displays a single Shops model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }
//
//    /**
//     * Creates a new Shops model.
//     * If creation is successful, the browser will be redirected to the 'view' page.
//     * @return mixed
//     */
//    public function actionCreate()
//    {
//        $model = new Shops();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    /**
//     * Updates an existing Shops model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    /**
//     * Deletes an existing Shops model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }
//
//    /**
//     * Finds the Shops model based on its primary key value.
//     * If the model is not found, a 404 HTTP exception will be thrown.
//     * @param integer $id
//     * @return Shops the loaded model
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    protected function findModel($id)
//    {
//        if (($model = Shops::findOne($id)) !== null) {
//            return $model;
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }
}


