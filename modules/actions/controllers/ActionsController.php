<?php

namespace app\modules\actions\controllers;

use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\common\controllers\BackendController;
use app\modules\common\models\User;
use Yii;
use app\modules\actions\models\Actions;
use app\modules\actions\models\ActionsParams;
use app\modules\actions\models\ActionsParamsValue;
use app\modules\actions\models\ActionsSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

/**
 * ActionsController implements the CRUD actions for Actions model.
 */
class ActionsController extends BackendController
{
    public $layout = '@app/views/layouts/control-page.php';
    //public $layout = 'control-page';
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
                            'index',
                            'view',
                            'update',
                            'create',
                            'compliment',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager', 'conflictManager'],
                    ],
                    [
                        'actions' => [
                            'compliment',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode',],
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

    /**
     * Lists all Actions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Actions model.
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
     * Creates a new Actions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Actions();
        //print_r(Yii::$app->request->post());die();

        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            $model->image = UploadedFile::getInstance($model, 'image');

            if(!empty($model->image)){
                $model->file_type = $model->image->getExtension();
            }
            $model->date_start = strtotime($model->date_start);
            $model->date_end = strtotime($model->date_end);

            if($model->save(true)) {
                if($model->uploadImage()){
                    //print_r($model);die();
                    return $this->redirect('index');
                }
            }

        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if(Yii::$app->request->isPost){

            if($model->load(Yii::$app->request->post())){
                $model->date_start = strtotime($model->date_start);
                $model->date_end = strtotime($model->date_end);
                $model->image = UploadedFile::getInstance($model, 'image');
                if(!empty($model->image)){
                    $model->file_type = $model->image->getExtension();
                }
                if($model->save(true)) {
                    if($model->uploadImage()){
                        return $this->redirect('index');
                    }
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionCompliment($userId = false)
    {
        $paramsId = ['category'=>132];
        $defaulActionParams = [
            'Actions' => [
                'title'         => 'Акция для клиента',
                'description'   => 'Комплимент',
                'priority'      => 1000,
                'block'         => 0,
                'accumulation'  => 0,
                'accum_value'   => 0,
                'count_purchase'=> 0,
                'accumulation'  => 0,
                'date_start'    => time(),//Date('Y-m-d H:i:s', time()),
                'date_end'      => strtotime('+1 month'),//Date('Y-m-d H:i:s', strtotime('month')),
                'image'         => null,
                'status'        => 1,
            ]
        ];

        if(!$userId){
            return false;
        }
        else{
            $model = new Actions();
            $modelParams = new ActionsParamsValue();
            $user = User::find()->where(['id'=>$userId])->one();
            if(Yii::$app->request->isPost && (!empty($user))){

                if($model->load($defaulActionParams) && $model->load(Yii::$app->request->post())){
                    if($modelParams->load(Yii::$app->request->post())){
                        $transaction = Yii::$app->db->beginTransaction();
                        $model->for_user_id = $user->id;
                        if($model->save(true)){
                            $transaction->commit();
                            //return true;
                        }
                        else{
                            $transaction->rollBack();
                        }
                    }
                }
            }

            $arCategory = Category::find()->where(['active'=>1])->asArray()->orderBy('parent_id')->All();
            $map = array();
            $arrayHelper = ArrayHelper::map($arCategory, 'id', 'parent_id');
            foreach ($arrayHelper as $id => $id_parent){
                if(empty($id_parent)){
                    $map[$id] = array();
                }elseif (isset($map[$id_parent])){
                    $map[$id_parent][$id] = array();
                }elseif(isset($arrayHelper[$id_parent])){
                    $map[$arrayHelper[$id_parent]][$id_parent][] = $id;
                }
            }
            $temp = ArrayHelper::map($arCategory, 'id', 'title');
            $arCategory = array();
            foreach ($map as $key => $value){
                $arCategory[$key] = $temp[$key];
                foreach ($value as $key1 => $value1){
                    $arCategory[$key1] = '->'.$temp[$key1];
                    foreach ($value1 as $key2 => $value2){
                        $arCategory[$value2] = '-->'.$temp[$value2];
                    }
                }
            }

            if(!empty($user)){
                $model = Actions::find()->where(['for_user_id'=>$user->id, 'status'=>1])->andWhere(['>','count_for_user',0])->one();
                if(!empty($model)){
                    $modelParams = ActionsParamsValue::find()->where(['action_id'=>$model->id,])->one();
                }
                else{
                    $model = new Actions();
                    $modelParams = new ActionsParamsValue();
                }

                if($modelParams->isNewRecord){
                    $modelParams->param_id = $paramsId['category'];
                    $modelParams->basket_price = 0;
                    $modelParams->discont_value = 0;
                    $modelParams->created_at = time();
                    $modelParams->updated_at = time();
                    $modelParams->created_user = Yii::$app->user->id;
                    $modelParams->updated_user = Yii::$app->user->id;
                }
                return $this->renderPartial('compliment', [
                    'action'=>$model,
                    'model' => $modelParams,
                    'category' => $arCategory,
                ]);
            }
            return false;


        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Actions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
