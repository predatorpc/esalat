<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GoodsVariationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Статистика по промокодам');
$this->params['breadcrumbs'][] = $this->title;

if(empty($_GET['CodesSearch']['dateStart']))$_GET['CodesSearch']['dateStart'] = date("Y-m-d 00:00:00", strtotime("now"));
if(empty($_GET['CodesSearch']['dateStop'])) $_GET['CodesSearch']['dateStop'] = date("Y-m-d 23:59:59", strtotime("now"));


?>
<style>
    table thead,table thead a,thead a:link, thead a:visited{color:#444;}
</style>
<div class="codes-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search_promo-code-stat', ['model' => $searchModel, 'clubModel' => $clubModel]); ?>

    <h3><?= Yii::t('admin', 'Информация по промокоду') ?></h3>

    <?php
    //что за таблица???
    if(!empty($_GET['CodesSearch']['code']) && !empty($code->user_id)) {

        $code = \app\modules\catalog\models\Codes::findByCode($_GET['CodesSearch']['code']);
        $model = \app\modules\common\models\User::find()
            ->select([
                'users.id as id',
                'users.name as name',
                'users.phone as phone',
                'codes.code as code',
   //             'SUM(`users_bonus`.`bonus` * (-1)) AS bonus_sum',
                '(9999 - codes.count) as count',
            ])
            ->leftJoin('codes','codes.user_id = users.id')
            ->leftJoin('users_pays','codes.user_id = users_pays.user_id')
            //->leftJoin('users_bonus','codes.user_id = users_bonus.user_id')
            ->where('users.id = '.$code->user_id)
            //->andWhere('users_bonus.type = 6')
            ->one();

        //var_dump($model);die();

        $bonus = \app\modules\common\models\UsersBonus::find()
            ->select([
                'SUM(`bonus` * (-1)) AS summ',
            ])
            ->where('user_id = '.$code->user_id)
            ->andWhere('type = 6')
            ->one();


        $pays = \app\modules\common\models\UsersPays::find()
            ->select([
                'SUM(`money` * (1)) AS summ',
            ])
            ->where('user_id = '.$code->user_id)
            ->andWhere('type = 6')
            ->one();

        echo DetailView::widget(
            [
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('admin', 'ID Пользователя'),
                    ],
                    [
                        'attribute' => 'phone',
                        'label' => Yii::t('admin', 'Телефон'),
                    ],
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('admin', 'Имя пользователя'),
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'code',
                        'label' => 'Promo code',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'count',
                        'label' => Yii::t('admin', 'Кол-во покупок'),
                        'format' => 'raw',
                    ],

                    [
                        'label' => Yii::t('admin', 'Сумма вознаграждения бонусами'),
                        'attribute' => 'id',
                        'format' => 'html',
                        'value' => (!empty($bonus->summ)) ? $bonus->summ : "0",
                    ],

                    [
                        'label' => Yii::t('admin', 'Сумма вознаграждения рублями'),
                        'attribute' => 'id',
                        'format' => 'html',
                        'value' => (!empty($pays->summ)) ? $pays->summ : "0",
                    ],

                ],
            ]
        );
    }

    ?>

    <?php
    if(isset($dataProvider) && !empty($dataProvider)) {
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('admin', 'Промо-код'),
                'attribute' => 'code',
                'vAlign'=>'middle',
                //'filterType'=>GridView::FILTER_SELECT2,
                //'filter'=>ArrayHelper::map(\app\modules\catalog\models\Codes::find()->orderBy('code')->asArray()->all(), 'id', 'code'),
                //'filterWidgetOptions'=>[
                //    'pluginOptions'=>['allowClear'=>true],
                //],
                //'filterInputOptions'=>['placeholder'=>'Промокод'],
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Тип кода'),
                'attribute' => 'type.name',
                'vAlign'=>'middle',
                'mergeHeader'=>true,
                'format'=>'raw',

            ],
            [
                'label' => Yii::t('admin', 'Количество покупок'),
                'attribute' => 'countSale',
                'vAlign'=>'middle',
                'mergeHeader'=>true,
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Владелец'),
                'attribute' => 'user_id',
                'value' => function($data, $url){
                    $code = \app\modules\catalog\models\Codes::find()->where('id = '.$url)->one();
                    if(!empty($code->user_id)){
                        $user = \app\modules\common\models\User::find()->where('id = '.$code->user_id)->one();
                        return html::a($data['userName'],['/user/update?id='.$user->id],['class' => 'btn btn-default']);
                    }
                    else
                        return '';
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(\app\modules\common\models\User::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=> Yii::t('admin', 'Владелец') ],
                'format'=>'html',
            ],
            [
                'label' => Yii::t('admin', 'Сумма 1'),
                'attribute' => 'summa',
                'format' => 'html',
                'value' => function($model){
                    
                $code = \app\modules\catalog\models\Codes::find()->where('user_id = '.$model->user_id)->andWhere('status = 1')->one();
                
//                var_dump($_GET['CodesSearch']['dateStart']);
                
                $wocode = \app\modules\shop\models\Orders::find()
            	    ->select('orders.*, orders_groups.*, orders_items.*')
            	    ->leftJoin('orders_groups','orders.id = orders_groups.order_id')
            	    ->leftJoin('orders_items','orders_groups.id = orders_items.order_group_id')
            	    ->where('user_id = '.$model->user_id)
            	    ->andWhere('code_id is null')
            	    ->andWhere('orders.status = 1')
            	    ->andWhere('orders_groups.status = 1')
            	    ->andWhere('orders_items.status = 1')
                    ->andWhere('date >= \''.date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart'])).'\'')
                    ->andWhere('date <= \''.date("Y-m-d 23:59:00", strtotime($_GET['CodesSearch']['dateStop'])).'\'')   
                    ->asArray()
            	    ->all();
            	//    \app\modules\common\models\Zloradnij::print_arr($wocode);die();
            	    
		    $c=0;
		    foreach($wocode as $item){
			$c+=$item['price']*$item['count'];
		    }
            	//    var_dump($c);die();
    
		    if(!empty($_GET['CodesSearch']['wocode']))
			$c = $model->summa + $c;
		    else 
			$c = $model->summa;
		    
                    $result = '<b>Сумма всего = '.\app\modules\common\models\ModFunctions::money($c)."</b><br><br>";


                    $delivery = 0;
                    $discount = 0;

                    if(isset($_GET['CodesSearch']['usetype']) && intval($_GET['CodesSearch']['usetype'])==0) {
                        //    var_dump($model->user_id);die();
                        
/*                            $selfPurchase = \app\modules\shop\models\Orders::find()->where(
                            [
                                'user_id' => $model->user_id,
                                'code_id' => $code->id,
                                'status' => 1,
                            ])
                            ->andWhere('date >= \''.date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart'])).'\'')
                            ->andWhere('date <= \''.date("Y-m-d 23:59:00", strtotime($_GET['CodesSearch']['dateStop'])).'\'')
                            ->all();                        
*/                        
                        if(!empty($_GET['CodesSearch']['wocode'])){
                            $selfPurchase = \app\modules\shop\models\Orders::find()->where(
                            [
                                'user_id' => $model->user_id,
//                              'code_id' => $code->id,
			//	'code_id' => 'null',
                                'status' => 1,
                            ])
                            ->andWhere('date >= \''.date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart'])).'\'')
                            ->andWhere('date <= \''.date("Y-m-d 23:59:00", strtotime($_GET['CodesSearch']['dateStop'])).'\'')
		            ->all(); 
                        }
                        else{
                            $selfPurchase = \app\modules\shop\models\Orders::find()->where(
                            [
                                'user_id' => $model->user_id,
                                'code_id' => $code->id,
                                'status' => 1,
                            ])
                            ->andWhere('date >= \''.date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart'])).'\'')
                            ->andWhere('date <= \''.date("Y-m-d 23:59:00", strtotime($_GET['CodesSearch']['dateStop'])).'\'')
                            ->all();
			} 
//                        $selfPurchase = \app\modules\shop\models\Orders::find()->where('user_id = ' . $model->user_id)-->all();
//              	\app\modules\common\models\Zloradnij::print_arr($selfPurchase->createCommand()->getRawSql());die();
                     // var_dump($selfPurchase);die();
                    //    $sql = $selfPurchase->createCommand()->getRawSql();
                    //    var_dump($sql);die();

                        $summSelfPurchaseFoods = 0;
                        $summSelfPurchaseOther = 0;
                        $summSelfDelivery = 0;
                        foreach ($selfPurchase as $key => $item) {
                            $groups = \app\modules\shop\models\OrdersGroups::find()->where('order_id = ' . $item->id)
                                ->andWhere('status = 1')
                                ->all();

                            foreach ($groups as $value) {
                                if(intval($value->type_id)==1001 || intval($value->type_id)==1002 || intval($value->type_id)==1003
                            		 || intval($value->type_id)==1004 || intval($value->type_id)==1007 || intval($value->type_id)==1006) {

                                    if(intval($value->delivery_id)==1003){  $delivery+=150;}
                                    elseif(intval($value->delivery_id)==1006){ $delivery+=150;}
                                    elseif(intval($value->delivery_id)==1007){  $delivery+=300;}

                                    $pcs = \app\modules\shop\models\OrdersItems::find()->where('order_group_id = ' . $value->id)
                                        ->andWhere('status = 1')
                                        ->all();
                                    foreach ($pcs as $pc) {
                                        $discount += $pc->discount;
                                        $summSelfPurchaseFoods = $summSelfPurchaseFoods + (($pc->price - $pc->discount) * $pc->count);
                                    }
                                }
                            }
                            if(intval($value->type_id)==1005 || intval($value->type_id)==1008 || intval($value->type_id)==1009 || intval($value->type_id)==1010 || intval($value->type_id)==1012){

                                if(intval($value->delivery_id)==1003){ $delivery+=0;}
                                else if(intval($value->delivery_id)==1006){  $delivery+=150;}
                                else if(intval($value->delivery_id)==1007){  $delivery+=300;}

                                $pcs = \app\modules\shop\models\OrdersItems::find()->where('order_group_id = ' . $value->id)
                                    ->andWhere('status = 1')
                                    ->all();

                                foreach ($pcs as $pc) {
                                    $discount += $pc->discount;
                                    $summSelfPurchaseOther = $summSelfPurchaseOther + (($pc->price - $pc->discount) * $pc->count);
                                }
                            }
                        }

                        $result .= '<span class=text-success>'.Yii::t('admin', 'Еда и товары').' = '.\app\modules\common\models\ModFunctions::money($summSelfPurchaseFoods).'</span><br>';
                        $result .= '<span class=text-danger>'.Yii::t('admin', 'Спортивные товары').' = '.\app\modules\common\models\ModFunctions::money($summSelfPurchaseOther).'</span><br><br>';
                        $result .= '<span class=text-info>'.Yii::t('admin', 'Скидка').' = '. \app\modules\common\models\ModFunctions::money($discount).'</span><br>';
                        $result .= '<span class=text-info>'.Yii::t('admin', 'Доставка').' ~ '. \app\modules\common\models\ModFunctions::money($delivery).'</span><br>';
                    }else{
                    
                        if(!empty($_GET['CodesSearch']['wocode'])){

                        $purchase = \app\modules\shop\models\Orders::find()
                            ->where('code_id = ' . $code->id)
//                            ->where('user_id <> '.$code->user_id)
                            ->andWhere('date >= \''.date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart'])).'\'')
                            ->andWhere('date <= \''.date("Y-m-d 23:59:00", strtotime($_GET['CodesSearch']['dateStop'])).'\'')
                            ->andWhere('status = 1')
                            ->all();
                                    
                    //	var_dump($purchase); die();  
                            
                        }
                        else
                        {
		
                        $purchase = \app\modules\shop\models\Orders::find()
                            ->where('code_id = ' . $code->id)
//                            ->andWhere('user_id <> '.$code->user_id)
                            ->andWhere('date >= \''.date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart'])).'\'')
                            ->andWhere('date <= \''.date("Y-m-d 23:59:00", strtotime($_GET['CodesSearch']['dateStop'])).'\'')
                            ->andWhere('status = 1')
                            ->all();			
                         
			}
                        //                               \app\modules\common\models\Zloradnij::print_arr($purchase->createCommand()->getRawSql());die();
                        //   echo count($purchase)."<br>";

                        $summPurchaseFoods = 0;
                        $summPurchaseOther = 0;

                        foreach ($purchase as $key => $item) {
                            $groups = \app\modules\shop\models\OrdersGroups::find()->where('order_id = ' . $item->id)
                                ->andWhere('status = 1')
                                ->all();
                            //    echo "<pre>".$item->id."</pre><br>";
                            foreach ($groups as $value) {
                                //  echo "<pre>&nbsp;&nbsp;&nbsp;&nbsp;".$value->id."</pre><br>";
                                //  echo " [ T = ".intval($value->type_id)." ][ D = ".intval($value->delivery_id)." ] <br>";//echo $value->delivery_id."<br>";

                                if(intval($value->type_id)==1001 || intval($value->type_id)==1002 || intval($value->type_id)==1003 || intval($value->type_id)==1004 || intval($value->type_id)==1007 || intval($value->type_id)==1006) {

                                    if(intval($value->delivery_id)==1003){  $delivery+=150;}
                                    elseif(intval($value->delivery_id)==1006){ $delivery+=150;}
                                    elseif(intval($value->delivery_id)==1007){  $delivery+=300;}

                                    $pcs = \app\modules\shop\models\OrdersItems::find()->where('order_group_id = ' . $value->id)
                                        ->andWhere('status = 1')
                                        ->all();
                                    foreach ($pcs as $pc) {
                                        $discount += $pc->discount;
                                        $summPurchaseFoods = $summPurchaseFoods + (($pc->price - $pc->discount) * $pc->count);
                                        //  echo "<pre>&nbsp;&nbsp;&nbsp;&nbsp;".$pc->id." (".$pc->price.") (".$pc->discount.") (".$pc->count.") </pre><br>";
                                    }
                                }
                                elseif(intval($value->type_id)==1005 || intval($value->type_id)==1008 || intval($value->type_id)==1009 || intval($value->type_id)==1010  || intval($value->type_id)==1012){

                                    if(intval($value->delivery_id)==1003){ $delivery+=0;}
                                    else if(intval($value->delivery_id)==1006){  $delivery+=150;}
                                    else if(intval($value->delivery_id)==1007){  $delivery+=300;}

                                    $pcs = \app\modules\shop\models\OrdersItems::find()->where('order_group_id = ' . $value->id)
                                        ->andWhere('status = 1')
                                        ->all();
                                    foreach ($pcs as $pc) {
                                        $discount += $pc->discount;
                                        $summPurchaseOther = $summPurchaseOther + (($pc->price - $pc->discount) * $pc->count);
                                        //  echo "<pre>&nbsp;&nbsp;&nbsp;&nbsp;".$pc->id." (".$pc->price.") (".$pc->discount.") (".$pc->count.") </pre><br>";
                                    }
                                }
                                else{
                                    // echo " [ T = ".intval($value->type_id)." ][ D = ".intval($value->delivery_id)." ] <br>";
                                }
                            }
                        }
                        $result .=  '<span class=text-success>'.Yii::t('admin', 'Еда и товары').' = '.\app\modules\common\models\ModFunctions::money($summPurchaseFoods).'</span><br>';
                        $result .= '<span class=text-danger>'.Yii::t('admin', 'Спортивные товары').' = '. \app\modules\common\models\ModFunctions::money($summPurchaseOther).'</span><br><br>';
                        $result .= '<span class=text-info>'.Yii::t('admin', 'Скидка').' = '. \app\modules\common\models\ModFunctions::money($discount).'</span><br>';
                        $result .= '<span class=text-info>'.Yii::t('admin', 'Доставка').' ~ '. \app\modules\common\models\ModFunctions::money($delivery).'</span><br>';
                    }

                    return $result;
                },
                'mergeHeader'=>true,
                'format'=>'html',
            ],
            [

                'label' => Yii::t('admin', 'Сумма 2'),
                'attribute' => 'summa',
                'format' => 'html',
                'value' => function($model){

                    $code = \app\modules\catalog\models\Codes::find()->where('user_id = '.$model->user_id)->andWhere('status = 1')->one();

//                var_dump($_GET['CodesSearch']['dateStart']);

                    $wocode = \app\modules\shop\models\Orders::find()
                        ->select('orders.*, orders_groups.*, orders_items.*')
                        ->leftJoin('orders_groups','orders.id = orders_groups.order_id')
                        ->leftJoin('orders_items','orders_groups.id = orders_items.order_group_id')
                        ->where('user_id = '.$model->user_id)
                        ->andWhere('code_id is null')
                        ->andWhere('orders.status = 1')
                        ->andWhere('orders_groups.status = 1')
                        ->andWhere('orders_items.status = 1')
                        ->andWhere('date >= \''.date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart'])).'\'')
                        ->andWhere('date <= \''.date("Y-m-d 23:59:00", strtotime($_GET['CodesSearch']['dateStop'])).'\'')
                        ->asArray()
                        ->all();
                    //    \app\modules\common\models\Zloradnij::print_arr($wocode);die();

                    $c=0;
                    foreach($wocode as $item){
                        $c+=$item['price']*$item['count'];
                    }
                    //    var_dump($c);die();

                    if(!empty($_GET['CodesSearch']['wocode']))
                        $c = $model->summa + $c;
                    else
                        $c = $model->summa;

                    $result = '<b>Сумма всего = '.\app\modules\common\models\ModFunctions::money($c)."</b><br><br>";


                    $delivery = 0;
                    $discount = 0;



                    return $result;
                },
                'mergeHeader'=>true,
                'format'=>'html',


            ],
            [
                'label' => Yii::t('admin', 'Клуб'),
                'attribute' => 'userName',
                'value' => function($model){
                    if(!empty($model->user_id)){
                        $user = \app\modules\common\models\User::find()->where('id = '.$model->user_id)->one();
                        if(!empty($user->store_id)){
                            $club = \app\modules\managment\models\ShopsStores::find()->where('id = '.$user->store_id)->one();
                            if(!empty($club->name) && $club->name !=' ' && $club->name != '1')
                                return $club->name;
                            else
                                return '';
                        }
                    }
                    else
                        return '';
                },
                'vAlign'=>'middle',
                'mergeHeader'=>true,
                'format'=>'raw',
            ],
            [
                'label' => Yii::t('admin', 'Телефон владелеца'),
                'vAlign'=>'middle',
                'attribute' => 'user.phone',
                'mergeHeader'=>true,
                'format'=>'raw',
            ],
        ];

        echo GridView::widget([
            'id' => 'kv-grid-demo',
            'dataProvider'=>$dataProvider,
            'filterModel'=>$searchModel,
            'columns'=>$gridColumns,
            'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
            'headerRowOptions'=>['class'=>'kartik-sheet-style'],
            'filterRowOptions'=>['class'=>'kartik-sheet-style'],
            'pjax'=>true, // pjax is set to always true for this demo
            // set your toolbar
            'toolbar'=> [
                '{export}',
                '{toggleData}',
            ],
            // set export properties
            'export'=>[
                'fontAwesome'=>true
            ],
            // parameters from the demo form

            'panel'=>[
                'type'=>GridView::TYPE_DANGER,
                'heading'=>false,
            ],
            'persistResize'=>false,
            //'exportConfig'=>$exportConfig,
        ]);

    }

    if(!empty($trash)){
    ?>
    <br><h3><?= Yii::t('admin', 'Сотрудники с нулевыми показателями за период') ?></h3><br>
    <table cellpadding="0" cellspacing="0" width="100%" border="0" class="table table-striped">
        <tr>
            <td><b>#</b></td>
            <td><b>ID</b></td>
            <td><b><?= Yii::t('admin', 'Телефон') ?></b></td>
            <td><b><?= Yii::t('admin', 'ФИО') ?></b></td>
        </tr>
        <?php
            $counter = 0;
            foreach ($trash as $key => $item) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>";
                echo "<td>" . $item['id'] . "</td>";
                echo "<td>" . $item['phone'] . "</td>";
                echo "<td>" . $item['name'] . "</td>";
                echo "</tr>";
                $counter++;
            }
        }
        ?>
    </table>
</div>



