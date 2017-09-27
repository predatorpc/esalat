<?php
use app\modules\catalog\models\Goods;

?>
    <h1 class="name mobile  hidden_r"><?=$product->name?></h1>
    <div class="row">
        <!--Изображения-->
        <div class="col-md-4 col-xs-4 content-images">
            <div class="image">
                <?php if(!empty(Goods::findProductImage($product->id,'max'))){ ?>
                    <a href="http://www.esalad.ru<?= Goods::findProductImage($product->id,'max');?>" class="cloud-zoom"><img src="http://www.esalad.ru<?= Goods::findProductImage($product->id);?>" alt="<?=$product->name?>" class="ad" /></a>
                <?php }elseif(!empty(Goods::findProductImage($product->id))){ ?>
                    <img src="http://www.esalad.ru<?= Goods::findProductImage($product->id);?>" alt="<?=$product->name?>"  class="ad" />
                <?php } ?>

            </div>
        </div><!--/Изображения-->

        <div class="col-md-12 col-xs-12 content-info module__tab">
            <div class="nav nav-tabs">
                <div class="item active"><span data-target="#description" data-toggle="tab">Описание</span></div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade text in active variation-description continue-text" id="description" itemprop="description">
                    <?php if(isset($product->description)):?><?=$product->description?><?php else:?> <noindex><p>В данный момент мы работаем над описанием товара.</p></noindex><?php endif;?>
                </div>
            </div>
        </div>
        <div class="clear"></div><?php //die();?>
    </div>
