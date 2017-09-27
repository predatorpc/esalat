<?php

namespace app\controllers;

use app\modules\catalog\models\CategoryListLinks;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Lists;
use app\modules\catalog\models\ListsGoods;
use app\modules\catalog\models\ListsSearch;
use app\modules\common\controllers\BackendController;
use app\modules\common\models\Zloradnij;
use Faker\Provider\File;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * ListController implements the CRUD actions for Lists model.
 */
class ListController extends BackendController
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
                            'update',
                            'view',
                            'index',
                            'variationadd',
                            'getvariationname',
                            'updatelist',
                            'deletelist',
                            'updatelistitem',
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
    public function actionGetvariationname($name ='')
    {
        $name = Yii::$app->request->post('string');
        $id = Yii::$app->request->post('list_id');
        $amount = Yii::$app->request->post('amount');
        $sort = Yii::$app->request->post('sort');

        $model = $this->findModel($id);

        $query = ListsGoods::find()
            ->where('list_id = '.$id)
            ->andWhere('status = 1');

        $searchModel = new ListsGoods();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        //echo $name;

        if(!empty($name)){
          //  $query = new \yii\db\Query;

            /*    $query = \app\modules\catalog\models\Goods::find()
        ->andWhere(['shop_group.status' => 1])
        ->groupBy('goods.id');*/

            $variations = GoodsVariations::find()
                ->leftJoin('goods','goods.id = goods_variations.good_id')
                //  ->leftJoin('goods_variations','goods_variations.good_id = goods.id')
                ->leftJoin('category_links','category_links.product_id = goods.id')
                ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
                ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
//                ->select('goods_variations.id,goods_variations.full_name,goods_variations.status')
                ->where([
                    'goods.status' => 1,
                    'goods.confirm' => 1,
                    'goods.show' => 1,
                    'shop_group.status' => 1,
                    'goods_variations.status' => 1,
                ])
                ->andWhere([
                    'or',

                    ['like', 'goods.full_name', $name],
                    ['like', 'goods.name', $name],
                    ['like', 'goods_variations.full_name', $name],

                ])
                ->all();


//            $variations = GoodsVariations::find()
//                ->leftJoin('goods','goods.id = goods_variations.good_id')
//                //  ->leftJoin('goods_variations','goods_variations.good_id = goods.id')
//                ->leftJoin('category_links','category_links.product_id = goods.id')
//                ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
//                ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
////                ->select('goods_variations.id,goods_variations.full_name,goods_variations.status')
//                ->where([
//                    'goods.status' => 1,
//                    'goods.confirm' => 1,
//                    'goods.show' => 1,
//                    'goods_variations.status' => 1,
//                ])
//                ->andWhere(['shop_group.status' => 1])
//                ->andFilterWhere(['like', 'goods.full_name', $name])
//                ->orFilterWhere(['like', 'goods.name', $name])
//                ->orFilterWhere(['like', 'goods_variations.full_name', $name])
//                ->all();

//            $variations = GoodsVariations::find()
//                ->leftJoin('goods','goods.id = goods_variations.good_id')
//              //  ->leftJoin('goods_variations','goods_variations.good_id = goods.id')
//                ->leftJoin('category_links','category_links.product_id = goods.id')
//                ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
//                ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
////                ->select('goods_variations.id,goods_variations.full_name,goods_variations.status')
//                ->where([
//                    'goods.status' => 1,
//                    'goods.confirm' => 1,
//                    'goods.show' => 1,
//                    'goods_variations.status' => 1,
//                ])
//                ->andWhere(['shop_group.status' => 1])
//                ->andFilterWhere(['like', 'goods_variations.full_name', $name])
//                ->all();


       //     Zloradnij::print_arr($variations);die();

//            $users = $query
//                ->select('id,name,phone')
//                ->from('users')
//                ->where(['status' => 1])
////			->andWhere(['phone' => $phone])
//                ->andFilterWhere(['like','phone',$phone])
//                //   ->one();
//                ->all();

            foreach($variations as $item){
                $item->amount = $amount;
                $item->sort = $sort;
            }


//			return $this->render('index',['phone' => 'empty']);
            return $this->render('/list/update',[
                'id' => $id,
                'model' => $model,
                'stringHash' => $variations,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
            return $this->render('/list/update',[
                'id' => $id,
                'model' => $model,
                'stringHash' => 'empty',
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

//        return $this->render('update', ['username' => date('H:i:s')]);

    }

    public function actionVariationadd($id, $list_id, $amount, $sort)
    {
        $model = new ListsGoods();
        $model->variation_id = $id;
        $model->list_id = $list_id;
        $model->amount = $amount;
        $model->sort = $sort;
        $model->status = 1;

        $query = ListsGoods::find()
            ->where('list_id = '.$list_id)
            ->andWhere('status = 1');

        $searchModel = new ListsGoods();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        if($model->save()){
            $model = $this->findModel($list_id);
            return $this->redirect(['/list/update',
                                    'id' => $model->id,
                                    'model' => $model,
                                    'searchModel' => $searchModel,
                                    'dataProvider' => $dataProvider,
                                    //'error' => '',
            ]);
        }
        else
        {
            $error = 'Ошибка добавления вариации';
            return $this->redirect(['/list/update',
                                    'id' => $model->id,
                                    'model' => $model,
                                    'error' => $error,
                                    'searchModel' => $searchModel,
                                    'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Lists all Lists models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ListsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lists model.
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
     * Displays a single Lists model.
     * @param integer $id
     * @return mixed
     */


    public function actionUpdatelistitem()
    {
//        $id = Yii::$app->request->post();
//        Zloradnij::print_arr($id);die();


        $modelId = Yii::$app->request->post('modelId');
        $id = Yii::$app->request->post('ListsGoods');

        $model = ListsGoods::find()->where('id = ' . $id['id'])->one();
        if (!empty($model)) {

            if ($model->load(Yii::$app->request->post())) {

                if($model->save())
                {
                    return $this->render(
                        'updatelist', [
                            'model' => $model,
                            'modelId' => $modelId,
                        ]
                    );

                }
                else{
                    //Zloradnij::print_arr($model->errors);die();

                }

            } else {

                //Zloradnij::print_arr($model->errors);die();

                return $this->render(
                    'updatelist', [
                        'model' => $model,
                        'modelId' => $modelId,
                    ]
                );

            }
        }
    }



    public function actionUpdatelist($id, $modelId)
    {

        //var_dump(Yii::$app->request->post());die();

        $model = ListsGoods::find()->where('id = '.$id)->one();

//        Zloradnij::print_arr($lists);die();
        if(!empty($model)){

            if($model->load(Yii::$app->request->post()) && $model->save())
            {
                return $this->render(
                    'updatelist', [
                        'model' => $model,
                        'modelId' => $modelId,
                    ]
                );
            }
            else {

                return $this->render(
                    'updatelist', [
                        'model' => $model,
                        'modelId' => $modelId,
                    ]
                );

            }

        }
    }

    /**
     * Displays a single Lists model.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletelist($id, $model)
    {
        $lists = ListsGoods::find()->where('id = '.$id)->one();
        $lists->delete();

        return $this->redirect('/list/update?id='.$model);
    }

    /**
     * Creates a new Lists model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Lists();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/list/update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Lists model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelCategoryLinks = CategoryListLinks::find()->where(['list_id' => $id])->one();
        if(!$modelCategoryLinks){
            $modelCategoryLinks = new CategoryListLinks();
        }

        $query = ListsGoods::find()
            ->where('list_id = '.$id)
            //->joinWith('variation')
            ->andWhere('lists_goods.status = 1');

        $searchModel = new ListsGoods();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
//        $searchModel = new ListsSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $imageName = !empty($model->image) ? $model->image : '';
        if(!empty(Yii::$app->request->post())){
            $image = UploadedFile::getInstance($model,'image');
            if(!empty($image)){
                $imageName = explode('.',$image->name);
                if(count($imageName) > 0){
                    $imageName = Yii::$app->params['listImagePath'].$model->id .'.'.$imageName[count($imageName)-1];
//                    Zloradnij::print_arr($_SERVER['DOCUMENT_ROOT'].$imageName);
                    if($image->saveAs($_SERVER['DOCUMENT_ROOT'].$imageName)){
//                        Zloradnij::print_arr($_SERVER['DOCUMENT_ROOT'].$imageName.'--1');

                    }else{
//                        Zloradnij::print_arr($_SERVER['DOCUMENT_ROOT'].$imageName.'--2');
                    }
                    //$model->image = $imageName;
                }
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($modelCategoryLinks->load(Yii::$app->request->post())){
                $modelCategoryLinks->list_id = $model->id;
                if($modelCategoryLinks->save()){

                }
            }

            if(!empty($imageName)){
                $model->image = $imageName;
                $model->save();
            }

//            Zloradnij::print_arr($imageName);
            return $this->redirect(['view', 'id' => $model->id]);
//            return $this->render('update', [
//                'model' => $model,
//                'dataProvider' => $dataProvider,
//                'searchModel' => $searchModel,
//                'stringHash' => '',
//            ]);
        } else {
            return $this->render('update', [
                'id' => $id,
                'model' => $model,
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'stringHash' => '',
            ]);
        }
    }

    /**
     * Deletes an existing Lists model.
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
     * Finds the Lists model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lists the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lists::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
