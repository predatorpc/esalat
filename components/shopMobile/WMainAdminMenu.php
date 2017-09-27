<?php

namespace app\components\shopMobile;
use Yii;
use yii\base\Widget;

class WMainAdminMenu extends Widget {
    public  $menuAdmin;
    public function init(){
        parent::init();
        if($this->menuAdmin === null){
            $this->menuAdmin = false;
        }
    }
    public function run(){


        ?>
            <div class="items">
                <?php if(!empty($this->menuAdmin)):?>
                   <?php foreach($this->menuAdmin as $key => $admin_menu): ?>
                        <div class="item <?=($key == 'reports' ? 'reports' : '')?>" >
                            <div class="open_plus" rel="<?=$key?>"></div>
                            <a href="/catalog/<?=$admin_menu['link']?>" class="no-border <?php if(isset($admin_menu['items'])):?> groups <?php endif; ?>"> <?=$admin_menu['title']?></a>
                            <?php if(isset($admin_menu['items'])):?>
                                <div class="cell i-<?=$key?> <?=($key == 'products' ? 'show' : '')?>" >
                                    <?php foreach($admin_menu['items'] as $subcatAdmin): ?>
                                        <div class="i <?=($key == 'reports' ? 'reports' : '')?>">
                                            <a href="<?= $subcatAdmin['link']; ?>" class="no-border"><?=$subcatAdmin['title']?></a>
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach;?>
                <?php else: ?>
                    <div class="item"><a href="/shop-management/" class="no-border"><?=\Yii::t('app','Управление магазином')?></a></div>
                    <div class="item"><a href="/shop/" class="no-border"><?=\Yii::t('app','Личный кабинет поставщика')?></a></div>
                <?php  endif;?>
            </div>
<?php
    }
}

