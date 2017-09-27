<?php

/* @var \app\modules\catalog\models\Lists $list */

use yii\helpers\Html;
use yii\helpers\Url;
use \app\modules\common\models\ModFunctions;
use \app\modules\basket\models\PromoCode;

$this->title = Yii::t('app','Мои списки продуктов');
$this->params['breadcrumbs'][] = $this->title;
?>

<!--Content-->
<div class="content">
    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-3">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">
            <h1 class="title my"><?=$this->title?></h1>
            <div class="my-promo my-list-goods">
                <div class="table-responsive">
                <form method="post" action="">
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                        <tr class="res">
                            <th>№</th>
                            <th><?=\Yii::t('app','Список')?></th>
                            <th><?=\Yii::t('app','Сумма')?></th>
                            <th><?=\Yii::t('app','Кол-во')?></th>
                            <th><?=\Yii::t('app','')?></th>
                        </tr>
                            <?php
                            if(!empty($lists)){
                                $i = 0;
                                $code = PromoCode::findOne(['status'=>1,'user_id'=>Yii::$app->user->identity->id]);
                                foreach ($lists as $list) {?>
                                    <tr>
                                        <td style="width:50px"><?=$i++?></td>
                                        <?php if(!empty($code)): ?>
                                          <td><a href="<?=Url::to(['/catalog/product-list/'.$list->id, 'code' => $code->code],true)?>"><?= $list->title?></a></td>
                                        <?php else: ?>
                                          <td><a href="/catalog/product-list/<?= $list->id?>"><?= $list->title?></a></td>
                                        <?php endif; ?>
                                        <td><?=$list->fullPrice?> руб.</td>
                                        <td><?= \app\modules\common\models\Zloradnij::pluralForm(count($list->listsGoods),[Yii::t('app','товар'),Yii::t('app','товара'),Yii::t('app','товаров')])?></td>
                                        <td  style="text-align: center"><input type="checkbox" name="list[]" value="<?=$list->id?>" /> </td>
                                    </tr>
                                    <?php
                                }
                            }?>

                    </table>
                    <!--Тренер -->
                    <?php if(Yii::$app->user->can('GodMode')){

                    ?>
                    <div class="form " style="margin:0px;">
                        <div class="form-group hidden_r">
                            <label for="searchUser"><?=\Yii::t('app','Поиск тренера')?>:</label>
                            <div class="input-group">
                                  <input type="text" name="phone" class="form-control" id="searchUser" placeholder="+70000000000" maxlength="10">
                                  <span class="input-group-btn"><button id="searchGoUser" class="btn btn-default" type="button">Поиск</button></span>
                            </div><!-- /input-group -->
                            <input type="hidden" id="searchCodeUser" name="trainer_code" value="" >
                        </div>
                        <?php

                            echo Html::dropDownList('users', 'array', \yii\helpers\ArrayHelper::map($users, 'id', 'name'), ['multiple' => 'true']);

                        ?>
                        <div class="alert alert-info hidden_r"></div>
                        <button type="submit" name="send" class="btn btn-default"><?=\Yii::t('app','Добавить')?></button>

                    </div> <!--Тренер -->
                    <?php } ?>

                </form>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
