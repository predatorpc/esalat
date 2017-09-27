<?php
use app\modules\common\models\ModFunctions;
use kartik\export\ExportMenu;
use yii\helpers\Url;
use kartik\grid\GridView;

$this->title = 'Отчет о продажах';
?>
<!--Отчет о продажам-->
<h3>Отчет о продажах сформирован <?=date('d.m.Y H:i:s')?></h3>
<div class="calendar-fast">
    <a class="dashed" href="<?=Url::to(['reports/mini-order-new', 'dateStart' => Date('Y-m-d', strtotime('last Monday')), 'dateEnd' => Date('Y-m-d')]);?>">Тек.нед.</a>|
    <a class="dashed" href="<?=Url::to(['reports/mini-order-new', 'dateStart' => Date('Y-m-d', strtotime('Monday  last week')), 'dateEnd' => Date('Y-m-d',strtotime('Sunday  last week'))]);?>">Прош.нед.</a>|
    <a class="dashed" href="<?=Url::to(['reports/mini-order-new', 'dateStart' => Date('Y-m-d', strtotime('first day of  this month')), 'dateEnd' => Date("Y-m-d",strtotime('last day of  this month'))]);?>">Тек.мес.</a>|
    <a class="dashed" href="<?=Url::to(['reports/mini-order-new', 'dateStart' => Date('Y-m-d', strtotime('first day of last month')), 'dateEnd' => Date("Y-m-d",strtotime('last day of last month'))]);?>">Прош.мес.</a><br>
</div>
<br>
<div id="panel-content" class=" panel">
    <?php
    $layoutGrid= '
        {summary}
        {items}
        <div class="clearfix"></div>
        {pager}
        ';
    echo GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $search->search(),
            'layout' => $layoutGrid,
            'responsive'=>false,
            'responsiveWrap'=>false,
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],
                [
                    'attribute' => 'date',
                    'label' => 'Дата',
                ],
                [
                    'attribute' => 'count',
                    'label' => 'Заказов',
                ],
                [
                    'attribute' => 'full_price',
                    'label' => 'Полная стоимость',
                ],
                [
                    'attribute' => 'detail',
                    'label' => 'Детально',
                    'value' => function($array){
                        $html = '';
                        $html .= 'Быстрая доставка: '.$array['detail']['fastOrder'].'<br>';
                        $html .= 'Спортпит: '.$array['detail']['sportGoods'].'<br>';
                        $html .= 'Товары Extreme: '.$array['detail']['extrmeGoods'].'<br>';
                        $html .= 'Прочее: '.$array['detail']['other'].'<br>';
                        $html .= 'Доставка: '.$array['deliveryPrice'].'<br>';

                        return $html;
                    },
                    'format' => 'raw',
                ],
//                [
//                    'attribute' => 'discount',
//                    'label' => 'Скидка',
//                ],
//                [
//                    'attribute' => 'bonus',
//                    'label' => 'Расчет бонусами',
//                ],
//                [
//                    'attribute' => 'profit',
//                    'label' => 'Прибыль',
//                ],
//                [
//                    'attribute' => 'revenues',
//                    'label' => 'Выручка',
//
//
//                ],
//                [
//                    'attribute' => 'deliveryPrice',
//                    'label' => 'Доставка',
//                ],
//                [
//                    'attribute' => 'payments_to_suppliers',
//                    'label' => 'Выплаты поставщикам',
//                ],
//                [
//                    'attribute' => 'cashback',
//                    'label' => 'Кэшбэк',
//                ],
//                [
//                    'attribute' => 'first_cost',
//                    'label' => 'Себестоимость',
//                ],
//                [
//                    'attribute' => 'commission',
//                    'label' => 'Комиссия за товар',
//                ],
//                [
//                    'attribute' => 'cancel',
//                    'label' => 'Отмены',
//                ],

            ],
        ]);?>
</div>


<div class="clear"></div>
