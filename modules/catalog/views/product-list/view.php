<?php
use \app\modules\basket\models\PromoCode;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $model app\modules\catalog\models\Lists */
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Каталог'),'url' => '/catalog/','template' => "{link}/ \n",];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Списки'),'url' => '/catalog/product-lists','template' => "{link}/ \n",];
$this->params['breadcrumbs'][] = ['label' => $this->title,'template' => "{link} \n",];

?>
<div class="content">
    <!--Хлебная крошка-->
    <?= \yii\widgets\Breadcrumbs::widget(['options' => ['class' => 'path'],'tag' => 'div','homeLink' => ['label' => Yii::t('app','Главная'), 'url' => '/','template' => "{link}/ \n"],'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]);?>
    <!--Хлебная крошка-->
    <div class="row">
        <div class="goods__tile " id="catalog-product-list-view">
            <div class="title"><h3><?= $model->title?></h3></div>
            <?php
            if(!empty($products)){
                $discount = Yii::$app->user->isGuest ? 0 : \app\modules\common\models\User::findOne(Yii::$app->user->identity->getId())->discount;
                //\app\modules\common\models\Zloradnij::print_arr($products) 10000071;
                foreach ($products as $categoryName => $categoryProducts) {
                    $firstProduct = \app\modules\catalog\models\GoodsVariations::findOne($categoryProducts[0]->variation_id);
                    print \app\widgets\catalog\lists\ChangeableProductCategoryVersionOne::widget([
                        'categoryName' => $categoryName,
                        'listId' => $model->id,
                        'categoryId' => $firstProduct->product->category->id,
                    ]);


//echo Yii::$app->request->get('code');
                       ?>
                       
                       
                       
                       <div class="content-list-goods" data-variant-id="<?= $categoryProducts[0]->variation_id?>" data-variant-id="0"> <?php
                            foreach ($categoryProducts as $i => $product){
                                if(!empty($product->variant->product)){
                                    print \app\widgets\catalog\lists\ChangeableProductVersionOne::widget([
                                        'product' => $product,
                                        'percent' => $discount,
                                        'activeTagsGroups' => $activeTagsGroups,
                                    ]);
                                }
                            }

                    ?>
                    </div>
                    <div class="clear"></div>
                    <div class="append-category-product" data-list-id="<?=  $model->id?>" data-variant-id="0" data-item-id="<?= $categoryProducts[0]->variation_id?>">
                        <span><?=\Yii::t('app','Добавить товар')?></span>
                        <div
                            class="change-product-container-block"
                            data-category="<?= $firstProduct->product->category->id;?>"
                            data-list="<?= $model->id?>"
                            data-action="add"
                        >
                            <div class="arrow"></div>
                            <div class="close-change" aria-hidden="true">&times;</div>
                            <img class="preload-image" src="/images/ajax_load.gif">
                        </div>
                    </div>
                    <?php
                }
            }?>
            <div class="clear"></div>
            <?php
            if(!empty(Yii::$app->user->identity->id)){
                $code = PromoCode::findOne(['status'=>1,'user_id'=>Yii::$app->user->identity->id]);
            }?>
            <?php if(!empty($code)): ?>
                <!--Code-->
                <div class="code " style="margin: 10px 0px 0px;">
                    <div class="input-group col-xs-12 col-sm-6">
                        <input type="text" class="form-control copy_input" value="<?=Url::to(['/catalog/product-list/'.$model->id, 'code' => $code->code],true)?>">
                          <span class="input-group-btn">
                            <button class="btn btn-danger copy" type="button">Скопировать ссылку</button>
                          </span>
                    </div><!-- /input-group -->
                </div> <!--/Code-->
                <script>
                    var button = document.querySelector('.copy');
                    var input = document.querySelector('input.copy_input');
                    button.addEventListener("click", function(event) {
                        event.preventDefault();
                       // input.value;
                        input.select(input.value);
                        var succeeded;
                        try {
                            succeeded = document.execCommand("copy");
                            var msg = succeeded ? 'succeeded' : 'no';
                            console.log('Cutting text command was ' + msg);
                            alert('Ваша ссылка скопирована!');
                        } catch (e) {
                            succeeded = false;
                        }
                    });
                </script>
            <?php endif; ?>
            <?php if(!Yii::$app->user->isGuest): ?>
                <!--Оформит заказ-->
                <div class="save-product-list-block text-center">
                    <p><b><?=\Yii::t('app','Сохранить список как')?></b></p>
                    <input class="form-control" type="text" name="product-list-name" placeholder="<?=\Yii::t('app','Название списка')?>" value="<?= $model->title?>" />
                    <input type="hidden" name="list-id" value="<?= $model->id?>" />
                    <div class="button_oran min center button__a save-product-list">
                        <div style="font-size: 14px"><?=\Yii::t('app','Сохранить список')?></div>
                    </div>
                    <div class="load"></div>
                    <!--                                        <div class="error center">Вот тут ошибка</div>-->
                    <hr />
                    <div class="button_oran center button__a buy-product-list"><div><?=\Yii::t('app','Купить')?></div></div>
                </div> <!--Оформит заказ-->
            <?php else: ?>
                <div class="text" style="text-align: center"><?=\Yii::t('app','Для сохранения списка необходимо')?> <a href="/" onclick="return window_show('login','<?=\Yii::t('app','Вход')?>');"><?=\Yii::t('app','войти')?></a> или <a href="/" onclick="return window_show('signup','<?=\Yii::t('app','Регистрация')?>');"><?=\Yii::t('app','зарегистрироваться')?></a></div>
            <?php endif;?>
        </div>
    </div>
</div>
