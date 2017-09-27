<?php

namespace app\controllers;

use app\modules\catalog\models\TagsGroups;
use app\modules\common\controllers\BackendController;
use app\modules\common\controllers\FrontController;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsSearch;

use app\modules\catalog\models\Goods;

use app\modules\basket\models\Basket;
use app\modules\basket\models\SmallBasket;
use app\modules\catalog\models\Lists;
use app\modules\common\models\User;
use app\modules\catalog\models\GoodsImagesLinks;
use app\modules\catalog\models\GoodsSearch;
use app\modules\catalog\models\GoodsImages;
use app\modules\catalog\models\GoodsTypes;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\Shops;
use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\CategorySearch;
use app\modules\catalog\models\GoodsComments;
use app\modules\basket\models\BasketOne;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\WProductItemOneMini;



class WizardController extends FrontController
{
    public $layout = 'main-wizard';
    public $defaultAction = 'step1';

    public function behaviors()
    {
        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => [
//                            'create',
//                            'update',
//                            'view',
//                            'index',
//                            'step1',
//                            'step2',
//                            'step3',
//                            'step4',
//                        ],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
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


    /**
     * Lists all Tags models.
     * @return mixed
     */
    public function actionStep1()
    {
        $this->view->registerCssFile('/css/catalog.css');
        //die();
        return $this->render('step1');

        //Zloradnij::print_arr(Yii::$app->controller->catalogMenu);
        //die();
    }


    /**
     * Lists all Tags models.
     * @return mixed
     */
    public function actionStep()
    {
        $this->view->registerCssFile('/css/catalog.css');

        $parent = $urlList = $breadcrumbsCatalog = false;

        $model = new Category();

        $searchModelProducts = new GoodsSearch();
        $dataProviderProducts = $model->getCategoryActiveProductsMini(Yii::$app->request->queryParams,'new');

        foreach ($dataProviderProducts->getModels() as $product) {
            $urlList[$product->id] = $product->catalogUrl;//Category::getCategoryPath($product->categoryId, $this->catalogHash) . $product->id;
            $productsIds[] = $product->id;
        }
        $variationsAllProductsList = !empty($productsIds) ? $model->findVariations($productsIds) : [];
        $imagesAllProductsList = !empty($productsIds) ? Goods::findProductImages($productsIds) : [];
        $stickersAllProductsList = !empty($productsIds) ? Goods::findProductStickers($productsIds) : [];

        return $this->render('step1', [
            'model' => $model,
            'children' => [],
            'parent' => $parent,
            'dataProviderProducts' => $dataProviderProducts,
            'searchModelProducts' => $searchModelProducts,
            'breadcrumbsCatalog' => $breadcrumbsCatalog,
            'variationsAllProductsList' => $variationsAllProductsList,
            'imagesAllProductsList' => $imagesAllProductsList,
            'stickersAllProductsList' => $stickersAllProductsList,
            'urlList' => $urlList,
            'productTypes' => TagsGroups::find()->indexBy('id')->all(),
            //            'basket' => $this->basketObject,
        ]);
    }

    /**
     * Lists all Tags models.
     * @return mixed
     */
    public function actionStep2($id = false, $alias = false)
    {
        $this->view->registerCssFile('/css/catalog.css');
        $parent = $urlList = $breadcrumbsCatalog = false;




        if($alias == 'new/'){
            return $this->redirect('/wizard/step1');
        }else{
            $alias = explode('/',$alias);
            foreach ($alias as $i => $alia) {
                if(empty($alia)){
                    unset($alias[$i]);
                }
            }



            if(count($alias) == 1 && $alias[0] == intval($alias[0])){   // это товар
                $product = Goods::findOne($alias[0]);
                if(!$product){

                }else{
//                    die('FUCK!');
                    return $this->redirect($product->catalogPath);
                }
            }




            $view = Category::changeView($id);
            $breadcrumbsCatalog = Catalog::findBreadcrumbs($alias,$this->catalogHash);



            if(empty($breadcrumbsCatalog)){
                return $this->redirect('/wizard/new');
            }



            $currentCategory = end($breadcrumbsCatalog);

            if($view == 'view'){


                $currentCategories = Catalog::getChildsIds($currentCategory['id']);
                $currentCategories[] = $currentCategory['id'];


                if($currentCategory) {
                    $children = Category::find()->where(['parent_id' => $currentCategory['id']])->all();



                    if (isset($currentCategory->parent_id) && $currentCategory['parent_id'] > 0) {
                        $parent = Category::find()->where(['id' => $currentCategory['parent_id']])->all();
                    }



                    $model = $this->findModel($currentCategory['id']);



                    $searchModelProducts = new GoodsSearch();
                    //$dataProviderProducts = $model->findCategoryProducts($currentCategories, Yii::$app->request->queryParams);
                    $dataProviderProducts = $model->getCategoryActiveProductsMini(Yii::$app->request->queryParams);


                 //   die('FUCK4!');

                    foreach ($dataProviderProducts->getModels() as $product) {
                        $urlList[$product->id] = $product->catalogUrl;//Category::getCategoryPath($product->categoryId, $this->catalogHash) . $product->id;
                        $productsIds[] = $product->id;

                    }
                    $variationsAllProductsList = !empty($productsIds) ? $model->findVariations($productsIds) : [];
                    $imagesAllProductsList = !empty($productsIds) ? Goods::findProductImages($productsIds) : [];
                    $stickersAllProductsList = !empty($productsIds) ? Goods::findProductStickers($productsIds) : [];





                    // Быстрый просмтора товара;
                    if(isset($_POST['compact'])) {
                        $good_id = intval($_POST['good_id']);
                        $good_compact = Goods::getAllDataProduct($good_id);
                    }


                    return $this->render('step2', [
                        'modelLists' => Lists::find()->leftJoin('category_list_links','category_list_links.list_id = lists.id')->where(['lists.status' => 1,'category_list_links.category_id' => $model->id])->andWhere(['IS NOT','image',NULL]),
                        'model' => $model,
                        'children' => $children,
                        'parent' => $parent,
                        'dataProviderProducts' => $dataProviderProducts,
                        'searchModelProducts' => $searchModelProducts,
                        'breadcrumbsCatalog' => $breadcrumbsCatalog,
                        'variationsAllProductsList' => $variationsAllProductsList,
                        'imagesAllProductsList' => $imagesAllProductsList,
                        'stickersAllProductsList' => $stickersAllProductsList,
                        'urlList' => $urlList,
                        'productTypes' => TagsGroups::find()->indexBy('id')->all(),
                    ]);
                }
            }
        }

        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('step1', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all Tags models.
     * @return mixed
     */
    public function actionStep3()
    {
        $searchModel = new tagssearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('step3', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all Tags models.
     * @return mixed
     */
    public function actionStep4()
    {
        $searchModel = new tagssearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('step4', [
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
    public function actionCreate()
    {
        $model = new Tags();
        $groups = TagsGroups::find()->all();


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'groups' => $groups,
            ]);
        }
    }

    /**
     * Updates an existing Tags model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $groups = TagsGroups::find()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'groups' => $groups,
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
//    protected function findModel($id)
//    {
//        if (($model = Tags::findOne($id)) !== null) {
//            return $model;
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
