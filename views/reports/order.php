<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\modules\shop\models\Orders;
Use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчет о продажах';
$this->params['breadcrumbs'][] = $this->title;
$arCategory = \app\modules\catalog\models\Category::find()->where(['active'=>1])->asArray()->orderBy('parent_id')->All();
$map = array();
$arrayHelper = ArrayHelper::map($arCategory, 'id', 'parent_id');
foreach ($arrayHelper as $id => $id_parent){
    if(empty($id_parent)){
        $map[$id] = array();
    }elseif (isset($map[$id_parent])){
        $map[$id_parent][$id] = array();
    }elseif(isset($arrayHelper[$id_parent])){
        $map[$arrayHelper[$id_parent]][$id_parent][] = $id;
    }
}
$temp = ArrayHelper::map($arCategory, 'id', 'title');
$arCategory = array();
foreach ($map as $key => $value){
    if(!isset($temp[$key])){
        continue;
    }
    $arCategory[$key] = $temp[$key];
    foreach ($value as $key1 => $value1){
        $arCategory[$key1] = '->'.$temp[$key1];
        foreach ($value1 as $key2 => $value2){
            $arCategory[$value2] = '-->'.$temp[$value2];
        }
    }
}

// Модальное окно;
Modal::begin([
    'header' => '<h3>Есть претензия!</h3>',
    'id'=>'orders',
    'size' => Modal::SIZE_SMALL,
]);
if(isset($_POST['modal_order'])) {
    $order_id = $_POST['order_id'];
    $order = Orders::findOne(intval($order_id));
    Html::beginForm(['/reports/order'], 'post', ['class' => 'modal-form-orders']);
    echo '<div class="alert alert-danger hidden_r"></div>';
    echo '<input class="order-id" type="hidden" value="'.$order_id.'" name="order_id">';
    //comments_call_center
    echo '<div class="form-group">
               <b>Комментарий</b>
               <textarea class="form-control comments" name="comments" rows="3" style="width: 100%; margin:3px 0px 3px 0px">'.($order->comments_call_center ? $order->comments_call_center : '' ).'</textarea>
             </div>';
    echo '<div class="form-group"><input class="negative_status" '.($order->negative_review ? 'checked':'').' type="checkbox" name="negative_status"><b style="position: relative;top: -2px;left: 5px;">Вкл./выкл. претензия</b></div>';
    echo '<div class="form-group"><button data-loading-text="Загрузка..." onclick="addNegative();" type="button" class="btn btn-info" style="float: right;">Сохранить</button></div>';
    echo '<div class="clear"></div>';
    Html::endForm();
}
Modal::end();

?>
  
