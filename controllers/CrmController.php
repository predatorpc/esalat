<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 15.02.2017
 * Time: 7:34
 */

namespace app\controllers;

use app\models\AuthAssignment;
use app\modules\common\models\User;
use app\modules\crm\models\CrmTasksComments;
use app\modules\managment\models\ShopsCallbackSearch;
use app\modules\crm\models\CrmTasks;
use app\modules\crm\models\CrmTasksSearch;
use app\modules\common\controllers\BackendController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;

class CrmController extends BackendController
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
                            'update',
                            'viewtask',
                            'index',
                            'report',
                            'createtask',
                            'addcomment',
                            'getusername',
                            'read'
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'conflictManager' , 'callcenterOperator', 'categoryManager', 'HR'],
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

    public function actionReport()
    {
        $searchModel = new ShopsCallbackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex(){
        $searchModel = new CrmTasksSearch();
        $conditions =  Yii::$app->request->queryParams;
        $userType = User::find()->select('typeof')->where(['id'=>Yii::$app->user->id])->asArray()->One();
        $conditions['CrmTasksSearch']['department'] = $userType['typeof'];

        $dataProvider = $searchModel->search($conditions);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreatetask($id = null, $search = null)
    {
        $model = new CrmTasks();
        $user[] = ['id' => 0, 'name' => ''];
        //Флаг изменения пользователяб ну а вдруг не задали
        $id_changed =Yii::$app->request->post('id_changed');


        if(intval($id_changed)==1 || empty($search)){
            if ($model->load(Yii::$app->request->post())) {
                $model->creator = Yii::$app->user->id;
                $curUser = User::find()->select('typeof')->where(['id'=>Yii::$app->user->id])->asArray()->One();
                $model->department = $curUser['typeof'];
                $model->date_create = date('Y-m-d h:i:s');
                if(intval($id_changed)==1) {
                    //   var_dump($model);
                    if (!$model->save()) {
                        //echo '<pre>'.print_r($model->getErrors(),1).'</pre>';
                        return $this->render(
                            'create_task', [
                                'model' => $model,
                                'user' => $user,]
                        );
                        die();
                    } else {
                        echo 'succses';
                        return $this->redirect(
                            ['index', 'id' => $model->id]
                        );
                    }
                }
                else {
                    $model->addError('user_id','Выберите пользователя!');
                    return $this->render(
                        'create_task', [
                            'model' => $model,
                            'user' => $user,]
                    );
                }
            } else {
                return $this->render('create_task', [
                    'model' => $model,
                    'user' => $user,
                ]);
            }
        }else{ //seaching
            $search_output = User::find()
                ->select([
                    'id',
                    'CONCAT(name, ", ", phone) AS name',
                ])->where(['status' => 1])->andWhere(['not', ['staff' => null]])->andWhere('phone like \'%'.$search.'%\'')
                ->orderBy('name ASC')->all();
            return $this->render(
                'create_task', [
                    'model' => $model,
                    'user' => $user,
                    'search_output' => $search_output,
                ]
            );
        }

    }

    public function actionViewtask($id = null, $search = null)
    {
        //Обязательно получим айди модели иначе еррор
        if(empty($id)){
            $id =Yii::$app->request->post('id');
            if(empty($id)){
                return $this->render('/site/error',['message'=>'No ID found!', 'code' => 007, 'name' => 'Error']);
            }
        }

        //Флаг изменения пользователя
        $id_changed =Yii::$app->request->post('id_changed');
        $model = $this->findModelTask($id);
        if(!empty($model->slave)){
            $user[] = User::find()
                ->select(['id','CONCAT(name, ", ", phone) AS name'])
                ->where('id = '.$model->slave)
                ->asArray()
                ->one();

        }
        if(empty($user)){
            $user[] = ['id' => 0, 'name' => ''];
        }



        $CrmTasksComments = CrmTasksComments::find()->where(['task_id' => $id])->orderBy('date')->All();

        if(intval($id_changed)==1 || empty($search)){
            if ($model->load(Yii::$app->request->post())) {
                //Говорят что можно и без этого но на всякий случай
//                $model->user_id = intval($model->user_id);
//                $model->card_id = intval($model->card_id);
//                $model->balance = intval($model->balance);
//                $model->status = intval($model->status);
                if(!$model->save()){
                    var_dump($model->errors);die();
                } // Shit happens
                else
                    return $this->redirect(['viewtask', 'id' => $model->id]);
            } else {
                return $this->render(
                    'view_task', [
                        'model' => $model,
                        'user' => $user,
                        'CrmTasksComments' => $CrmTasksComments
                    ]
                );
            }
        }
        else//searching render
        {
            $search_output = User::find()
                ->select([
                    'id',
                    'CONCAT(name, ", ", phone) AS name',
                ])->where(['status' => 1])->andWhere(['not', ['staff' => null]])->andWhere('phone like \'%'.$search.'%\'')
                ->orderBy('name ASC')->all();
            return $this->render(
                'view_task', [
                    'model' => $model,
                    'user' => $user,
                    'search_output' => $search_output,
                    'CrmTasksComments' => $CrmTasksComments
                ]
            );
        }

    }

    protected function findModelTask($id)
    {
        if (($model = CrmTasks::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddcomment(){
        $request = Yii::$app->request->post();
        $task_id = $request['task_id'];
        $model = new CrmTasksComments();
        $model->task_id = $task_id;
        $model->user_id = Yii::$app->user->id;;
        $model->date = date("Y-m-d h:i:s");
        $model->text = $request['comment'];
        $model->read = 0;
        if($model->save()){
            $model = $this->findModelTask($task_id);
            return $this->redirect(['viewtask',
                'id' => $model->id,
                'model' => $model,
                //'error' => '',
            ]);

        }
    }

    public function actionRead($id,$check){
        $comment = CrmTasksComments::find()->where(['id'=>$id])->One();
        $comment->read = $check;
        if(!$comment->save()){
            print_r($comment->getErrors());
        }
    }
}