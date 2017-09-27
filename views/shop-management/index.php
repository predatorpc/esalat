
<?php

//print_r(Yii::$app->reCaptcha->siteKey);die();



use yii\helpers\Html;
use yii\grid\GridView;


//\app\modules\common\models\Zloradnij::print_arr('!!!!!!!!!!');

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* version by predator_pc */

$this->title = 'Shops';
$this->params['breadcrumbs'][] = $this->title;

//\app\modules\common\models\Zloradnij::print_arr($sql);
//\app\modules\common\models\Zloradnij::print_arr(count($data));
//\app\modules\common\models\Zloradnij::print_arr($data);

$price = 0;
$priceCancel = 0;
$priceCancelTest = 0;

foreach ($statisticPrice as $item) {
    $price += $item['price'] * $item['count'];
}

foreach ($statisticCancelPrice as $item) {
    $priceCancel += $item['price'] * $item['count'];
}


foreach ($statisticCancelPriceTest as $item) {
    $priceCancelTest += $item['price'] * $item['count'];
}

$header = Yii::$app->controller->sortGraphResult($statisticGeneral);
$headerMonth = Yii::$app->controller->sortGraphResult($statisticGeneralMonth);
$headerYear = Yii::$app->controller->sortGraphResult($statisticGeneralYear, 'm');

//\app\modules\common\models\Zloradnij::print_arr($sort);die();

/*
 *

$dataArray = [];

foreach ($statisticGeneral as $key => $item) {
//    echo date('Y-m-d',strtotime($item['date']))."<br>";
    $index = date('Y-m-d', strtotime($item['date']));
    if (empty($dataArray[$index][$item['type_id']])) {
        $dataArray[$index][$item['type_id']] = 0;
    }
    $dataArray[$index][$item['type_id']] += $item['price'];
}

//\app\modules\common\models\Zloradnij::print_arr($dataArray);die();

$cnt = 0;
$newArray = [];
$arrItem = [];

foreach ($dataArray as $key => $item) {
    $arrItem['date'] = $key;
    $arrItem['sum'] = 0;
    foreach ($item as $i => $value) {
        $arrItem[$i] = $value;
        $arrItem['sum'] += $value;
    }
    $newArray[] = $arrItem;
    $arrItem = [];
}


//arsort($newArray);
//\app\modules\common\models\Zloradnij::print_arr($newArray);


$header[0] = ['День'];

//\app\modules\common\models\Zloradnij::print_arr(count($newArray));die();

$tmpArr = [];
//$cnt=0;
foreach ($newArray as $item) {
    $tmpArr[0] = $item['date'];

    if (!empty($item['1001'])) {
        $tmpArr[1] = $item['1001'];
        if(!in_array('Товары', $header[0]))
            array_push($header[0], 'Товары');
    }
    else {
        $tmpArr[1] = 0;
        if(!in_array('Товары', $header[0]))
            array_push($header[0], 'Товары');
    }

    if (!empty($item['1002'])) {
        $tmpArr[2] = $item['1002'];
        if(!in_array('Услуги', $header[0]))
            array_push($header[0], 'Услуги');
    }
    else{
        $tmpArr[2] = 0;
        if(!in_array('Услуги', $header[0]))
            array_push($header[0], 'Услуги');
    }

    if (!empty($item['1003'])) {
        $tmpArr[3] = $item['1003'];
        if(!in_array('Продукты', $header[0]))
            array_push($header[0], 'Продукты');
    }
    else {
        $tmpArr[3] = 0;
        if(!in_array('Продукты', $header[0]))
            array_push($header[0], 'Продукты');
    }

    if (!empty($item['1004'])) {
        $tmpArr[4] = $item['1004'];
        if(!in_array('Готовая еда', $header[0]))
            array_push($header[0], 'Готовая еда');
    }
    else{
        $tmpArr[4] = 0;
        if(!in_array('Готовая еда', $header[0]))
            array_push($header[0], 'Готовая еда');
    }

    if (!empty($item['1005'])) {
        $tmpArr[5] = $item['1005'];
        if(!in_array('Спортивные товары', $header[0]))
            array_push($header[0], 'Спортивные товары');
    }
    else{
        $tmpArr[5] = 0;
        if(!in_array('Спортивные товары', $header[0]))
            array_push($header[0], 'Спортивные товары');
    }

    if (!empty($item['1006'])) {
        $tmpArr[6] = $item['1006'];
        if(!in_array('Товары под заказ', $header[0]))
            array_push($header[0], 'Товары под заказ');
    }
    else {
        $tmpArr[6] = 0;
        if(!in_array('Товары под заказ', $header[0]))
            array_push($header[0], 'Товары под заказ');
    }

    if (!empty($item['1007'])) {
        $tmpArr[7] = $item['1007'];
        if(!in_array('Товары для дома', $header[0]))
            array_push($header[0], 'Товары для дома');
    }
    else{
        $tmpArr[7] = 0;
        if(!in_array('Товары для дома', $header[0]))
            array_push($header[0], 'Товары для дома');
    }

    if (!empty($item['1008'])) {
        $tmpArr[8] = $item['1008'];
        if(!in_array('Доставка 5-14 дней', $header[0]))
            array_push($header[0], 'Доставка 5-14 дней');
    }
    else{
        $tmpArr[8] = 0;
        if(!in_array('Доставка 5-14 дней', $header[0]))
            array_push($header[0], 'Доставка 5-14 дней');
    }

    if (!empty($item['1009'])) {
        $tmpArr[9] = $item['1009'];
        if(!in_array('Товары от Pure Protein', $header[0]))
            array_push($header[0], 'Товары от Pure Protein');
    }
    else{
        $tmpArr[9] = 0;
        if(!in_array('Товары от Pure Protein', $header[0]))
            array_push($header[0], 'Товары от Pure Protein');
    }

    if (!empty($item['1010'])) {
        $tmpArr[10] = $item['1010'];
        if(!in_array('Спортивное питание под заказ', $header[0]))
            array_push($header[0], 'Спортивное питание под заказ');
    }
    else{
        $tmpArr[10] = 0;
        if(!in_array('Спортивное питание под заказ', $header[0]))
            array_push($header[0], 'Спортивное питание под заказ');
    }

    $tmpArr[11] = $item['sum'];
    $header[] = $tmpArr;
    $tmpArr = [];
}

array_push($header[0], 'Всего');


*/


