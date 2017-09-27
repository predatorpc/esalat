<?php

namespace app\controllers;

use app\modules\common\controllers\BackendController;
use app\modules\common\models\User;
use app\modules\common\models\UsersBonus;
use app\modules\common\models\UsersBonusSearch;
use app\modules\common\models\UsersPays;
use app\modules\common\models\UsersPaysSearch;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;


/**
 * TransactionsController implements the CRUD actions for UsersPays model.
 */
class TransactionsController extends BackendController
{
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
                            'create-bonus',
                            'create-new',
                            'update',
                            'view',
                            'index',
                            'bonus',
                            'getusername',
                            'getusernamebonus',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'conflictManager' , 'callcenterOperator'],
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

    public function actionGetusernamebonus($phone ='')
    {
        $phone = Yii::$app->request->post('string');
        $id = Yii::$app->request->post('user_id');

        $model = new UsersPays();
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
            return $this->render('/transactions/create-bonus',[
                'id' => $id,
                'model' => $model,
                'stringHash' => $users, //['name']
                'users' => $users,
            ]);
        }else{

            //    $users = [];

            return $this->render('/transactions/create-bonus',[
                'id' => $id,
                'model' => $model,
                'stringHash' => $users, //['name']
                'users' => $users,
            ]);
//            return $this->render('/shops/update',[ 'model' => $model, 'stringHash' => 'empty']);
        }

//        return $this->render('update', ['username' => date('H:i:s')]);

    }


    public function actionGetusername($phone ='')
    {
        $phone = Yii::$app->request->post('string');
        $id = Yii::$app->request->post('user_id');
        $model = new UsersPays();
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
            return $this->render('/transactions/create-new',[
                'id' => $id,
                'model' => $model,
                'stringHash' => $users, //['name']
                'users' => $users,
            ]);
        }else{

            //    $users = [];

            return $this->render('/transactions/create-new',[
                'id' => $id,
                'model' => $model,
                'stringHash' => $users, //['name']
                'users' => $users,
            ]);
//            return $this->render('/shops/update',[ 'model' => $model, 'stringHash' => 'empty']);
        }
