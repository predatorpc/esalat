<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Профайл');
$this->params['breadcrumbs'][] = $this->title;

$averageActivityIndex = $data / $cntUsers;

//var_dump($index);die();

if(empty($index)) {
    echo Html::a(
        "Добавить текущий индекс Customer Average Activity Index = " . round(
            $averageActivityIndex, 2
        ) . " в базу данных",
        'generate-users-average-activity-index?index=' . round(
            $averageActivityIndex, 2
        ), ['class' => 'button btn primary']
    );
}

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'name',
        'rate',
        'date',
        ],
    ]);


?>


