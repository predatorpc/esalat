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

    <h3><?= Yii::t('admin', 'Информация по промокоду') ?></h3>
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
                'contentOptions'=>['style'=>'max-width: 50px;']
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
                'label' => Yii::t('admin', 'Еда и товары'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-success">'.$data['totalSale']['food'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Спортивные товары'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-danger">'.$data['totalSale']['sport'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Скидка'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-info">'.$data['totalSale']['discount'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Доставка'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-info">'.$data['totalSale']['delivery'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Продажи'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-success">'.$data['totalSale']['total'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Еда и товары'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-success">'.$data['totalPurchase']['food'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Спортивные товары'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-danger">'.$data['totalPurchase']['sport'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Скидка'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-info">'.$data['totalPurchase']['discount'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Доставка'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-info">'.$data['totalPurchase']['delivery'].' p.</span><br>';
                    return $html;
                },
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Покупки'),
                'value' => function($data){
                    $html = '';
                    $html .= '<span class="text-success">'.$data['totalPurchase']['total'].' p.</span><br>';
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
            'dataProvider'=>$dataProvider,
            //'filterModel'=>$searchModel,
            'columns'=>$gridColumns,
            'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
            'headerRowOptions'=>['class'=>'kartik-sheet-style'],
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
            // parameters from the demo form
            'panel'=>[
                'type'=>GridView::TYPE_PRIMARY,
                'heading'=>false,
            ],
            'persistResize'=>false,
            //'exportConfig'=>$exportConfig,
        ]);
    }?>
</div>