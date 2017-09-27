<?php
namespace app\components;
use yii\base\Widget;
use app\modules\pages\models\PagesMenus;
use Yii;

class WSidebar extends Widget {

    public function run(){
   ?>
        <!--Категория ЛК-->
        <div class="category___sidebar my-menu">
            <div class="item">
                <a href="#" class="main blue"><?=Yii::t('app','Кабинет');?></a>
                <?php foreach(PagesMenus::getPagesMyMenu() as $key => $menu): ?>
                    <?php if($menu['url'] == \Yii::$app->request->url): ?>
                        <div class="i"><div class="open"><?=$menu['label']?></div></div>
                    <?php else:?>
                        <div class="i"><a href="<?=$menu['url']?>" class="blue"><?=$menu['label']?></a></div>
                    <?php endif;?>
                <?php endforeach; ?>
            </div>
        </div><!--/Категория-->
     <?php
    }
}