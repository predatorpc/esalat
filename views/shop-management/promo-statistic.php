<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GoodsVariationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Статистика по промокодам');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    table thead,table thead a,thead a:link, thead a:visited{color:#444;}
</style>
<div class="codes-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    \app\modules\common\models\Zloradnij::print_arr($list);
    ?>

</div>

