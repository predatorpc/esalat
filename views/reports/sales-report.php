<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart', 'bar']});
</script>
<?php

// \app\modules\common\models\Zloradnij::print_arr($array);

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Отчет о продажах (общий)');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1><br><br><br><br>

<div id="report-sales">

    <div class="row">

        <form class="form-horizontal text-right" action="" method="POST" id="getForm">
            <input type="hidden" name="_csrf" value="<?php Yii::$app->request->csrfToken ?>">
            <div class="col-sm-5 col-md-3">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label><?= Yii::t('admin', 'Дата от'); ?></label>
                        <input type="date" name="date1" id="date1" class="form-control"
                               value="<?php echo date("Y-m-d", strtotime("-1 week")); ?>" max="<?php echo date("Y-m-d"); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label><?= Yii::t('admin', 'Дата по'); ?></label>
                        <input type="date" name="date2" id="date2" value="<?php echo date("Y-m-d"); ?>"
                               class="form-control" max="<?php echo date("Y-m-d"); ?>" required>
                    </div>
                </div>
            </div>
            <div class="col-sm-5 col-md-3">
                <div class="form-group">
                    <div class="col-sm-12">
                        <button type="button" id="formPost" data-target="table1" class="btn btn-primary"
                                onclick="return sales_report();"><?= Yii::t('admin', 'Сформировать'); ?>
                        </button>
                        <a href="#chart_div" id="chart-show" class="btn btn-primary" role="button"><?= Yii::t('admin', 'Показать график'); ?></a>
                        <div class="loader"></div>
                    </div>

                </div>
                <p class="text-center"><?= Yii::t('admin', 'Для загрузки данных нажмите кнопку «Сформировать»'); ?></p>
            </div>
        </form>



    </div> <!--./row-->

    <div class="row">
        <div class="col-xs-12">
            <table id="table2" class="table table-bordered">
                <tr>
                    <td colspan="4" class="text-center"><?= Yii::t('admin', 'В среднем в сутки, руб.'); ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Всего покупок'); ?></td>
                    <td class="s1 bb text-center"></td>
                    <td><?= Yii::t('admin', 'Товары для дома'); ?></td>
                    <td class="s-home bb text-center"></td>
                    <!--<td>Сотрудники</td>
                    <td class="s3 bb text-center"></td>-->
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Продукты'); ?></td>
                    <td class="s2 bb text-center"></td>
                    <td><?= Yii::t('admin', 'Спортпит'); ?></td>
                    <td class="s-sport bb text-center"></td>
                    <!--<td>Клиенты</td>
                    <td class="s4 bb text-center"></td>-->
                </tr>
            </table>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-xs-12">
            <table id="table1" class="table table1 table-bordered">
                <thead>
                <tr>
                    <td rowspan="2"><?= Yii::t('admin', 'Дата'); ?></td>
                    <td rowspan="2"><?= Yii::t('admin', 'Сумма, руб.'); ?></td>
                    <!--<td colspan="4">В том числе</td>-->
                    <!--<td colspan="2">Потрачено сотрудниками на продукты и товары для дома</td>
                    <td colspan="2">Потрачено клиентами на продукты и товары для дома</td>-->
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Продукты'); ?></td>
                    <td><?= Yii::t('admin', 'Товары для дома'); ?></td>
                    <td><?= Yii::t('admin', 'Спортпит'); ?></td>
                    <td><?= Yii::t('admin', 'Прочие'); ?></td>
                    <!--<td>Рублей</td>
                    <td>Бонусов</td>
                    <td>Рублей</td>
                    <td>Бонусов</td>-->
                </tr>
                </thead>
                <tbody id="main-info" class="table-striped">
                </tbody>
                <tfoot>
                <tr>
                    <td><?= Yii::t('admin', 'Итог'); ?></td>
                    <td id="sum1"></td>
                    <td id="sum2"></td>
                    <td id="sum2_1"></td>
                    <td id="sum_sport"></td>
                    <td id="sum3"></td>
                    <!--<td id="sum4"></td>
                    <td id="sum5"></td>
                    <td id="sum6"></td>
                    <td id="sum7"></td>-->
                </tr>
                </tfoot>
            </table>
        </div>
    </div> <!--./row--><br>

    <!-- График -->
    <div class="row">
        <div class="col-xs-12">
            <div id="chart_div"></div>
            <!--<div id="container_chart" style="position:relative; height:100%;"></div>-->
        </div>
    </div>

</div> <!--./report-sales-->

<?php // \app\modules\common\models\Zloradnij::print_arr($array); ?>

