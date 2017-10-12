<?php
namespace app\components;

use app\modules\catalog\models\GoodsGroups;
use app\modules\common\models\Zloradnij;
use yii\base\Widget;
use yii\helpers\Url;
use yii\db\Query;
use app\modules\catalog\models\Category;
use app\modules\common\models\ModFunctions;
use app\components\WProductItemOne;
use app\modules\catalog\models\Goods;
use Yii;
/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
class WCatalogProductItem extends Widget {

    public $limit;
    public $categories;

    public function init() {
        parent::init();
        if ($this->categories === null) {
            $this->categories = false;
        }
        $this->limit  =  (!empty($this->limit) ? $this->limit : 0);
    }

    public function run(){
        $counts_category_parents = 0;

        ?>

                <?php
                   foreach ($this->categories as $category) {

                    $counts_category_parents = $category->getCategories()->count();

                    foreach ($category->getCategories()->limit(1)->offset($this->limit)->all() as $category_parent) {
                          print '<div class="main_title_js ">';
                                if(!empty($category_parent->categories)) {
                                        print '<h3 class="title"><b>' . $category_parent->title . '</b></h3>';
                                        ?>
                                        <div class="items">
                                            <?php foreach ($category_parent->productsClear as $key => $product):
                                                print WProductItemOne::widget([
                                                    'model' => $product,
                                                    'user' => Yii::$app->user->can('categoryManager'),
                                                    'categoryCurrent' => false,
                                                ]);
                                                ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php
                                }
                                if(!empty($category_parent->categories)) {
                                    foreach ($category_parent->categories as $category_i) {
                                        echo '<div class="clear"></div>';
                                        print '<div class="main_title_js">';
                                            print '<h3 class="title">' . $category_i->title . '</h3>';
                                            ?>
                                            <div class="items">
                                                <?php foreach ($category_i->productsClear as $k => $product):
                                                    print WProductItemOne::widget([
                                                        'model' => $product,
                                                        'user' => Yii::$app->user->can('categoryManager'),
                                                        'categoryCurrent' => false,
                                                    ]);
                                                    ?>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php
                                        print '</div>';

                                    }
                                }
                           print '</div>';
                            echo '<div class="clear"></div>';
                     }


                }
                ?>
                <?php if($counts_category_parents > 0): ?>
                   <div class="more more__load_js" data-count="<?=$counts_category_parents?>" data-all-cont="<?=$counts_category_parents?>"></div>
                   <div class="content-load" style="margin-top: 40px"></div>
                <?php  endif; ?>

        <?php
    }
}

