<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Отчет о доставке');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1><br><br><br><br>


    <div id="cms-reports">
        <div class="filter">
            <form action="" method="post">


                <div class="filter-date">

                    <select name="date" class="type" disabled>
                        <option value="2"><?= Yii::t('admin', 'Получение') ?></option>
                    </select>
                    <div class="label">с</div>
                    <div class="calendar">
                        <input type="hidden" name="date_begin" value="<?php if(!empty($filter['date_begin'])) echo $filter['date_begin']; else echo strtotime(date('d.m.Y')); ?>" class="value" />
                        <div class="value"><?php if(!empty($filter['date_begin'])) echo date("d.m.Y",$filter['date_begin']); else echo date('d.m.Y'); ?> </div>

                        <div class="date-button"></div>
                        <div class="table"></div>
                    </div>
                    <div class="label">по</div>
                    <div class="calendar">
                        <input type="hidden" name="date_end" value="<?php if(!empty($filter['date_end'])) echo $filter['date_end']; else echo strtotime(date('d.m.Y'));?>" class="value" />
                        <div class="value"><?php if(!empty($filter['date_end'])) echo date("d.m.Y",$filter['date_end']); else echo date('d.m.Y'); ?></div>
                        <div class="date-button"></div>
                        <div class="table"></div>
                    </div>
                </div>

                <div class="filter-order-id">
                    <div class="label"><?= Yii::t('admin', 'номер заказа') ?></div>
                    <input type="text" name="order_id" value="<?php if(!empty($filter['order_id'])) echo $filter['order_id']; ?>" maxlength="8" class="number" />
                </div>
                <div class="filter-status">
                    <div class="label"><?= Yii::t('admin', 'статус заказа') ?></div>
                    <select name="status_id" class="status" disabled>
                        <option value=""><?= Yii::t('admin', 'Все') ?></option>
                    </select>
                </div>

                <div class="filter-users auto-search">
                    <input type="text" maxlength="64" search="users" class="search" />
                    <div class="auto-search-values">
						<?php

						if(!empty($filter['users']))	
							foreach($filter['users'] as $i => $item){
		                        echo "<span class=item item-".$i;
	                         echo "onclick=search_items_delete('users', '".$i."')"; 
                             echo ">".$item."</span>";
							}
								
						?>

                    </div>
                    <div class="auto-search-label"><?= Yii::t('admin', 'Покупатель') ?></div>
                    <div class="auto-search-load"></div>
                    <div class="auto-search-items"></div>
                </div>
                <div class="filter-drivers auto-search">
                    <input type="text" maxlength="64" search="drivers" class="search" />
                    <div class="auto-search-values">
                       <?php
						if(!empty($filter['drivers']))	
							foreach($filter['users'] as $i => $item){

		                        echo "<span class=item item-".$i;
	                         echo "onclick=search_items_delete('drivers', '".$i."')"; 
                             echo ">".$item."</span>";
							}
							
						?>
                    </div>
                    <div class="auto-search-label"><?= Yii::t('admin', 'Водитель') ?></div>
                    <div class="auto-search-all"></div>
                    <div class="auto-search-load"></div>
                    <div class="auto-search-items"></div>
                </div>
                <div class="filter-load"></div>
                <div class="filter-button" onclick="return orders_delivery();"><?= Yii::t('admin', 'Сформировать') ?></div>
            </form>
        </div>
        <div class="info"><?= Yii::t('admin', 'Для загрузки данных нажмите кнопку «Сформировать»') ?></div>
        <div class="items"></div>
    </div>
    <br />
    <!--<script type="text/javascript" charset="utf-8" src="https://api-maps.yandex.ru/services/constructor/1.0/js/?sid=nwVJpjbkrKd21BfXCze4jYg4pM0suSyE&width=1200&height=600&lang=ru_RU&sourceType=constructor"></script>-->

