<?php

namespace app\controllers;

use app\modules\common\controllers\BackendController;
use app\modules\common\models\UsersPays;
use Yii;
use app\models\UsersCredits;
use app\models\UsersCreditsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\common\models\User;

/**
 * UsersCreditsController implements the CRUD actions for UsersCredits model.
 */
class UsersCreditsController extends BackendController
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
                            'delete',
                            'getusername',
                            'create-all',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'HR', 'categoryManager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionGetusername($phone ='')
    {
//        $types = CodesTypes::find()->all();
        $model = new UsersCredits();

        $phone = Yii::$app->request->post('string');
        $id = Yii::$app->request->post('user_id');
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
            return $this->render('/users-credits/create',[
                'id' => $id,
                'model' => $model,
                //'types' => $types,
                'stringHash' => $users, //['name']
                'users' => $users,
            ]);
        }else{
            //    $users = [];
            return $this->render('/users-credits/create',[
                'id' => $id,
                'model' => $model,
                //'types' => $types,
                'stringHash' => $users, //['name']
                'users' => $users,
            ]);
//            return $this->render('/shops/update',[ 'model' => $model, 'stringHash' => 'empty']);
        }
//        return $this->render('update', ['username' => date('H:i:s')]);
    }



    /**
     * Lists all UsersCredits models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersCreditsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UsersCredits model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionCreateAll()
    {
        $users = UsersCredits::find()->where('status = 1')->all();

        if(!empty($users)) {
            foreach ($users as $user) {

                $transaction = new UsersPays();
                $transaction->status = 1;
                $transaction->user_id = $user->user_id;
                $transaction->type = 23; //income
                $transaction->comments = "Зачисление под з/п от " . date(
                        "Y-m-d", strtotime("now")
                    );
                $transaction->date = date("Y-m-d H:i:s", strtotime("now"));
                $transaction->type_id = 1;
                $transaction->created_user_id = Yii::$app->user->getId();
                $transaction->money = $user->amount;

           //     var_dump($transaction);die();

                if (!$transaction->save()) {
                    var_dump($transaction->errors);
                    die();
                }
                else
                {
            	    $usr = \app\modules\common\models\User::find()->where('id = '.$user->user_id)->one();
            	    $usr->money+=$user->amount;
            	    if(!$usr->save())
            	    {
            		var_dump($usr->errors);
            		die();
            	    }
                }
            }
        }

        return $this->redirect('/users-credits/index');
    }


    /**
     * Creates a new UsersCredits model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = new UsersCredits();

//        var_dump($model);

        $users = User::find()
            ->select([
                'id',
                'CONCAT(name, ", ", phone) AS name',
            ])->where(['status' => 1])
            ->orderBy('name ASC')->all();

        if(!empty($model))
        {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'users' => $users,
                    'id' => $id,
                    'stringHash' => $users, //['name']
                ]);
            }
        }
        else
            return $this->render('create', [
                'model' => $model,
                'users' => $users,
                'id' => $id,
                'stringHash' => $users, //['name']
            ]);

    }

    /**
     * Updates an existing UsersCredits model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $users = User::find()
            ->select([
                'id',
                'CONCAT(name, ", ", phone) AS name',
            ])->where(['status' => 1])
            ->orderBy('name ASC')->all();


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'users' => $users,
                'id' => $id,
                'stringHash' => $users, //['name']
            ]);
        }
    }

    /**
     * Deletes an existing UsersCredits model.
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
     * Finds the UsersCredits model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UsersCredits the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UsersCredits::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
