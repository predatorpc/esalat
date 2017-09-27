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
    {include file="templates/html/_shop_menu.html"}
    <div id="shop-statistics">
        <span id="current-graph" data-value="getNewValue"></span>

        <span class="getNewValue active" id="yearPrice" data-variant="Price" data-period="year">
            <?= Yii::t('admin', 'Доход / Год') ?>
            <span class="monthVariantSimbil">$</span>
        </span>
        <span class="getNewValue" id="yearCount" data-variant="Count" data-period="year">
            <?= Yii::t('admin', 'Количество / Год') ?>
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
                        '.Yii::t('admin', 'Количество').' / '.$monthLanguage[$month].'
                    </span>
                    <span class="getNewValue" id="'.$month.'Count" data-variant="Price" data-period="'.$month.'">
                        '.Yii::t('admin', 'Доход').' / '.$monthLanguage[$month].'
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
