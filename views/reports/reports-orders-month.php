<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart', 'bar']});
</script>
<?php
function nice_number($n)
{
    $n = (0 + str_replace(",", "", $n));

    if (!is_numeric($n)) return false;

    if ($n > 1000000000000) return round(($n / 1000000000000), 3) . ' trillion';
    elseif ($n > 1000000) return round(($n / 1000000), 3) . ' млн';
    elseif ($n > 1000) return round(($n / 1000), 3) . ' тыс.';

    return number_format($n);
}

//$formatter = new IntlDateFormatter('ru_RU', IntlDateFormatter::FULL, IntlDateFormatter::FULL);
//$formatter->setPattern('MMM YYYY');
//echo $formatter->format(new DateTime()); // 22 января

// \app\modules\common\models\Zloradnij::print_arr($arr);

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Отчет о заказах по месяцам');
$this->params['breadcrumbs'][] = $this->title;

?>

<script>
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Месяц', 'Сумма', {role: 'annotation'}, 'Кол-во', {role: 'annotation'}],
            <?php foreach ($arr as $a): ?>
            ['<?= date('m', strtotime($a['date_sum'])) ?>', <?= intval($a['money']) ?>, '<?= nice_number($a['money']) ?>', <?= $a['sum_count'] ?>, <?= $a['sum_count'] ?>],
            <?php endforeach; ?>
        ]);

        var options = {
            title: 'График о заказах по месяцам',
            legend: {position: 'right'},
            lineWidth: 3,
            curveType: 'function',
            height: 450,
            focusTarget: 'category',
            crosshair: {trigger: 'both'},
            vAxis: {
                title: 'Кол-во заказов и сумма', logScale: true, scaleType: 'log', gridlines: {count: 4}, 1: {
                    viewWindow: {
                        max: 1000
                    }
                }
            },
            hAxis: {title: 'Месяц'},
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

        chart.draw(data, options);
    }


    /* google.charts.load('current', {'packages':['corechart']});
     google.charts.setOnLoadCallback(drawVisualization);

     function drawVisualization() {
     var data = google.visualization.arrayToDataTable([
     ['Месяц', 'Кол-во', 'Сумма'],

     ]);

     var options = {
     title : 'График о заказах по месяцам',
     vAxis: {title: 'Кол-во заказов и сумма'},
     hAxis: {title: 'Месяц'},
     seriesType: 'bars',
     series: {5: {type: 'line'}}
     };

     var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
     chart.draw(data, options);
     }*/
</script>

<h1><?= Html::encode($this->title) ?></h1><br><br><br><br>

<div id="report-orders-month">


    <!-- График -->
    <div class="row">
        <div class="col-xs-12">
            <div id="chart_div"></div>
        </div>
    </div>
    <br><br>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <td style="font-weight: bold;">Месяц</td>
                    <td style="font-weight: bold;">Заказов на сумму</td>
                    <td style="font-weight: bold;">Количество заказов</td>
                </tr>
                </thead>

                <?php foreach ($arr as $a): ?>
                    <tr>
                        <td><?= $a['date_sum']; ?></td>
                        <td><?= number_format($a['money'], 0, ',', ' '); ?> ₽</td>
                        <td><?= $a['sum_count'] ?></td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </div>
    </div>

</div>

<?php // \app\modules\common\models\Zloradnij::print_arr($arr); ?>

