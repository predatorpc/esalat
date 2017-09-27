<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\widgets\Select2;


$labelParam = Yii::t('actions', $paramsName['title']);
$currency = [1=>'в рублях', 2=>'в процентах', 3=>'в бонусах'];
$type = [1=>'скидки', 2=>'зачисления'];

if(!empty($disable)){
    $dis = 'disabled="disabled"';
}
else{
    $dis = '';
}
//<a href="/actions/actions-params/update?id=<?= $paramsValue->param->id">
?>
<p>
    <a href="#">
        <?=$labelParam?>
    </a>
     <?php if($paramsValue->status == 1){?>
              <a class="btn btn-danger changeStatusActionParam" data-action-param-value-id="<?=$paramsValue->id;?>">Выключить</a>
                  <?php }else{?>
                          <a class="btn btn-success changeStatusActionParam" data-action-param-value-id="<?=$paramsValue->id;?>">Включить</a>
                              <?php } ?>
</p>

<div class="hidden">
    <input id="actionsparamsvalue-<?= $i?>-param_id" class="form-control"  <?=$dis?> type="hidden" value="<?= $paramsValue->param_id?>" name="ActionsParamsValue[<?= $i?>][param_id]">
</div>

<div class="form-group field-actionsparamsvalue-<?= $i?>-value required">
    <label class="control-label" for="actionsparamsvalue-<?= $i?>-object">Условие акции (id товара и тп)</label>
    <input id="actionsparamsvalue-<?= $i?>-condition_value" class="form-control number" <?=$dis?>  type="text" value="<?= $paramsValue->condition_value?>" name="ActionsParamsValue[<?= $i?>][condition_value]">
    <?php if($good = \app\modules\catalog\models\Goods::find()->where(['id'=>$paramsValue->condition_value])->One()){
        echo '<a href="/product/update?id='.$good->id.'" target="_blank" >'.$good->name.'</a>';
    }?>
    <div class="help-block"></div>
</div>

<div class="form-group field-actionsparamsvalue-<?= $i?>-value required">
    <label class="control-label" for="actionsparamsvalue-<?= $i?>-object">Стоимость корзины (в рублях)</label>
    <input id="actionsparamsvalue-<?= $i?>-basket_price" class="form-control number" type="text" <?=$dis?> value="<?= $paramsValue->basket_price?>" name="ActionsParamsValue[<?= $i?>][basket_price]">
    <div class="help-block"></div>
</div>

<div class="form-group field-actionsparamsvalue-<?= $i?>-object">
    <label class="control-label" for="actionsparamsvalue-<?= $i?>-object">Размер <?=$type[$paramsName['type']] . ' ' .$currency[$paramsName['currency']]?></label>
    <input id="actionsparamsvalue-<?= $i?>-discont_value" class="form-control number" <?=$dis?> type="text" value="<?= $paramsValue->discont_value?>" name="ActionsParamsValue[<?= $i?>][discont_value]">
    <div class="help-block"></div>
</div>

<hr style="border-style: groove;" />




