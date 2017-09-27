<?php
use app\components\WProductItemOneMini;
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

//print_arr(print_arr(Yii::$app->user));
?>

<div class="content">

    <div class="row">

        <div class="goods col-md-9 col-xs-9">

            <?php
            $roleParamCach = '';
            if(!empty(Yii::$app->user->identity)){
                $roleParamCach = \Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
                if(!empty($roleParamCach) && is_array($roleParamCach)){
                    $roleParamCach = key($roleParamCach);
                }
            }

            //var_dump('ListView_' . $model->id . '_'.$roleParamCach.'_' . '_page-' . (!empty($_GET['page']) ? $_GET['page'] : '1'));die();
            //if ($this->beginCache('ListView_' . $model->id . '_'.$roleParamCach.'_' . '_page-' . (!empty($_GET['page']) ? $_GET['page'] : '1'), ['duration' => 300])){
            //if ($this->beginCache('ListView_' . $model->id . '_'.$roleParamCach.'_' . '_page-' . (!empty($_GET['page']) ? $_GET['page'] : '1'), ['duration' => 300])){
            //нет времени разбиратся пока отключил
            if(1){
                print ListView::widget([
                    'dataProvider' => $dataProviderProducts,
                    'options' => [
                        'tag' => 'div',
                        'class' => 'product-list js-product-list mod___goods_list goods-list',
                        'id' => 'list-wrapper',
                    ],
                    'layout' => "<div class='items'>{items}<div class='clear'></div></div>\n{pager}",
                    'itemView' => function ($model){
                        return WProductItemOneMini::widget([
                            'model' => $model,
                            'user' => Yii::$app->user->can('categoryManager'),
                        ]);
                    },

                ]);
                //     $this->endCache();
            }


            ?>
            <div class="description-seo"><?=$model->description?></div>
            <?php if(isset($model->anon)): ?>
                <div class="description-seo"><?=$model->anon?></div>
            <?php endif;?>
        </div>
    </div>
    <div class="clear"></div>
</div>