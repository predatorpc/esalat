<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Shops */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Shops', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="statisticBlock small">
        <?php
        foreach($statistic['value'] as $key => $item){
            print '
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small" style="text-align:right;margin-top: 1px;">'.$statistic['title'][$key].'</div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small" data-param="'.$key.'" style="margin-top: 1px;">'.$item.'</div>
                </div>
            </div>
            ';
        }
        ?>
    </div>
    <div id="shop-statistics">
        <span id="current-graph" data-value="getNewValue"></span>

        <span class="getNewValue active" id="yearPrice" data-variant="Price" data-period="year">
            Доход / Год
            <span class="monthVariantSimbil">$</span>
        </span>
        <span class="getNewValue" id="yearCount" data-variant="Count" data-period="year">
            Количество / Год
            <span class="monthVariantSimbil">C</span>
        </span>
        <hr />
        <div>
            <span class="arrowMonthLeft">&nbsp;&nbsp;&nbsp;<<&nbsp;&nbsp;&nbsp;</span>
            <?php
	    foreach($monthLine as $month){
            print '
	    <span class="monthVariant">
                <span class="monthTitle">
                    '.$monthLanguage[$month].'
                </span>
                <span class="monthStatisticValue">
                    <span class="getNewValue" id="'.$month.'Count" data-variant="Count" data-period="'.$month.'">
                        Количество / '.$monthLanguage[$month].'
                    </span>
                    <span class="getNewValue" id="'.$month.'Count" data-variant="Price" data-period="'.$month.'">
                        Доход / '.$monthLanguage[$month].'
                    </span>
                </span>
            </span>
	    ';
            }?>
            <span class="arrowMonthRight">&nbsp;&nbsp;&nbsp;>>&nbsp;&nbsp;&nbsp;</span>
        </div>
        <hr />

        <span id="shopStatisticData" data-value='<?=$visibleParams?>'></span>
        <div id="shopStatisticCanvas" style="padding:30px 50px;background:#FFF;"></div>
    </div>
</div>


<div class="shops-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'name_full',
            //'contract',
            //'tax_number',
            'description:ntext',
            'phone',
            //'min_order',
            //'delivery_delay',
            //'delay',
            //'comission_id',
            //'comission_value',
            //'count',
            //'show',
            //'notice',
            //'registration',
            //'status',
        ],
    ]) ?>

</div>
