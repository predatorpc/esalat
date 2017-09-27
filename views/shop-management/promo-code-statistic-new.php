<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 28.03.2017
 * Time: 10:06
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\widgets\DetailView;
use app\modules\common\models\ModFunctions;

$this->title = Yii::t('admin', 'Статистика по промокодам');
$this->params['breadcrumbs'][] = $this->title;

$clubs = \app\modules\managment\models\ShopsStores::find()
    ->select('id, name')
    ->where('shop_id = 10000001')
    ->andWhere('status = 1')
    ->all();

if(empty($_GET['CodesSearch']['dateStart']))$_GET['CodesSearch']['dateStart'] = date("Y-m-d 00:00:00", strtotime("now"));
if(empty($_GET['CodesSearch']['dateStop'])) $_GET['CodesSearch']['dateStop'] = date("Y-m-d 23:59:59", strtotime("now"));


?>
<style>
    table thead,table thead a,thead a:link, thead a:visited{color:#444;}
</style>
<div class="codes-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search_promo-code-stat'); ?>

    <?php
    if(isset($dataProvider) && !empty($dataProvider)) {
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('admin', 'Владелец'),
                'attribute' => 'user',
            ],
            [
                'label' => Yii::t('admin', 'Промокод'),
                'attribute' => 'code',
            ],
            [
                'label' => Yii::t('admin', 'Тип промокода'),
                'attribute' => 'code_type',
            ],
            [
                'label' => Yii::t('admin', 'Количество использований'),
                'attribute' => 'count',
            ],
            [
                'label' => Yii::t('admin', 'Сумма продаж'),
                'attribute' => 'totalSale',
                'value' => function($data){
                    $html = '';
                    $html .= '<b>Сумма всего = '.$data['totalSale']['total'].' p.</b><br>';
                    $html .= '<span class="text-success">Еда и товары = '.$data['totalSale']['food'].' p.</span><br>';
                    $html .= '<span class="text-danger">Спортивные товары = '.$data['totalSale']['sport'].' p.</span><br>';
                    $html .= '<span class="text-info">Скидка =  '.$data['totalSale']['discount'].' p.</span><br>';
                    $html .= '<span class="text-info">Доставка ~  '.$data['totalSale']['delivery'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Сумма покупок'),
                'attribute' => 'totalPurchase',
                'value' => function($data){
                    $html = '';
                    $html .= '<b>Сумма всего = '.$data['totalPurchase']['total'].' p.</b><br>';
                    $html .= '<span class="text-success">Еда и товары = '.$data['totalPurchase']['food'].' p.</span><br>';
                    $html .= '<span class="text-danger">Спортивные товары = '.$data['totalPurchase']['sport'].' p.</span><br>';
                    $html .= '<span class="text-info">Скидка =  '.$data['totalPurchase']['discount'].' p.</span><br>';
                    $html .= '<span class="text-info">Доставка ~  '.$data['totalPurchase']['delivery'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Всего'),
                'value' => function($data){
                    $total = 0;
                    $total = $total + $data['totalPurchase']['total'] + $data['totalSale']['total'];
                    return $total;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Клуб'),
                'attribute' => 'club',
                'value' => function($data){
                    if(!empty($data['club'])){
                        $club = \app\modules\managment\models\ShopsStores::find()->where(['id'=>$data['club']])->One();
                        return $club->name;
                    }else{
                        return '';
                    }

                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Телефон владельца'),
                'attribute' => 'phone',
            ],

        ];

        echo GridView::widget([
            'id' => 'kv-grid-demo',
            'tableOptions'=> ['class'=>'mobile_ad'],
            'dataProvider'=>$dataProvider,
            //'filterModel'=>$searchModel,
            'columns'=>$gridColumns,
          //  'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
           // 'headerRowOptions'=>['class'=>'kartik-sheet-style'],
            'filterRowOptions'=>['class'=>'kartik-sheet-style'],
            'pjax'=>true, // pjax is set to always true for this demo
            // set your toolbar
            'toolbar'=> [
                '{export}',
                '{toggleData}',
            ],
            // set export properties
            'export'=>[
                'fontAwesome'=>true
            ],
            //'showPageSummary'=> true,
            // parameters from the demo form

            'panel'=>[
               // 'type'=>GridView::TYPE_PRIMARY,
                'heading'=>false,
            ],
            'responsive'=>false,
            'responsiveWrap'=>false,
            'persistResize'=>false,
            //'exportConfig'=>$exportConfig,
        ]);
    }

    $a = 0;
    $b = 0;
    foreach ($dataProvider->allModels as $allModel){
        $a = $a + $allModel['totalPurchase']['total'];
        $b = $b + $allModel['totalSale']['total'];
    }
    echo '<pre>';
    echo 'Всего продаж на '.ModFunctions::moneyFormat($a).' руб.'.PHP_EOL;
    echo 'Всего покупок на '.ModFunctions::moneyFormat($b).' руб.'.PHP_EOL;
    //print_r($dataProvider);
    echo '</pre>';
    ?>
</div>


<?php

if(!empty($trash)){
?>
<br><h3><?= Yii::t('admin', 'Сотрудники с нулевыми показателями за период') ?></h3><br>
<table  class="table table-striped table-hover">
    <tr>
        <th><b>#</b></th>
        <th><b>КОД</b></th>
        <th><b><?= Yii::t('admin', 'Телефон') ?></b></th>
        <th><b><?= Yii::t('admin', 'ФИО') ?></b></th>
    </tr>
    <?php
    $counter = 1;
    foreach ($trash as $key => $item) {
        echo "<tr>";
        echo "<td>" . $counter . "</td>";
        echo "<td>" . $item['code'] . "</td>";
        echo "<td>" . $item['phone'] . "</td>";
        echo "<td>" . $item['user'] . "</td>";
        echo "</tr>";
        $counter++;
    }
    }
    echo '</table>';
    ?>
