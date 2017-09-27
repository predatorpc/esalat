<?php
use yii\helpers\Html;
use \app\modules\common\models\ModFunctions;
use kartik\date\DatePicker;


$this->title = Yii::t('app','Промо-код');
$this->params['breadcrumbs'][] = $this->title;

//print_arr($orders);
?>
<script>
    $(document).ready( function() {
        // Инициализация календар;
        $("input.date-input").date_input();
    });
</script>
<!--Content-->
<div class="content">

    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-3">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">
            <h1 class="title my"><?= $this->title; ?></h1>
            <div class="my-promo">
                <?php if(!empty($promo) and $promo['code']): ?>
                <div class="info">
                    <div class="code"><?=\Yii::t('app','Ваш промокод')?>: <b><?=$promo['code']?></b></div>
                    <div class="balance"><?=\Yii::t('app','Текущий баланс')?>:<span class="money"> <b><?= ModFunctions::money(Yii::$app->user->identity->money)?></b></span></div>
                    <div class="order-promo"><?=\Yii::t('app','Сумма вознаграждения')?>:<span class="money"> <b><?=ModFunctions::bonus((isset($promo['fee']) and $promo['fee'] > 0)  ? $promo['fee'] : '0')?></b></span><a href="/" onclick="return window_show('promo');" class="hidden"><?=\Yii::t('app','вывести средства')?></a></div>
                    <div class="order-promo"><?=\Yii::t('app','Сумма продажи с промо-кодом за текущий месяц')?>: <span class="money"><b><?=ModFunctions::money((isset($promo['total']) and $promo['total'] > 0)  ? $promo['total'] : '0')?></b></span></div>
                </div>
                <form method="post" action="">
                    <div class="report">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                        <div class="calendar-prom">
                            <?=\Yii::t('app','От')?>
                            <input  type="hidden" name="date_begin" value="<?php if(isset($_SESSION['report']['date_begin'])  ): ?><?=$_SESSION['report']['date_begin']?><?php else:?><?=date('Y-m-d');?><?php endif;?>" class="date-input" />
                            <?=\Yii::t('app','До')?>
                            <input type="hidden" name="date_end" value="<?php if(isset($_SESSION['report']['date_end'])  ): ?><?=$_SESSION['report']['date_end']?><?php else:?><?=date('Y-m-d');?><?php endif;?>" class="date-input" /></div>
                        <div class="button"><input type="submit" name="report" value="<?=\Yii::t('app','Сформировать')?>" class="button_oran"/></div>
                        <div class="clear"></div>
                        <div class="table-responsive">
                           <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                            <tr class="res">
                                <th>№</th>
                                <th><?=\Yii::t('app','Имя клиента')?></th>
                                <th><?=\Yii::t('app','Дата  покупки')?></th>
                                <th><?=\Yii::t('app','Наименование товара')?></th>
                                <th><?=\Yii::t('app','Количество')?></th>
                                <th><?=\Yii::t('app','Сумма покупки')?></th>
                                <th><?=\Yii::t('app','Бонусы вознаграждения')?></th>
                            </tr>

                            <?php if(isset($promo['orders']) and count($promo['orders']) > 0): ?>
                                        <?php foreach($promo['orders'] as $key => $orders): ?>
                                           <?php if(count($orders['promo']) > 0): ?>
                                               <tr onclick="$('tr.promo.t-<?=$orders['id']?>').toggle();" class="active">
                                                   <td colspan='7' class="order">#<strong><?=$orders['id']?> </strong> - <?=$orders['user_name']?><span class="glyphicon glyphicon-chevron-down" style="margin: 0px 0px 0 10px;position: relative;top: 3px;"></span> <span style="float: right;"><b>Итого: <?=$orders['money_total']?> р.</b></span></td>
                                               </tr>
                                             <?php $i = 1;?>
                                              <?php foreach($orders['promo'] as $key => $promoItems): ?>
                                                    <tr class="hidden_r promo t-<?=$orders['id']?> ">
                                                        <td><?=$i++;?></td>
                                                        <td class="user_name"><?=$orders['user_name']?></td>
                                                        <td><?=$orders['date']?> г.</td>
                                                        <td class="goods"><?=$promoItems['good_name']?></td>
                                                        <td><?=$promoItems['count']?> шт.</td>
                                                        <td><?=$promoItems['money']?> р.</td>
                                                        <td><?=$promoItems['fee'] + $promoItems['bonusBack']?> β.</td>
                                                    </tr>
                                               <?php endforeach;?>
                                          <?php endif;?>
                                        <?php endforeach;?>
                            <?php else: ?>
                            <tr><td colspan='7'><?=\Yii::t('app','Нет записей')?></td></tr>
                            <?php endif;?>
                        </table>
                        </div>
                    </div>
                </form>
                <div style="font-style: italic;">*<?=\Yii::t('app','Вознаграждение по промо-коду начисляется после получения заказа клиентом')?></div>
               <?php else: ?>
                <p><?=\Yii::t('app','У Вас нет промокода, для его создания обратитесь к управляющему клуба.')?>
<?php                    $staff = \app\modules\common\models\User::find()->where('id = '.\Yii::$app->user->getId())->one()->staff;
                    if(!empty($staff)) {
                        echo "<a href='/promo'>".Yii::t('app','Или нажмите здесь.')."</a>.";
                    }

?>
                </p>
              <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
