<?php

namespace app\modules\actions\controllers;

use app\modules\common\controllers\BackendController;
use Yii;
use app\modules\actions\models\ActionsParamsValue;
use app\modules\actions\models\ActionsParamsValueSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ActionsParamsValueController implements the CRUD actions for ActionsParamsValue model.
 */
class ActionsParamsValueController extends BackendController
{
    /**
     * @inheritdoc
     */
    public $layout = '@app/views/layouts/main';
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
                            'change-status',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode'],
                    ],
                    [
                        'actions' => [
                            'change-status',
                        ],
                        'allow' => true,
                        'roles' => ['categoryManager'],
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
     * Lists all ActionsParamsValue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActionsParamsValueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ActionsParamsValue model.
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
     * Creates a new ActionsParamsValue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ActionsParamsValue();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ActionsParamsValue model.
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
     * Deletes an existing ActionsParamsValue model.
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
     * Finds the ActionsParamsValue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ActionsParamsValue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ActionsParamsValue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionChangeStatus($id){
            $model = $this->findModel($id);
            if($model->status == 1){
                $model->status = 0;
                if($model->save()){
                    return 'off';
                }
            }elseif($model->status == 0 || $model->status == NULL){
                $model->status = 1;
                if($model->save()){
                    return 'on';
                }
            }
    }
}
