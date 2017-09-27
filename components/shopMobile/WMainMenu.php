<?php

namespace app\components\shopMobile;
use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use Yii;
use app\controllers\MenuController;
use yii\base\Widget;

class WMainMenu extends Widget{
    public $key;
    public function run(){

        //Каталог;
        $catalogHash = Category::find()->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
        $urls = [];
        $catalogMenu = Catalog::buildTree($catalogHash,$urls);
        ?>
            <div class="items">
                <?php
                 //  print_arr(Yii::$app->controller->catalogMenu);
                ?>
               <?php foreach($catalogMenu as $key => $item_group): ?>


                      <div class="item <?php if($item_group['id'] == 10000005):?> _s1<?php endif; ?>">
                          <div class="border" style="<?=!empty($item_group['color']) ? 'background:'.$item_group['color'] : ''?>"></div>
                        <?php if(isset($item_group['items'])):?><div class="open_plus" rel="<?=$item_group['id']?>"></div> <?php endif; ?>
                        <a href="/catalog/<?=$item_group['alias']?>" class="no-border <?php if(isset($item_group['items'])):?> groups <?php endif; ?>"> <?=$item_group['title']?> (<?=Catalog::getCategoryGoodsCount($item_group['id'])?>)</a>
                        <?php if(isset($item_group['items'])):?>
                                <div class="cell i-<?=$item_group['id']?>">
                                    <?php foreach($item_group['items'] as $subcat): ?>
                                        <div class="i">
                                            <?php if(isset($subcat['items'])):?> <div class="open_plus" rel="<?=$subcat['id']?>"></div><?php endif; ?>
                                            <a href="/catalog/<?=$item_group['alias']?>/<?=$subcat['alias']?>" class="no-border<?php if(isset($subcat['items'])): ?> groups <?php else: ?>  arrow<?php endif; ?>"><?=$subcat['title']?> (<?=Catalog::getCategoryGoodsCount($subcat['id'])?>)</a>
                                            <?php if(isset($subcat['items'])):?>
                                                <div class="cell i-<?=$subcat['id']?>">
                                                    <?php foreach($subcat['items'] as $k => $i): ?>
                                                        <div class="i">
                                                            <a href="<?=$i['url']?>" class="no-border arrow"  rel="<?=$i['id']?>"><?=$i['title']?> (<?=Catalog::getCategoryGoodsCount($i['id'])?>)</a>
                                                        </div>
                                                    <?php endforeach;?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                  <?php endforeach;?>
                                </div>
                        <?php endif; ?>
                    </div>


               <?php endforeach;?>
            </div>
<?php
    }
}

