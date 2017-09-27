<?php

use yii\helpers\Html;
use app\helpers\GridHelper;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Shops */
$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="cms-goods">
    <div class="statisticBlock row small">
        <h5>Статистика по товарам</h5>
        <?php
        foreach($statistic['value'] as $key => $item){
            print '
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small" style="text-align:right;margin-top: 1px;">'.$statistic['title'][$key].'</div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small" data-param="'.$key.'" style="margin-top: 1px;">'.$item.'</div>
                </div>
            </div>
            ';
        }
        ?>
    </div>
    <div class="shop_goods_lists">
        <div class="button_href">
            <a href="/shop/create-product">Добавить товар</a>
        </div>
        <?php
?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                //'producer_name',

                [
                    'attribute' => 'producer_name',
                    'value' => function($data) use ($tagsListValueByGroup){
                        if(isset($tagsListValueByGroup[$data->id][1008])){
                            return $tagsListValueByGroup[$data->id][1008];
                        }else{
                            return '';//$tagsListValueByGroup;
                        }
                    },
                    'format'=>'html'
                ],

                [
                    'attribute' => 'name',
                    'value' => ['app\helpers\GridHelper', 'getLinkValue'],
                    'format'=>'html'
                ],
                [
                    'attribute' => 'price_out',
                    'content' => function($data){
                        return '<span style="padding:0 15px;">'.$data->price_out.'</span>';
                    },
                    'format'=>'html'
                ],
                [
                    'attribute' => 'date_create',
                    'content' => function($data){
                        return date('d.m.Y H:i',strtotime($data->date_create));
                    },
                    'format'=>'html'
                ],
                [
                    'attribute'=>'status',
                    'filter' => ['Неактивные','Активные'],
                    'value' => ['app\helpers\GridHelper', 'columnStatusValue'],
                    'format'=>'html',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center']
                ],
                [
                    'attribute'=>'confirm',
                    'filter' => [-1 => 'Отклонён', 0 => 'На модерации', 1 => 'Одобрен'],
                    'value' => ['app\helpers\GridHelper', 'columnConfrmValue'],
                    'format'=>'html',
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center']
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header'=>'',
                    'headerOptions' => ['width' => '60'],
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url,$model,$key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                '/shop/update-product?id='.$key,
                                ['title' => 'Изменить товар']
                            );
                        },
                        'delete' => function ($url,$model,$key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-trash"></span>',
                                '/shop/delete?id='.$key,
                                [
                                    'title' => 'Удалить товар',
                                    'data-pjax' => 0,
                                    'data-method' => 'post',
                                    'data-confirm' => 'Вы уверены, что этот товар надо удалить?',
                                    'aria-label' => 'Delete'
                                ]
//<a data-pjax="0" data-method="post" data-confirm="Are you sure you want to delete this item?" aria-label="Delete" title="Delete" href="/shop/delete?id=10368064">
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
    <div class="clear"></div>
</div>