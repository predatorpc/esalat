<?php

namespace app\components;

use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use Yii;
use app\models\Zloradnij;
use yii\base\Widget;

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
class WLeftCatalogMenu extends Widget
{
    public $key;
    public $catalogHash;
    public $catalogMenu;
    public function init()
    {
        parent::init();
        if ($this->key === null) {
            $this->key = false;
        }
        $this->catalogHash = Category::find()->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
        $urls = [];
        $this->catalogMenu = Catalog::buildTree($this->catalogHash,$urls);
    }

    public function run(){?>
        <div class="category___sidebar category-list">

        <?php
        if(!empty($this->catalogMenu[$this->key]['items'])) {
            foreach ($this->catalogMenu[$this->key]['items'] as $menuItem) {
                ?>

                <?php if (!empty(Yii::$app->params['userCatalogHide']['status'])): ?>
                    <?php if ($this->key != Yii::$app->params['userCatalogHide']['id']): ?>
                        <div class="item">
                            <a class="main <?=$menuItem['id'] == 10000289 ? 'danger':'blue'?> " href="<?= $menuItem['url'] ?>">
                                <b><?= $menuItem['label'] ?></b>
                            </a>
                            <?php
                            if (isset($menuItem['items']) && !empty($menuItem['items'])) {
                                foreach ($menuItem['items'] as $menuItemLevelTree) { ?>
                                    <div class="i">
                                        <a class="blue" href="<?= $menuItemLevelTree['url'] ?>">
                                            <?= $menuItemLevelTree['label'] ?>
                                        </a>

                                    </div>
                                    <?php
                                }
                            } ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="item">
                        <a class="main blue" href="<?= $menuItem['url'] ?>">
                            <b><?= $menuItem['label'] ?></b>
                        </a>
                        <?php
                        if (isset($menuItem['items']) && !empty($menuItem['items'])) {
                            foreach ($menuItem['items'] as $menuItemLevelTree) { ?>
                                <div class="i">
                                    <a class="blue" href="<?= $menuItemLevelTree['url'] ?>">
                                        <?= $menuItemLevelTree['label'] ?>
                                    </a>
                                </div>
                                <?php
                            }
                        } ?>
                    </div>
                <?php endif; ?>
                <?php

            } ?>

            </div>

            <?php
        }
    }
}