//        return $this->render('update', ['username' => date('H:i:s')]);
    }


    /**
     * Lists all UsersPays models.
     * @return mixed
     */
    public function actionIndex()
    {
        $userFlag = false;

        $searchModel = new UsersPaysSearch();
        $dataProvider = $searchModel->searchWithUsers(Yii::$app->request->get());

        $getParams =Yii::$app->request->get();
        $sumIN = 0; $sumOUT = 0; $sumDelivery= 0;
        $modelUser = new User();

        if(!empty($getParams['UsersPaysSearch']['user_id'])){
        //            !empty($getParams['UsersPaysSearch']['userPhone'])) {
            $userFlag = true;
            $modelUser = $modelUser::find()
                ->where('name like \'%'.$getParams['UsersPaysSearch']['user_id'].'%\'')
            //    ->orwhere('phone like \'%'.$getParams['UsersPaysSearch']['userPhone'].'%\'')
                ->all();

            foreach($modelUser as $i => $item) {

                $modeUserPaysIN = UsersPays::find()
                    ->where('user_id = '.$item['id'])
                    ->andWhere('type = 1')
                    ->all();

                foreach($modeUserPaysIN as $value)
                {
                    $sumIN +=intval($value['money']);
                }

                $modeUserPaysOUT = UsersPays::find()
                    ->where('user_id = '.$item['id'])
                    ->andWhere('type = 4')
                    ->all();

                foreach($modeUserPaysOUT as $value)
                {
                    $sumOUT +=intval($value['money']);
                }

                $modeUserPaysDelivery = UsersPays::find()
                    ->where('user_id = '.$item['id'])
                    ->andWhere('type = 9')
                    ->all();

                foreach($modeUserPaysDelivery as $value)
                {
                    $sumDelivery +=intval($value['money']);
                }

                $item->moneyCount=$sumIN;
                $item->moneySpend=$sumOUT;
                $item->moneyDelivery=$sumDelivery;
            }

         //   var_dump($modelUser);die();

        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'userFlag' => $userFlag,
            'modelUser' => $modelUser,
        ]);
    }



    public function actionBonus()
    {
        $userFlag = false;

        $searchModel = new UsersBonusSearch();
        $dataProvider = $searchModel->searchWithUsers(Yii::$app->request->get());

        $getParams =Yii::$app->request->get();
        $sumIN = 0; $sumOUT = 0; $sumDelivery= 0;
        $modelUser = new User();

        if(!empty($getParams['UsersBonusSearch']['users'])){
            //            !empty($getParams['UsersPaysSearch']['userPhone'])) {
            $userFlag = true;

            $modelUser = $modelUser::find()
                ->where('name like \'%'.$getParams['UsersBonusSearch']['users'].'%\'')
                //    ->orwhere('phone like \'%'.$getParams['UsersPaysSearch']['userPhone'].'%\'')
                ->all();

            foreach($modelUser as $i => $item) {

                $modeUserPaysIN = UsersPays::find()
                    ->where('user_id = '.$item['id'])
                    ->andWhere('type = 1')
                    ->all();

                foreach($modeUserPaysIN as $value)
                {
                    $sumIN +=intval($value['money']);
                }

                $modeUserPaysOUT = UsersPays::find()
                    ->where('user_id = '.$item['id'])
                    ->andWhere('type = 4')
                    ->all();

                foreach($modeUserPaysOUT as $value)
                {
                    $sumOUT +=intval($value['money']);
                }

                $modeUserPaysDelivery = UsersPays::find()
                    ->where('user_id = '.$item['id'])
                    ->andWhere('type = 9')
                    ->all();

                foreach($modeUserPaysDelivery as $value)
                {
                    $sumDelivery +=intval($value['money']);
                }

                $item->moneyCount=$sumIN;
                $item->moneySpend=$sumOUT;
                $item->moneyDelivery=$sumDelivery;
            }

            //   var_dump($modelUser);die();

        }

        return $this->render('bonus', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'userFlag' => $userFlag,
            'modelUser' => $modelUser,
        ]);
    }
    /**
     * Displays a single UsersPays model.
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
     * Creates a new UsersPays model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = new UsersPays();
        $users = User::find()
            ->select([
                'id',
                'CONCAT(name, ", ", phone) AS name',
            ])->where(['status' => 1])
            ->orderBy('name ASC')->all();

        if ($model->load(Yii::$app->request->post())) {
            //$model->date = date('Y-m-d H:i:s');

            if (intval($model->type) == 23) {
                $modelUser = User::find()->where(['id' => $model->user_id])
                    ->one();
                $modelUser->money = $modelUser->money + $model->money;
                $model->save();
            }
            if (intval($model->type) == 22) {
                $modelUser = User::find()->where(['id' => $model->user_id])
                    ->one();
                $modelUser->money = $modelUser->money - $model->money;
                $model->money=(-1)*$model->money;
                $model->save();
            }


            if($modelUser->save())
                return $this->redirect(['index?sort=-date']);
            else
                return $this->render('create', [
                    'model' => $model,
                    'users' => $users,
                    'id' => $id,
                ]);

        }
        else {
            return $this->render('create', [
                'model' => $model,
                'users' => $users,
                'id' => $id,
            ]);
        }
    }

    /**
     * Creates a new UsersPays model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateNew($id = null)
    {
        $model = new UsersPays();
        $users = User::find()
            ->select([
                'id',
                'CONCAT(name, ", ", phone) AS name',
            ])->where(['status' => 1])
            ->orderBy('name ASC')->all();

        if ($model->load(Yii::$app->request->post())) {
            //$model->date = date('Y-m-d H:i:s');

            if (intval($model->type) == 23) {
                $modelUser = User::find()->where(['id' => $model['user_id']])
                    ->one();
                $modelUser->money = $modelUser->money + $model['money'];
                $model->save();
            }
            if (intval($model->type) == 22) {
                $modelUser = User::find()->where(['id' => $model['user_id']])
                    ->one();
                $modelUser->money = $modelUser->money - $model['money'];
                $model->money=(-1)*$model->money;
                $model->save();
            }


            if($modelUser->save())
                return $this->redirect(['index?sort=-date']);
            else
                return $this->render('create-new', [
                    'model' => $model,
                    'users' => $users,
                    'id' => $id,
                ]);

        }
        else {
            return $this->render('create-new', [
                'model' => $model,
                'users' => $users,
                'id' => $id,
            ]);
        }
    }


    /**
     * Creates a new UsersPays model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateBonus($id = null)
    {
        $model = new UsersBonus();
        $users = User::find()
            ->select([
                'id',
                'CONCAT(name, ", ", phone) AS name',
            ])->where(['status' => 1])
            ->orderBy('name ASC')->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->bonus = $model->type == 0 ? (-1)*$model->bonus : $model->bonus;

            $modelUser = User::find()->where(['id' => $model->user_id])->one();
            if($model->type == 0){
                $model->bonus = 0 - abs(intval($model->bonus));
            }

            if($model->save()){

            }else{
                //Zloradnij::print_arr($model->errors);die();
            }
            $modelUser->bonus = $modelUser->bonus + $model->bonus;

            if($modelUser->save())
                return $this->redirect(['bonus?sort=-date']);
                //return $this->redirect(['bonus']);
            else
                return $this->render('create', [
                    'model' => $model,
                    'users' => $users,
                    'id' => $id,
                ]);

           // return $this->redirect(['bonus?sort=-date']);
        } else {
            return $this->render('create-bonus', [
                'model' => $model,
                'users' => $users,
                'id' => $id,
            ]);
        }
    }

    /**
     * Updates an existing UsersPays model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UsersPays model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UsersPays model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UsersPays the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UsersPays::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
