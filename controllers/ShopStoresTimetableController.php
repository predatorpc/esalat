<?php

namespace app\controllers;

use app\modules\common\controllers\BackendController;
use Yii;
use app\modules\managment\models\ShopStoresTimetable;
use app\modules\managment\models\ShopStoresTimetableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShopStoresTimetableController implements the CRUD actions for ShopStoresTimetable model.
 */
class ShopStoresTimetableController extends BackendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ShopStoresTimetable models.
     * @return mixed
     */
//    public function actionIndex()
//    {
//        $searchModel = new ShopStoresTimetableSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    public function actionIndexModal($store_id)
    {
        $searchModel = new ShopStoresTimetableSearch();
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['ShopStoresTimetableSearch']['store_id'] = $store_id;
        $queryParams['ShopStoresTimetableSearch']['sort'] = 'day';
        $dataProvider = $searchModel->search($queryParams);
        return $this->renderPartial('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'store_id' => $store_id,
        ]);
    }

    /**
     * Displays a single ShopStoresTimetable model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('update', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ShopStoresTimetable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($store_id)
    {
        $model = new ShopStoresTimetable();
        $model->store_id = $store_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/shops/update', 'id' => $model->store->shop_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ShopStoresTimetable model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/shops/update', 'id' => $model->store->shop_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ShopStoresTimetable model.
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
     * Finds the ShopStoresTimetable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShopStoresTimetable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShopStoresTimetable::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
