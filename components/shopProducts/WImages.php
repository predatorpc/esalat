<?php

namespace app\components\shopProducts;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Каталог;
class WImages extends Widget
{
    public $model;
    public $variant_id;
    public $images;
    public function init() {
        parent::init();
        if ($this->model === null) {
            $this->model = false;
        }
        if ($this->variant_id === null) {
            $this->variant_id = false;
        }

    }
    public function run(){
        if(!$this->model){
            return false;
        }else {
            $this->images[$this->variant_id][] = new \app\modules\catalog\models\GoodsImages(); ?>

            <?php
               foreach( $this->images[$this->variant_id] as $image){
                if(isset($image->id) && !empty($image->id)){?>
                    <div style="display:inline-block;position: relative;" class="item-image">
                    <img src="/files/goods/<?= \app\modules\catalog\models\GoodsImages::getImageFolder($image->id).'/'.$image->id?>.jpg" style="width:200px;" />
                    <div class="panel-ad">
                        <span class="glyphicon <?=($image->cover? 'glyphicon-ok-sign text-success' : 'glyphicon-plus-sign text-primary')?>" title="<?=($image->cover? 'Убрать обложка' : 'Добавить обложка') ?> " onclick="cover_images('<?= $this->model->id?>','<?= $this->variant_id?>','<?= $image->id?>');"></span>
                        <span
                            class="delete-image-block-new glyphicon glyphicon-remove-sign text-danger"
                            data-product="<?= $this->model->id?>"
                            data-variant="<?= $this->variant_id?>"
                            data-image="<?= $image->id?>"
                            title="Удалить?"
                        </span>

                    </div>

                    </div><?php
                }
            } ?>

       <?php
        }
    }
}
