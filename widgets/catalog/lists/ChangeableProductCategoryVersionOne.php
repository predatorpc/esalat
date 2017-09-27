<?php

/* @var $product app\modules\catalog\models\Goods */

namespace app\widgets\catalog\lists;

use app\modules\catalog\models\Goods;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsGroups;
use app\modules\common\models\ModFunctions;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class ChangeableProductCategoryVersionOne extends Widget
{
    public $categoryName;
    public $listId;
    public $categoryId;

    public function init()
    {
        parent::init();

        if ($this->categoryName === null || $this->listId === null || $this->categoryId === null) {
            return false;
        }
    }

    public function run(){?>
       <div class="clear"></div>
        <div class="row text-center head-product-list-view">
            <div class="col-xs-12  title-main" ><?= $this->categoryName?></div>
        </div>
        <?php
    }
}

