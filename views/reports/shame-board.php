<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\RatesAvg;
use app\models\RatesAvgSearch;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//\app\modules\common\models\Zloradnij::print_arr($users);
?>

<center>

<h3><?= Yii::t('admin', 'Доска позора (те кто не делал заказы с'); ?> <?=$date_start?> <?= Yii::t('admin', 'по'); ?> <?=$date_end?>)</h3>
    <?php
    echo Html::beginForm(['shame-board'], 'get', ['data-pjax' => 'false', 'class' => 'form-inline']);
//    echo DatePicker::widget([
//        'name'  => 'date_start',
//        'value'  => $date_start,
//        'language' => 'ru',
//        'dateFormat' => 'yyyy-mm-dd',
//    ]);
//
//    echo DatePicker::widget([
//        'name'  => 'date_end',
//        'value'  => $date_end,
//        'language' => 'ru',
//        'dateFormat' => 'yyyy-mm-dd',
//    ]);

    echo Yii::t('admin', 'Дата начала') . ' ';
    echo DatePicker::widget([
        'name' => 'date_start',
        //'type' => DatePicker::TYPE_INPUT,
        'value' => $date_start,
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd'
        ]
    ]);

    echo '&nbsp;&nbsp;&nbsp;'. Yii::t('admin', 'Дата конца'). ' ';
    echo DatePicker::widget([
        'name' => 'date_end',
        //'type' => DatePicker::TYPE_INPUT,
        'value' => $date_end,
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd'
        ]
    ]);
    echo Html::submitButton('Filter', ['class' => 'btn btn-primary', 'name' => 'hash-button']);
    echo Html::endForm();

    ?>
    </center>
<br>
<table cellpadding="0" cellspacing="0" width="100%" border="0">
    <tr>
        <td><b>#</b></td>
        <td><b>ID</b></td>
        <td><b><?= Yii::t('admin', 'Телефон'); ?></b></td>
        <td><b><?= Yii::t('admin', 'ФИО'); ?></b></td>
    </tr>
<?php

$counter = 0;
foreach ($users as $key=>$item){
        $color = ($counter%2)==0 ? "'#CCCCCC'" : "'#ffffff'";
        echo "<tr bgcolor=".$color.">";
        echo "<td>".$counter."</td>";
        echo "<td>".$item['id']."</td>";
        echo "<td>".$item['phone']."</td>";
        echo "<td>".$item['name']."</td>";
        echo "</tr>";
        $counter++;
}

?>
</table>
</center>


