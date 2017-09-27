<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\models\UserShop;

/* @var $this yii\web\View */
/* @var $searchModel \app\modules\coders\models\ClientLog */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Просмотр статистики работы мастера покупок');
$this->params['breadcrumbs'][] = $this->title;

//$basket = \app\modules\basket\models\BasketLg::find()->where(['id' => intval('114781'),'status' => 0])->all();
//$basket = $basket[0];
//\app\modules\common\models\Zloradnij::print_arr($basket);
?>
<div class="logs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);

    ?>


    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider(['query' => \app\modules\coders\models\ClientLog::find()->orderBy('id DESC')]),
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'master',
                'label'=> Yii::t('admin', 'Время работы мастера / c'),
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return $data->master;
                },
            ],

            [
                'attribute'=>'user_id',
                'label' => 'Name',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    if(!empty($data->user_id)){
                        $user = \app\modules\common\models\User::find()->where(['id' => $data->user_id])->one();
                        return $user->name . ' / ' . $user->phone;
                    }
                    return 'Not User';

                },
            ],

            'order_id',
        ],
    ]); ?>

</div>

