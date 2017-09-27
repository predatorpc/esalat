<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\models\UserShop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Статистика покупок в мастере продаж');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logs-index">

    <h1><?= Html::encode($this->title) ?></h1>
   <div class="table-ad">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('admin', 'Покупатель'),
//                'attribute'=>'user_id',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    $result = !empty($data->user_id) ? $data->user->name : '';
                    $result .= !empty($data->user->phone) ? '<br />'.$data->user->phone : '';
                    if(!empty($data->user->staff))
                        $result .= "<br /><span style='color: #ebebf8;    background: #7c7ce2;    border-radius: 3px;    padding: 0px 5px 2px 5px;    margin: 0px 0px 0px 8px;'> Сотрудник</span>";
                    return $result;//!empty($data->user_id) ? $data->user->name : '';
                },
            ],

            [
                'label' => Yii::t('admin', 'Последнее обновление корзины'),
//                'attribute' => 'last_update',
                'format'=>'raw',
                'value' => function ($data, $url, $model) {
                    return date('Y-m-d',$data->last_update);
                },
            ],

            [
                'label'=> Yii::t('admin', 'Способ покупки: Всего / из списка / мастер'),
                'format'=>'html',
                'value' => function ($data, $url, $model) {
                    $catalog = 0;
                    $list = 0;
                    $master = 0;
                    if(!empty($data->products)){
                        $catalog = $master = $list = 0;
                        foreach ($data->products as $product) {
                            if($product->tool == 1){
                                $master++;
                            }
                            if($product->list_id > 0){
                                $list++;
                            }
                            $catalog++;
                        }
                    }
                    $result = $catalog . ' / ' . $list . ' / ' . $master;

                    return $result;
                },
            ],
            [
                'label'=> Yii::t('admin', 'Продукты'),
                'format'=>'html',
                'value' => function ($data, $url, $model) {
                    $result = '
                    <div>';
                    if(!empty($data->products)){
                        foreach ($data->products as $product) {
                            $master = $list = '';
                            if($product->tool == 1){
                                $master = '<span class="product-tool-master">M</span>';
                            }
                            if($product->list_id > 0){
                                $list = '<span class="product-tool-list">L</span>';
                            }
                            $result .= '
                            <div style="line-height: 50px;"><img src="'.$product->product->imageSimple.'" style="width:50px;margin-right:20px;" /><a href="/catalog/'.$product->product_id.'">'.$product->product->name.'</a>&nbsp;-&nbsp;'.$product->count.' шт.' . $master . $list . '</div>';
                        }
                    }
                    $result .= '
                    </div>';

                    return $result;
                },
            ],
        ],
    ]); ?>
   </div>
</div>

