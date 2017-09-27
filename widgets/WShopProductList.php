<?php
namespace app\widgets;

use app\modules\catalog\models\Category;
use app\modules\common\models\UserShop;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\Shops;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\catalog\models\Goods;


/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Класс родительского блока "product-item" - якорь для javascript. НЕ УДАЛЯТЬ и не навешивать на него стили !!!!
class WShopProductList extends Widget {
    public $model;

    public function run(){
//        (new Category())->getCatalogPath();
//        (new Goods())->getCatalogPath();

        ?>
        
        <h3><?= $this->model->name?></h3>
        <a href="/shop/edit-store?shop_id=<?= $this->model->id;?>">Редактировать остаток</a>
        
        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $this->model->getProductsQuery()]),
//            'filterModel' => $searchModel,
            'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
            'tableOptions' => [
                'class' => 'table table-ad table-w  table-striped table-bordered'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute'=>'id',
                    'format'=>'raw',
//                    'value' => function ($data, $url, $model) {
//                        return Html::a($data['good_id'], "/logs/view?id=".$url);
//                    },
                    'value' => function ($data) {
                        return Html::a($data->name, (!empty($data->category) ? 'http://www.Esalad.ru'.$data->getCatalogPath() : ''));
                    },
                ],
                [
                    'attribute'=>'category',
                    'format'=>'raw',
                    'value' => function ($data) {
                        return !empty($data->category) ? $data->category->title : 'Нет категории';
                    },
                ],
            ],
        ]); ?>
        <?php
    }
}

