<?php

namespace app\controllers;

use app\modules\common\controllers\BackendController;
use app\modules\common\controllers\FrontController;
use Yii;

use app\modules\pages\models\Pages;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\CategorySearch;
use app\modules\catalog\models\Catalog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\common\models\UserShop;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class CategoryController extends BackendController
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
                            'index',
                            'index-page',
                            'create',
                            'view',
                            'view-page',
                            'update-page',
                            'update',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager','conflictManager', 'HR'],
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

/////////////////////////////////////////////////////////////
//  SEO & Category begin
////////////////////////////////////////////////////////////

// Анонимайзер генератор текст;
    public static function textGenerators ($title, $text)
    {
        // Тэги;
        $tag1 = 'купить,заказать';
        $tag2 = 'по выгодным,низким';
        $tag3 = 'нашем,этом';
        $tag4 = 'цены,стоимость';

        $title = mb_strtolower($title,'UTF-8');
        // Рандом перебор тэги;
        if(isset($tag1) and $tag1 != false) {
            $tag1 = explode(',', $tag1);
            $rand1 = rand(0, count($tag1) - 1);
        }
        if(isset($tag2) and $tag2 != false) {
            $tag2 = explode(',', $tag2);
            $rand2 = rand(0, count($tag2) - 1);
        }
        if(isset($tag3) and $tag3 != false) {
            $tag3 = explode(',', $tag3);
            $rand3 = rand(0, count($tag3) - 1);
        }
        if(isset($tag4) and $tag4 != false) {
            $tag4 = explode(',', $tag4);
            $rand4 = rand(0, count($tag4) - 1);
        }
        // Замена слов;
        $t = str_replace("[title]",$title, $text);
        if(isset($tag1) and $tag1 != false) $t = str_replace('[tag1]',$tag1[$rand1],$t);
        if(isset($tag2) and $tag2 != false) $t = str_replace('[tag2]',$tag2[$rand2],$t);
        if(isset($tag3) and $tag3 != false) $t = str_replace('[tag3]',$tag3[$rand3],$t);
        if(isset($tag4) and $tag4 != false) $t = str_replace('[tag4]',$tag4[$rand4],$t);
        return $t;
    }

    protected function findSeoModel($id)
    {
        if (($model = CategorySearch::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(
                'The requested page does not exist.'
            );
        }
    }

    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            '/category/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]
        );
    }

    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post())) {

            $searchModel = Category::find()->where(
                ['id' => $model->parent_id]
            )->one();

            if ($model->parent_id == '') {
                $model->level = 0;
            } else {
                if (!empty($model->parent_id) && $searchModel['level'] == 0) {
                    $model->level = $searchModel['level'] + 1;
                } else {
                    $model->level = $searchModel['level'] + 1;
                }
            }

            if ($model->save()) {
                return $this->redirect(['/category/view', 'id' => $model->id]);
            } else {
                return $this->render(
                    '/category/create', [
                        'model' => $model,
                        'parent' => Category::find()->where(['active' => 1])
                            ->orderBy('title ASC')->all(),
                    ]
                );
            }
        } else {
            return $this->render(
                '/category/create', [
                    'model' => $model,
                    'parent' => Category::find()->where(['active' => 1])->orderBy(
                        'title ASC'
                    )->all(),
                ]
            );
        }

    }

    public function actionUpdate($id)
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = $this->findSeoModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $data_anon = $this->textGenerators($model->title, $model->anon);
            $model->anon = $data_anon;
            $data_seo_description = $this->textGenerators($model->title, $model->seo_description);
            $model->seo_description = $data_seo_description;

            $searchModel = Category::find()->where(
                ['id' => $model->parent_id]
            )->one();

            if ($model->parent_id == '') {
                $model->level = 0;
            } else {
                if (!empty($model->parent_id) && $searchModel['level'] == 0) {
                    $model->level = $searchModel['level'] + 1;
                    //var_dump($searchModel);die();
                } else {
                    $model->level = $searchModel['level'] + 1;
                }
            }
            if ($model->save()) {
                return $this->redirect('/category/index');
            } else {
                return $this->render(
                    '/category/update', [
                        'model' => $model,
                        'parent' => Category::find()// ->where(['active' => 1])
                            ->orderBy('title ASC')->all(),
                    ]
                );
            }
        } else {
            return $this->render(
                '/category/update', [
                    'model' => $model,
                    'parent' => Category::find()// ->where(['active' => 1])
                        ->orderBy('title ASC')->all(),
                ]
            );
        }
    }

    public function actionDelete($id)
    {
            $model = $this->findSeoModel($id);
            $model->active = -1;
            $model->save();
            return $this->redirect('/category/index');
    }

    public function actionView($id)
    {
        $model = $this->findSeoModel($id);
        return $this->render(
            '/category/view', [
            'model' => $model,
        ]
        );
    }

//////////////////////////////////////////////////////////
//  SEO & Category end
/////////////////////////////////////////////////////////

/*------Редактирования страница-----------*/

    public function actionIndexPage()
    {
        $roles = array();
        // Правило роли;
        if(Yii::$app->user->can('HR') && Yii::$app->user->can('categoryManager')) {
            $roles = array('level'=>'6');
        }
        $dataProvider = new ActiveDataProvider([
            'query' => Pages::find()->where($roles),
        ]);

        return $this->render('index-page', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewPage($id)
    {
        return $this->render('view-page', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdatePage($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['view-page', 'id' => $model->id]);
        } else {
            return $this->render('update-page', [
                'model' => $model,
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = Pages::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
