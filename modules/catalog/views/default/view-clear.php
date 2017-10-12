<?php
use app\components\WProductItemOne;
use yii\widgets\ListView;
use yii\widgets\Breadcrumbs;
use yii\helpers\HtmlPurifier;
use app\modules\common\models\ModFunctions;

$this->title = (isset($model->seo_title) && !empty($model->seo_title) ? $model->seo_title : $model->title);
// SEO;
$this->registerMetaTag(['name' => 'description', 'content' => (!empty($model->seo_description) ? $model->seo_description : '')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords]);

$cacheKey = Yii::$app->controller->module->params['cacheTemplate'];
$cacheKey = str_replace('#MODEL_ID#',$model->id,$cacheKey);
$cacheKey = str_replace('#PAGE_ID#',(!empty($_GET['page']) ? $_GET['page'] : '1'),$cacheKey);
Yii::$app->controller->module->params['cacheKey'] = $cacheKey;

//if ($this->beginCache($cacheKey, ['duration' => Yii::$app->controller->module->params['cacheDuration']])){

    $url = '/' . Yii::$app->params['catalogPath'] . '/';
    $breadcrumbsUrl = '';
    //$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Каталог'), 'url' => '/','template' => "{link}/\n"];

    foreach($breadcrumbsCatalog as $item){
        if($item['title'] != $model->title){
            $url .= $item['alias'] . '/';
            $this->params['breadcrumbs'][] = ['label' => $item['title'], 'url' => $url,'template' => "{link}/\n"];
        }else{
//            $this->params['breadcrumbs'][] = $item['title'];
//            $url .= $item['alias'] . '/';
//            $breadcrumbsUrl = $url;
        }
    }
    $this->params['breadcrumbs'][] = ['label' => $model->title,'template' => "{link} \n",];
    reset($breadcrumbsCatalog);
    $keyBreadcrumbsCatalog = key($breadcrumbsCatalog);



// Параметры для мастер помощник;
$menuArrayEnd =  end($breadcrumbsCatalog);
$menuArrayId = reset($breadcrumbsCatalog);



Yii::$app->params['menuListId']['parent_id'] = !empty($model->parent_id) ? $model->parent_id : $menuArrayEnd['id'];
Yii::$app->params['menuListId']['id'] = $menuArrayEnd['id'];
Yii::$app->params['menuListId']['catalog_id'] = $menuArrayId['id'];
// Состояние окно;
$session = Yii::$app->session;
$session['menuList'] = true;
/*
if(Yii::$app->session->get('shopMaster',0) > 0){
    $session['menuList'] = true;
}else{
    if(!empty($session['menuList'])){
        unset($session['menuList']);
    }
}*/
    ?>

    <div class="content">
        <!--Хлебная крошка -->
        <?= Breadcrumbs::widget(['options' => ['class' => 'path'],'tag' => 'div','homeLink' => ['label' => Yii::t('app','Главная'), 'url' => '/','template' => "{link}/ \n"],'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]);?>
        <!--Хлебная крошка-->
        <h1 class="title"><?=$model->title?></h1>
        <div class="row">
            <?php if(false): ?>
                <div class="sidebar col-md-3 col-xs-3 hidden">
                    <div class="hidden"><?=$model->id?></div>
                    <h1 class="title"><?=$model->title?></h1><?php
                    print app\components\WLeftCatalogMenu::widget(['key' => $keyBreadcrumbsCatalog]);?>
                </div>
            <?php endif;?>

            <div class="goods  <?=(!empty(Yii::$app->params['mobile'])? 'goods-new' : '')?>col-md-12 col-xs-12">
            <?php if(false): ?>
                <?php if(ModFunctions::img_path('/images/cat/'.$model->id.'.png')): ?>
                   <!--Постер-->
                   <div class="images_poster">
                      <img src="/images/cat/<?=$model->id?>.png" alt="<?=$model->title?> ">
                       <div class="content_img">
                           <div class="text"><?=preg_replace('~style="[^"]*"~i', '', $model->description);?></div>
                       </div>
                   </div><!--./Постер-->
                <?php else: ?>
                    <div class="_content_top">
                       <div class="title hidden"><?=$model->title?></div>
                       <div class="description-seo"><?=$model->description?></div>
                    </div>
                <?php endif; ?>
            <?php endif;?>
                    <div class="module___images"><?php
                    $lists = $modelLists->all();
                    if(!empty($lists)){
                        foreach ($lists as $list) {
                            print \app\components\WListInCatalog::widget([
                                'model' => $list,

                            ]);
                        }
                    }?>

                    <div class="clear"></div>
                </div>
                    <div class="top bottom js-shadow"></div>
                    <div id="list-wrapper"  class="product-list js-product-list mod___goods_list goods-top">
                        <div id='sort' class='items' style="overflow: hidden;"><?php
                            if(!empty($productsClear)){
                                // Активация мастер покупки;
                                if(!empty($session['menuList'])) {

                                    if(!empty($model->categories[0]->productsClear)) {
                                        foreach ($model->categories as $categories) {
                                            if ($categories->type_master) {
                                                echo '<h3 class="title-master"><a class="black" href="/' . $model->catalogpath . $categories->alias . '">' . $categories->title . '</a></h3>';
                                                echo '<div class="groups  js-disabled-page" data-group-category="' . $categories->id . '">';

                                                $goodsArray = array();
                                                if (!empty($categories->categories)) {
                                                    foreach ($categories->categories as $i) {
                                                        foreach ($i->productsClear as $goodsParent) {
                                                            $goodsArray[] = $goodsParent;
                                                        }
                                                    }
                                                }
                                                $goodsAll = (!empty($categories->categories) ? array_merge($categories->productsClear, $goodsArray) : $categories->productsClear);

                                                foreach ($goodsAll as $i => $product) {
                                                    if (!empty($product->master_active)) {
                                                        print WProductItemOne::widget([
                                                            'model' => $product,
                                                            'user' => Yii::$app->user->can('categoryManager'),
                                                            'categoryCurrent' => $menuArrayEnd,
                                                        ]);
                                                    }
                                                }
                                                echo '</div>';
                                            }
                                        }
                                    }else{
                                        foreach ($productsClear as $i => $product) {
                                            if($product->master_active) {
                                                print WProductItemOne::widget([
                                                    'model' => $product,
                                                    'user' => Yii::$app->user->can('categoryManager'),
                                                    'categoryCurrent' => $menuArrayEnd,
                                                ]);
                                            }
                                        }
                                    }
                                }else {
                                    foreach ($productsClear as $i => $product) {
                                        if(!$session['menuList']) {
                                            print WProductItemOne::widget([
                                                'model' => $product,
                                                'user' => Yii::$app->user->can('categoryManager'),
                                                'categoryCurrent' => $menuArrayEnd,
                                            ]);
                                        }
                                    }
                                }
                            }
                            ?>
                            <div class="last-element"></div>
                        </div>
                        <div
                            class="more"
                            data-page-id="2"
                            data-category-id="<?= $model->id?>"
                            data-page-size="<?= !empty($_GET['page-size']) ? intval($_GET['page-size']) : 40?>"
                            style="margin-top: 40px"
                        ></div>
                        <div class="content-load"></div>
                    </div>
                <?php if(!empty($model->anon)): ?>
                    <div class="description-seo"><?=$model->anon?></div>
                <?php endif;?>
            </div>

        </div>

        <div class="clear"></div>
    </div>
    <?php
//    $this->endCache();
//}
?>