<h1><?= Html::encode($this->title) ?></h1><br><br><br><br>
	<!-- script type="text/javascript" src="/js/jquery.calendar.js"></script -->
    <div id="cms-reports">
        <?php if(\Yii::$app->user->can('callcenterOperator') || \Yii::$app->user->can('categoryManager') || \Yii::$app->user->can('GodMode')): ?>
          <div id="can"></div>
        <?php endif ?>
        <div class="filter">
            <form action="" method="post" id = "filterform">
				<input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">

                <div class="filter-store">
                    <div class="label">Локация</div>
                    <select name="store_id">
                        <option value="">Все</option>
                        <?php

                        //                       if(!empty($stores))
                        foreach($stores as $item){
                            echo '<option value='.$item['id'];
                            if(!empty($filter['store_id']))
                                if($filter['store_id'] == $item['id']) echo " selected ";
                            echo ">".$item['name']." </option>";
                        }

                        ?>
                    </select>
                </div>
                 <div class="filter-date">
                    <select name="date" class="type">
                        <option value="1" <?php if(!empty($filter['date'])) if($filter['date'] == 1) echo " selected "; ?>  >Оформление</option>
                        <option value="2" <?php if(!empty($filter['date'])) if($filter['date'] == 2) echo " selected "; ?> >Получение</option>
                    </select>
                     <div class="calendar-fast">
                         <a href="#" class="dashed" onclick="setDateRange('<?=time()?>', '<?=time()?>','<?=Date('d.m.Y')?>','<?=Date('d.m.Y')?>')">Сегодня</a>
                         <a href="#" class="dashed" onclick="setDateRange('<?=strtotime('-1 day')?>', '<?=strtotime('-1 day')?>', '<?=Date('d.m.Y', strtotime('-1 day'))?>','<?=Date('d.m.Y', strtotime('-1 day'))?>')">Вчера</a>
                         <a href="#" class="dashed" onclick="setDateRange('<?=strtotime('-2 day')?>', '<?=strtotime('-2 day')?>', '<?=Date('d.m.Y', strtotime('-2 day'))?>','<?=Date('d.m.Y', strtotime('-2 day'))?>')">Позавчера</a>
                         <a href="#" class="dashed" onclick="setDateRange('<?=strtotime('-1 week')?>', '<?=time()?>', '<?=Date('d.m.Y', strtotime('-1 week'))?>','<?=Date('d.m.Y')?>')">Прош. неделя</a>
                         <a href="#" class="dashed" onclick="setDateRange('<?=strtotime('-1 month')?>', '<?=time()?>', '<?=Date('d.m.Y', strtotime('-1 month'))?>','<?=Date('d.m.Y')?>')">Месяц</a>
                     </div>
                    <div class="calendar-content right-margin">
                         <div class="label">с</div>
                         <div class="calendar">
                            <input type="hidden" id="date_begin" name="date_begin" value="<?php if(!empty($filter['date_begin'])) echo $filter['date_begin']; else echo strtotime(date('Y-m-d')); ?>" class="value" />
                            <div class="value"><?php if(!empty($filter['date_begin'])) echo date("d.m.Y",$filter['date_begin']); else echo date('d.m.Y'); ?> </div>
                            <div class="date-button"></div>
                            <div class="table"></div>
                        </div>
                    </div>
                    <div class="calendar-content">
                         <div class="label">по</div>
                         <div class="calendar">
                            <input type="hidden" id="date_end" name="date_end" value="<?php if(!empty($filter['date_end'])) echo $filter['date_end']; else echo strtotime(date('Y-m-d'));?>" class="value" />
                            <div class="value"><?php if(!empty($filter['date_end'])) echo date("d.m.Y",$filter['date_end']); else echo date('d.m.Y'); ?></div>
                            <div class="date-button"></div>
                            <div class="table"></div>
                        </div>
                    </div>
                     <div class="clear"></div>
                </div>
                <div class="clear mobile"></div>
                <div class="filter-order-id">
                    <div class="label hidden">номер заказа</div>
                    <input type="text" name="order_id" placeholder="номер заказа"  value="<?php if(!empty($filter['order_id'])) echo $filter['order_id']?>" maxlength="8" class="number" />
                </div>
                <div class="filter-status">
                      <div class="label hidden">статус заказа</div>
                    <select name="status_id" class="status">
                        <option value="">статус заказа</option>
                        <option value="NULL" <?php if(!empty($filter['status_id'])) if($filter['status_id']=="NULL") echo " selected "; ?> style="color: #cc0000;">Не обработан</option>
                        <option value="NO" <?php if(!empty($filter['status_id'])) if($filter['status_id']=="NO") echo " selected "; ?> style="color: #cc0000;">Не выдан</option>
                        <option value="-1" <?php if(!empty($filter['status_id'])) if($filter['status_id']=="-1") echo " selected "; ?> style="color: #cc0000;">Отменен</option>
						<?php

					if(!empty($status)) 
							foreach ($status as $item){
    	                            echo "<option value=".$item['id'];
                                if(!empty($filter['status_id']))
									  if($filter['status_id'] == $item['id'])
											echo " selected "; 
								echo ">".$item['name']."</option>";
							}
						?>

                    </select>
                </div>
                <div class="content-filter-auto-search">
                    <div class="filter-shops auto-search">
                        <div class="group group-1">
                            <input id="filter-group-shops" type="checkbox" name="not_our_shops" value="1" <?php if(!empty($filter['not_our_shops'])) echo " checked "; ?> class="check" />
                            <label for="filter-group-shops">Не наш товар</label>
                            <input id="filter-group" type="checkbox" name="not_free_delivery" value="1" <?php if(!empty($filter['not_free_delivery'])) echo " checked "; ?> class="check" />
                            <label for="filter-group">Платная доставка</label>
                        </div>
                        <div class="group group-2">
                            <input id="filter-group-shops" type="checkbox" name="our_shops" value="1" <?php if(!empty($filter['our_shops'])) echo " checked "; ?> class="check" />
                            <label for="filter-group-shops">Наш товар</label>
                            <input id="filter-group" type="checkbox" name="group" value="1" <?php if(!empty($filter['group'])) echo " checked "; ?> class="check" />
                            <label for="filter-group">Группировать</label>
                        </div>

                        <input type="text" maxlength="64" search="shops" class="search" />
                        <div class="auto-search-values">

                        <?php
                    if(!empty($filter['shops']))
                            foreach($filter['shops'] as $i => $item)
                                echo "<span class=item item-".$i." onclick=search_items_delete('shops', '".$i."');>".$item."</span>";
                        ?>

                        </div>
                        <div class="auto-search-label">Поставщик</div>
                        <div class="auto-search-all"></div>
                        <div class="auto-search-load"></div>
                        <div class="auto-search-items"></div>

                        <div class="input-form"><input type="text" maxlength="128" name="good_id" value="" placeholder="id товара" class="good-id" /></div>
                    </div>

                    <div class="filter-users auto-search">
                        <div class="group">
                            <input id="filter-group-1" type="radio" name="user_type" value="1" <?php if(!empty($filter['user_type'])) if ($filter['user_type'] == 1) echo " checked "; ?> class="check" />
                            <label for="filter-group-1">Клиенты</label>
                            <input id="filter-group-2" type="radio" name="user_type" value="2"  <?php if(!empty($filter['user_type'])) if ($filter['user_type'] == 2) echo " checked "; ?>  class="check" />
                            <label for="filter-group-2">Сотрудники</label>
                            <input id="filter-group-0" type="radio" name="user_type" value="0" checked <?php //if(!empty($filter['user_type'])) if ($filter['user_type'] == 0) echo " checked "; ?>  class="check" />
                            <label for="filter-group-0">Все</label>
                        </div>

                        <input type="text" maxlength="64" search="users" class="search" />
                        <div class="auto-search-values">



                        <?php
                        if(!empty($filter['users']))
                              foreach($filter['users'] as $i => $item)
                                echo "<span class=item item-".$i." onclick=search_items_delete('users', '".$i."');>".$item."</span>";
                            ?>


                        </div>
                        <div class="auto-search-label">Покупатель</div>
                        <div class="auto-search-load"></div>
                        <div class="auto-search-items"></div>
                    </div>
                </div>
                <div class="filter-codes no-promo basket_sort">
                    <input class="no-promo" type="checkbox"   name="basket_sort" value="1"  />
                    <label>Сортировка корзина</label>
                </div>
                <div class="filter-codes no-promo">
                    <input class="no-promo" type="checkbox"   name="no_promo" value="1"  />
                    <label>Без промо-кода</label>
                </div>
                <div class="filter-codes auto-search">
                    <input type="text" maxlength="64" search="codes" class="search" />
                    <div class="auto-search-values">

					<?php
				if(!empty($filter['codes'])) 
					      foreach($filter['codes'] as $i => $item)				      
							echo "<span class=item item-".$i." onclick=search_items_delete('codes', '".$i."');>".$item."</span>";
						?>


                    </div>
                    <div class="auto-search-label">Промо-код</div>
                    <div class="auto-search-load"></div>
                    <div class="auto-search-items"></div>
                </div>
          <!---Нов-->
           <div class="content-filter-from">
                <div class="filter-default">
                        <select name="delivery_store_id">

                            <option value="">Доставка в клуб (все)</option>
                            <?php

                            //                       if(!empty($stores))
                            foreach($stores as $item){
                                echo '<option value='.$item['id'];
                                if(!empty($filter['store_id']))
                                    if($filter['store_id'] == $item['id']) echo " selected ";
                                echo ">".$item['name']." </option>";
                            }

                            ?>
                        </select>
                    </div><!---Нов.-->
                <div class="filter-type_id">
                    <select name="type_id">
                        <option value="">Все типы</option>

                        <?php
                    if(!empty($types))
                             foreach($types as $key=>$item){
                               if($item['id'] == 1007 || $item['id'] == 1003 || $item['id'] == 1005 || $item['id'] == 1010 || $item['id'] == 1011 || $item['id'] == 1012) {
                                   echo "<option value=" . $item['id'];
                                   if (!empty($filter['type_id']))
                                       if ($filter['type_id'] == $item['id']) echo " selected ";
                                   echo ">" . $item['name']  . "</option>";
                               }
                            }
                        ?>

                    </select>
                </div>

                <div class="filter-type hidden">
                    <select name="type">
                        <option value="1" <?php if(!empty($filter['type'])) if($filter['type'] == 1) echo " selected "; ?> >Сайт</option>
                        <option value="2" <?php if(!empty($filter['type'])) if($filter['type'] == 2) echo " selected "; ?> >Терминалы</option>
                    </select>
                </div>
                <div class="filter-delivery hidden">
                    <select name="delivery_id">
                        <option value="">Все доставки</option>

                        <?php
                        if(!empty($deliveries))
                            foreach($deliveries as $item){
                                echo "<option value=".$item['id'];
                                if(!empty($filter['deliveries']))
                                    if($filter['deliveries'] == $item['id']) echo " selected ";
                                echo ">".$item['name']."</option>";
                            }
                        ?>

                    </select>
                </div>
               <!--Категория-->
               <div class="filter-type">
                    <select name="category_all">
                        <option value="">Все категории</option>
                        <?php foreach ($arCategory as $id => $name){
                            echo '<option value="'.$id.'">'.$name.'</option>';
                        }
                        ?>
                    </select>
                </div>  <!--Категория-->


           </div>

                <!-- div class="filter-delivery-club">
                    <select name="delivery_store_id">
                        <option value="">Все</option>

                        <?php
                        if(!empty($deliveries))
                            foreach($deliveries as $item){
                                echo "<option value=".$item['id'];
                                if(!empty($filter['deliveries']))
                                    if($filter['deliveries'] == $item['id']) echo " selected ";
                                echo ">".$item['name']."</option>";
                            }
                        ?>

                    </select>
                </div -->

                <div class="filter-load"></div>
                <div class="filter-button" onclick="return orders_items();">Сформировать</div>
            </form>

        </div>
        <div class="info">Для загрузки данных нажмите кнопку «Сформировать»</div>
        <div class="items"></div>
    </div>



