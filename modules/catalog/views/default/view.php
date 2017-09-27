<?php
use app\components\WProductItemOne;
use yii\widgets\ListView;
use yii\widgets\Breadcrumbs;
$this->title = (isset($model->seo_title) && !empty($model->seo_title) ? $model->seo_title : $model->title);

$url = '/' . Yii::$app->params['catalogPath'] . '/';
$breadcrumbsUrl = '';
$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => $url,'template' => "{link}/\n"];
foreach($breadcrumbsCatalog as $item){
    if($item['title'] != $this->title){
        $url .= $item['alias'] . '/';
        $this->params['breadcrumbs'][] = ['label' => $item['title'], 'url' => $url,'template' => "{link}/\n"];
    }else{
        $url .= $item['alias'] . '/';
        $breadcrumbsUrl = $url;
    }
}
$this->params['breadcrumbs'][] = ['label' => $model->title,'template' => "{link} \n",];
reset($breadcrumbsCatalog);
$keyBreadcrumbsCatalog = key($breadcrumbsCatalog);

// SEO;
$this->registerMetaTag(['name' => 'description', 'content' => (!empty($model->seo_description) ? $model->seo_description : $model->description)]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords]);

// Параметры для мастер помощник;
$menuArrayEnd =  end($breadcrumbsCatalog);
$menuListId = (isset($menuArrayEnd['parent_id']) && !empty($menuArrayEnd['parent_id'])) ? $menuArrayEnd['parent_id'] : $keyBreadcrumbsCatalog;

Yii::$app->params['menuListId']['parent_id'] = $model->parent_id;
Yii::$app->params['menuListId']['id'] = $model->id;

?>
<div class="content">
    <!--Хлебная крошка-->
       <?= Breadcrumbs::widget(['options' => ['class' => 'path'],'tag' => 'div','homeLink' => ['label' => 'Главная', 'url' => '/','template' => "{link}/ \n"],'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]);?>
    <!--Хлебная крошка-->
    <div class="row">
        <div class="sidebar col-md-3">
            <h1 class="title"><?=$model->title?></h1>
            <?php
                 /* app\components\WSidebarFilter::widget()*/
            ?>
            <?php
            if ($this->beginCache('WLeftCatalogMenu_' . $model->id, ['duration' => Yii::$app->controller->module->params['cacheDuration']])){
                print app\components\WLeftCatalogMenu::widget(['key' => $keyBreadcrumbsCatalog]);
                $this->endCache();
            }
            ?>
        </div>


        <div class="goods  <?=(!empty(Yii::$app->params['mobile'])? 'goods-new' : '')?>col-md-9 col-xs-12">
            <div class="module___images"><?php
                $lists = $modelLists->all();
                if(!empty($lists)){
                    foreach ($lists as $list) {
                        print \app\components\WListInCatalog::widget([
                            'model' => $list,
                        ]);
                    }
                }
                ?>
                <div class="clear"></div>
            </div>
            <h1 class="title mobile-title-x"><?=$model->title?></h1>
            <?php
            $cacheKey = Yii::$app->controller->module->params['cacheTemplate'];
            $cacheKey = str_replace('#MODEL_ID#',Yii::$app->controller->module->params['category'],$cacheKey);
            $cacheKey = str_replace('#PAGE_ID#',(!empty($_GET['page']) ? $_GET['page'] : '1'),$cacheKey);
            Yii::$app->controller->module->params['cacheKey'] = $cacheKey;

//            if ($this->beginCache($cacheKey, ['duration' => Yii::$app->controller->module->params['cacheDuration']])){
                print ListView::widget([
                    'dataProvider' => $dataProviderProducts,
                    'options' => [
                        'tag' => 'div',
                        'class' => 'product-list js-product-list mod___goods_list goods-list',
                        'id' => 'list-wrapper',
                    ],
                    'itemOptions' => [
                        'tag' => 'div',
                        'class' => 'sort_item',
                    ],

                    'layout' => "
                           <div id='sort' class='items'>{items}<div class='clear'></div></div>\n{pager}
                           ",
                    'itemView' => function ($model){
                        return WProductItemOne::widget([
                            'model' => $model,
                            'user' => Yii::$app->user->can('categoryManager'),
                        ]);
                    },

                ]);
//                $this->endCache();
//            }
            ?>
           <div class="description-seo"><?=$model->description?></div>
            <?php if(isset($model->anon)): ?>
               <div class="description-seo"><?=$model->anon?></div>
            <?php endif;?>
        </div>
    </div>
    <div class="clear"></div>
</div>