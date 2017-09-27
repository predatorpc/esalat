<?php

namespace app\controllers;

use app\modules\catalog\models\Codes;
use app\modules\common\controllers\BackendController;
use app\modules\common\models\UsersInvite;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Security;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\common\models\UserAdmin;
use app\modules\common\models\User;
use app\modules\common\models\UserSearch;

/**
 * UserController implements the CRUD actions for Useradmin model.
 */
class UserController extends BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'update',
                            'create',
                            'view',
                            'delete',
                            'inviteuser'
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'callcenterOperator', 'HR'],
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
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $model = UserAdmin::findOne($id);

        $model->role = Yii::$app->authManager->getRolesByUser($model->id);
        $model->role = current($model->role);

        if(!empty($model->role->description))
            $model->role = $model->role->description;

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
       $model = new UserAdmin();
        if ($model->load(Yii::$app->request->post())){
            $model->status = 1;
            $model->registration = date('Y-m-d H:i:s');
            $model->created_at = strtotime(date('Y-m-d H:i:s'));
            $model->auth_key = !empty($model->auth_key) ? $model->auth_key : Yii::$app->security->generateRandomKey();
            $model->phone="+7".$model->phone;

            if($model->save()){
               // Yii::$app->session->setFlash('OK save',$model->name);
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
              //  Yii::$app->session->setFlash('error save',$model->errors);
                return $this->render('create',[
                    'model' => $model,
                ]);
            }
        } else {
            if($model->errors){
                Yii::$app->session->setFlash('error LOAD',$model->errors);
            }

            return $this->render('create',[
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */

    public function actionUpdate($id)
    {
        $auth = Yii::$app->authManager;

        $model = $this->findModel($id);

        $model->registration = date('Y-m-d H:i:s');
        $model->role = Yii::$app->authManager->getRolesByUser($model->id);
        $model->auth_key = !empty($model->auth_key) ? $model->auth_key : Yii::$app->security->generateRandomString();


        if ($model->load(Yii::$app->request->post())) {
        
    	    if(intval($model->staff)==0)
    	    {
    		    $model->staff=null;
    	    }
    	    
            if(!empty($model->phone)){
//                $model->phone = str_replace('+7','',$model->phone);
            }
            if($model->save()) {

                if(!empty($model->role)) {
                    $role = $auth->getRole($model->role);
                    $auth->revokeAll($model->id);
                    $auth->assign($role, $model->id);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }else{
//                Zloradnij::print_arr($model);
                Zloradnij::print_arr($model->errors);
            }
        } else {
            if(!empty($model->role) && is_array($model->role)){
                $model->role = current($model->role);
                $model->role = $model->role->name;
            }else{

            }
//            Zloradnij::print_arr($model);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * desactivate users
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $user = $this->findModel($id);
        $user->status = 0;
        $user->save();
        return $this->redirect(['index']);

    }

    protected function findModel($id)
    {
        if (($model = UserAdmin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionInviteuser(){
        $model = new UsersInvite();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->status = 1;
            $model->password_hash = password_hash($model->password_hash,true);
            $model->created_at = time();
            $model->phone = '+7'.str_replace('-','',$model->phone);
            $model->registration = date('Y-m-d H:i:s');
            if(!$model->save()){
                print_r($model->getErrors());
            }

            $model = new UsersInvite();
            return $this->render('invite', ['model' => $model,'success' => 'Пользователь успешно добавлен!']);
        } else {
            return $this->render('invite', ['model' => $model,'success' => '']);
        }


    }
}
