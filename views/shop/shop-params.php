<?php

use yii\helpers\Html;
use app\helpers\GridHelper;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Shops */
$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;

$dayWeekTitle = [
    1 => 'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота',
    7 => 'Воскресенье',
];
?>
<div id="shop-params">
    <div class="statisticBlock row small">
        <h5><?=$this->title?></h5>
        <?php
        foreach($shopParams as $i => $param){
            if(is_array($param['value'])){
                ?>
                <div class="row">
                    <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                        <div class="group small" style="text-align:right;margin-top: 1px;"><?=$param['title']?></div>
                    </div>
                    <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                        <div class="visibleShopParams group small" data-param="<?=$i?>" style="margin-top: 1px;">
                            <span class="value_span">
                                <?php
                                if(isset($shopParamsActive[$i])){
                                    $delimiter = '';
                                    foreach($shopParamsActive[$i] as $val){
                                        print $delimiter . Yii::$app->params['methodNotification'][$val];
                                        if($delimiter == ''){
                                            $delimiter = ', ';
                                        }
                                    }

                                }else{
                                    print "&nbsp;";
                                }?>
                            </span>
                            <?php
                            if($param['update']){
                                ?>
                                <span class='field'>
                                    <select class="value_input" multiple name="methodNotification[]">
                                        <option disabled><?=$param['title']?></option>
                                        <?php foreach ($param['value'] as $bit => $item) {
                                            print '<option value="'.$bit.'" '.((isset($shopParamsActive[$i][$bit]))?' selected':'').'>'.$item.'</option>';
                                        }?>
                                    </select>
                                    <span class='set_ok'>Сохранить</span>
                                </span>
                                <span class='changeFieldUpdate glyphicon glyphicon-pencil'></span>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }else{
                print '
                <div class="row">
                    <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                        <div class="group small" style="text-align:right;margin-top: 1px;">'.(isset($param['title'])?$param['title']:'&nbsp;').'</div>
                    </div>
                    <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                        <div class="visibleShopParams group small" data-param="'.$i.'" style="margin-top: 1px;">
                            <span class="value_span">'.(($param['value'])?$param['value']:'&nbsp;').'</span>
                            '.(($param['update'])?"<span class='field'><input class='value_input' type='text' value='".$param['value']."'><span class='set_ok'>Сохранить</span></span><span class='changeFieldUpdate glyphicon glyphicon-pencil'></span>":"").'
                        </div>
                    </div>
                </div>
                ';
            }
        }
        ?>
    </div>
    <div class="statisticBlock row small">
        <?php
        if($stores){
            ?>
            <h5>Склады</h5>
            <?php
            foreach($stores as $store){
                ?>
                <div class="row">
                    <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                        <div class="group small" style="text-align:right;margin-top: 1px;"><?=isset($store->name)?$store->name:'&nbsp;'?></div>
                    </div>
                    <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                        <div class="visibleShopParams group small" data-param="<?=$i?>" style="margin-top: 1px;">
                            <span class="value_span">
                                <?=isset($store->address)?$store->address:'&nbsp;'?>
                                <?=isset($store->address_id)?$store->address:'&nbsp;'?>
                            </span>
                        </div>
                    </div>
                    <?php
                    if(!$storesTime){

                    }else{
                        foreach($storesTime as $storeId => $storeDay){
                            if(empty($storeDay)){

                            }else{
                                foreach($storeDay as $storeItemList){
                                    if(!empty($storeItemList)){
                                        foreach($storeItemList as $storeItem){
                                            print '<pre style="display: none;">';
                                            print_r($storeItem);
                                            print '</pre>';
                                            ?>
                                            <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6 col-xs-offset-8 col-sm-offset-7 col-md-offset-7 col-lg-offset-6">
                                                <div class="visibleShopParams group small" data-param="<?$i?>" style="margin-top: 1px;">
                                                    <span class="value_span"><?=isset($storeItem->day)?$dayWeekTitle[$storeItem->day].' - ':''?><?=isset($storeItem->time_begin)?date('H:i',strtotime($storeItem->time_begin)).' : ':''?><?=isset($storeItem->time_end)?date('H:i',strtotime($storeItem->time_end)):''?></span>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>
    </div>

</div>