//\app\modules\common\models\Zloradnij::print_arr($header);die();
//\app\modules\common\models\Zloradnij::print_arr($header2);

?>

<h2><?= \Yii::t('admin', 'Добро пожаловать в панель управления!') ?></h2>
<hr>
<a href="#" onclick='void(window.open("http://2ip.ru/speed/?start=%E2%9C%93&id=425", "newWin","toolbar=no, directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=500,height=360"));'>
    <?= \Yii::t('admin', 'Спидометр') ?></a>
             
<h4><?= \Yii::t('admin', 'Продажи за период с') ?></a> <?=date("d/m/Y",strtotime($monday));?> <?= \Yii::t('admin', 'по') ?> <?=date("d/m/Y",strtotime($sunday));?></h4>
<h4><?= \Yii::t('admin', 'Всего продаж') ?> <?= count($data); ?> <?= \Yii::t('admin', 'на сумму') ?> <?= number_format($price, 2, '.', ' '); ?> <?= \Yii::t('admin', 'р.') ?></h4>
<h4><?= \Yii::t('admin', 'Всего продаж отменено') ?> <?= count($dataCancel); ?> <?= \Yii::t('admin', 'на сумму') ?> <?=  number_format($priceCancel, 2, '.', ' '); ?> <?= \Yii::t('admin', 'р.') ?>
    <?= \Yii::t('admin', 'из них тестовых') ?> <?= count($statisticCancelTest); ?> <?= \Yii::t('admin', 'на сумму') ?> <?=  number_format($priceCancelTest, 2, '.', ' '); ?> <?= \Yii::t('admin', 'р.') ?></h4>

<hr>

