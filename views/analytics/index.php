<?php
use kartik\widgets\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\modules\catalog\models\GoodsTypes;
use yii\helpers\ArrayHelper;
$this->title = 'Аналитика';

$types = GoodsTypes::find()->asArray()->All();
$types = ArrayHelper::map($types, 'id', 'name');
?>

<?php $form = ActiveForm::begin(['method' => 'get']);?>
<?php
echo '<div style="display: inline-block; width: 60%;">';
echo '<div class="form-group" style="float: left;width: 45%;">';
echo '<label for="w1-kvdate">Дата от:</label>';
echo DatePicker::widget([
    'name' => 'dateFrom',
    'value' => $dateFrom,
    'options' => ['placeholder' => 'Выберите дату...'],
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true
    ]
]);
echo '</div>';
echo '<div class="form-group" style="float: right;width: 45%;">';
echo '<label for="w2-kvdate">Дата до:</label>';
echo DatePicker::widget([
    'name' => 'dateTo',
    'value' => $dateTo,
    'options' => ['placeholder' => 'Выберите дату...'],
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true
    ]
]);
echo '</div>';
echo '</div>';
?>
<div class="form-group">
    <?= Html::submitButton('Отчет', ['class' => 'btn btn-primary']) ?>
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>
    $('#w0').submit(function(){
        loading('show');
        return false;
    });
</script>
<?php ActiveForm::end(); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php if(isset($arTypes) && count($arTypes)>0){?>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                <?php foreach ($arTypes as $key => $value){?>
                ['<?=$types[$key];?>',<?=$value?>],
                <?php }?>
            ]);
            var options = {
                title: 'Cтастистика по типам товаров, %'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
        }
    </script>


    <div id="piechart" style="width: 100%; height: 500px;"></div>
    Всего: <?=array_sum($arTypes); ?>
<?php }?>

<?php if(isset($curOrders) && count($curOrders)>0){
    //print_r($curOrders);

    ?>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Дни', 'По запросу','На год раньше'],
                <?php foreach($curOrders as $day => $sum) {
                if(!isset($oldOrders[$day])){
                    $oldOrders[$day] = 0;
                }
                echo '[new Date('.date('Y,m-1,d',strtotime($day)).').toLocaleString("ru",{year: \'numeric\', month: \'long\', day: \'numeric\'}),'.$sum.','.$oldOrders[$day].'],';
            } ?>
            ]);

            var options = {
                title: 'Продажи за период, руб.',
                curveType: 'function',
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }
    </script>

    <div id="curve_chart" style="width: 100%; height: 500px"></div>
    Итого за переод: <?=array_sum($curOrders);?>
<?php }?>


<?php if(isset($arWeight) && count($arWeight)>0){?>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Дни', 'Вес, кг'],
                <?php foreach($arWeight as $day => $kg) {
                echo '[new Date('.date('Y,m-1,d',strtotime($day)).').toLocaleString("ru",{year: \'numeric\', month: \'long\', day: \'numeric\'}),'.round($kg,1).'],';
            } ?>
            ]);

            var options = {
                title: 'Средний вес заказа, кг',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
                    0: {color: '#fba000'},
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart_weight'));

            chart.draw(data, options);
        }
    </script>

    <div id="curve_chart_weight" style="width: 100%; height: 500px"></div>

<?php }?>

<?php if(isset($arPrice) && count($arPrice)>0){?>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Дни', 'Цена, руб.'],
                <?php foreach($arPrice as $day => $sum) {
                echo '[new Date('.date('Y,m-1,d',strtotime($day)).').toLocaleString("ru",{year: \'numeric\', month: \'long\', day: \'numeric\'}),'.round($sum,2).'],';
            } ?>
            ]);

            var options = {
                title: 'Средняя цена руб./кг',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
                    0: {color: '#0e9615'},
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart_price'));

            chart.draw(data, options);
        }
    </script>

    <div id="curve_chart_price" style="width: 100%; height: 500px"></div>

<?php }?>