<div class="shops-index">

    <script type="text/javascript"
            src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawVisualization);
        google.charts.setOnLoadCallback(drawVisualizationMonth);
        google.charts.setOnLoadCallback(drawVisualizationYear);

        function drawVisualization() {
            // Some raw data (not necessarily accurate)
            var data = google.visualization.arrayToDataTable([

                <?php

                foreach($header as $i => $item)
                {
                    $str = '[';
                    foreach($item as $key => $value)
                    {
                         if($i==0) {$str.="'".$value."', ";}
                         else {if($key==0 && $i>0) {$str.="'".$value."', ";}
                         else {$str.=$value.", ";}}
                    }
                    $str.='],';
                    echo $str."\n";
                    $str='';
                }

                ?>

//         ['Month', 'Bolivia', 'Ecuador', 'Madagascar', 'Papua New Guinea', 'Rwanda', 'Всего'],
//         ['2004/05',  165,      938,         522,             998,           450,      614.6],
//         ['2005/06',  135,      1120,        599,             1268,          288,      682],
//         ['2006/07',  157,      1167,        587,             807,           397,      623],
//         ['2007/08',  139,      1110,        615,             968,           215,      609.4],
//         ['2008/09',  136,      691,         629,             1026,          366,      569.6]

            ]);

            var options = {
                title: '<?= \Yii::t('admin', 'Статистика продаж за текущую неделю на Esalad.ru') ?>',
                vAxis: {title: '<?= \Yii::t('admin', 'Рубли') ?>'},
                hAxis: {title: '<?= \Yii::t('admin', 'Дни') ?>'},
                seriesType: 'bars',
                curveType: 'function',
                series: {10: {type: 'line'}},
                colors: ['#ff0000','#0000ff', '#008edf',  '#ff5a00', '#ff5a00', '#00a8df', '#af4726', '#ff5a00', '#6400df', '#f6c7b6', '#268800',],
            };

            var chart = new google.visualization.ComboChart(document.getElementById('chart_div1'));

            chart.draw(data, options);
        }

        function drawVisualizationMonth() {
            // Some raw data (not necessarily accurate)
            var data = google.visualization.arrayToDataTable([

                <?php
                foreach($headerMonth as $i => $item)
                {
                $str = '[';
                    foreach($item as $key => $value)
                    {
                         if($i==0) {$str.="'".$value."', ";}
                         else {if($key==0 && $i>0) {$str.="'".$value."', ";}
                         else {$str.=$value.", ";}}
                    }
                    $str.='],';
                    echo $str."\n";
                    $str='';
                }

                ?>

//         ['Month', 'Bolivia', 'Ecuador', 'Madagascar', 'Papua New Guinea', 'Rwanda', 'Всего'],
//         ['2004/05',  165,      938,         522,             998,           450,      614.6],
//         ['2005/06',  135,      1120,        599,             1268,          288,      682],
//         ['2006/07',  157,      1167,        587,             807,           397,      623],
//         ['2007/08',  139,      1110,        615,             968,           215,      609.4],
//         ['2008/09',  136,      691,         629,             1026,          366,      569.6]

            ]);

            var options = {
                title: '<?= \Yii::t('admin', 'Статистика продаж за текущий месяц на Esalad.ru') ?>',
                vAxis: {title: '<?= \Yii::t('admin', 'Рубли') ?>'},
                hAxis: {title: '<?= \Yii::t('admin', 'Дни') ?>'},
                seriesType: 'bars',
                curveType: 'function',
                series: {10: {type: 'line'}},
                colors: ['#ff0000','#0000ff', '#008edf',  '#ff5a00', '#ff5a00', '#00a8df', '#af4726', '#ff5a00', '#6400df', '#f6c7b6', '#268800',],

//                seriesType: 'bars',
//                curveType: 'function',
//                series: {11: {type: 'line'}}

            };

            var chart = new google.visualization.ComboChart(document.getElementById('chart_div1a'));

            chart.draw(data, options);
        }


        function drawVisualizationYear() {
            // Some raw data (not necessarily accurate)
            var data = google.visualization.arrayToDataTable([

                <?php
                foreach($headerYear as $i => $item)
                {
                $str = '[';
                    foreach($item as $key => $value)
                    {
                         if($i==0) {$str.="'".$value."', ";}
                         else {if($key==0 && $i>0) {$str.="'".$value."', ";}
                         else {$str.=$value.", ";}}
                    }
                    $str.='],';
                    echo $str."\n";
                    $str='';
                }

                ?>

            ]);

            var options = {
                title: '<?= \Yii::t('admin', 'Статистика продаж за текущий год на Esalad.ru') ?>',
                vAxis: {title: '<?= \Yii::t('admin', 'Рубли') ?>'},
                hAxis: {title: '<?= \Yii::t('admin', 'Месяцы') ?>'},
                seriesType: 'bars',
                curveType: 'function',
                series: {10: {type: 'line'}},
                colors: ['#ff0000','#0000ff', '#008edf',  '#ff5a00', '#ff5a00', '#00a8df', '#af4726', '#ff5a00', '#6400df', '#f6c7b6', '#268800',],

//                seriesType: 'bars',
//                curveType: 'function',
//                series: {11: {type: 'line'}}

            };

            var chart = new google.visualization.ComboChart(document.getElementById('chart_div2'));

            chart.draw(data, options);
        }



    </script>

    <div id="chart_div1" style="width: 90%; height: 400px;"></div>
    <div id="chart_div1a" style="width: 90%; height: 400px;"></div>
    <div id="chart_div2" style="width: 90%; height: 400px;"></div>


</div>
