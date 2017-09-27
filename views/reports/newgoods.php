<?php
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$this->title = 'Отчет о добавленных товарах';


?>
<h2><?=$this->title;?></h2>
    <div class="filter-date">
        <div class="calendar-fast">
            <a class="dashed" href="<?=Url::to(['reports/new-goods', 'date_begin' => Date("d.m.Y"), 'date_end' => Date("d.m.Y")]);?>">Сегодня</a>|
            <a class="dashed" href="<?=Url::to(['reports/new-goods', 'date_begin' => Date('d.m.Y', strtotime('-1 day')), 'date_end' => Date('d.m.Y', strtotime('-1 day'))]);?>">Вчера</a>|
            <a class="dashed" href="<?=Url::to(['reports/new-goods', 'date_begin' => Date('d.m.Y', strtotime('-2 day')), 'date_end' => Date('d.m.Y', strtotime('-2 day'))]);?>">Позавчера</a>|
            <a class="dashed" href="<?=Url::to(['reports/new-goods', 'date_begin' => Date('d.m.Y', strtotime('-1 week')), 'date_end' => Date('d.m.Y')]);?>">Прош. неделя</a>|
            <a class="dashed" href="<?=Url::to(['reports/new-goods', 'date_begin' => Date('d.m.Y', strtotime('-1 month')), 'date_end' => Date("d.m.Y")]);?>">Прош. месяц</a>
        </div>
    </div>

<?php if(count($data)>0){
    echo '<h3>Период с '.date("d.m.Y",strtotime($filter['date_begin'])).' по '.date("d.m.Y",strtotime($filter['date_end'])).'</h3>';
    echo '<div class="panel-group" id="accordion">';
// РАСКЛАДКА ПО ДАТАМ
//    foreach ($data as $date => $categories) {
//        echo '<div class="panel panel-default">';
//            echo '<div class="panel-heading">';
//                echo '<h4 class="panel-title">';
//                    echo '<a data-toggle="collapse" data-parent="#accordion" href="#'.$date.'">';
//                        echo $date;
//                    echo '</a>';
//                echo  '</h4>';
//            echo '</div>';
//            echo '<div id="'.$date.'" class="panel-collapse collapse">';
//                echo '<div class="panel-body">';
//
//                    echo '<div class="panel-group" id="accordion'.$date.'">';
//                        $n = 0;
//                        foreach ($categories as $category => $goods){
//                            echo '<div class="panel panel-default">';
//                                echo '<div class="panel-heading">';
//                                    echo '<h4 class="panel-title">';
//                                        echo '<a data-toggle="collapse" data-parent="#accordion'.$date.'" href="#'.$n.'_'.$date.'">';
//                                            echo $category.': '.count($goods);
//                                        echo '</a>';
//                                    echo  '</h4>';
//                                echo '</div>';
//                                echo '<div id="'.$n.'_'.$date.'" class="panel-collapse collapse">';
//                                    echo '<div class="panel-body">';
//                                        foreach ($goods as $id => $good){
//                                            echo $good['name'].'<br>';
//                                        }
//                                    echo '</div>';
//                                echo '</div>';
//                            echo '</div>';
//                            $n++;
//                        }
//                    echo '</div>';
//
//                echo '</div>';
//            echo '</div>';
//    }
        $n = 0;
        foreach ($data as $category => $goods) {
            echo '<div class="panel panel-default">';
                echo '<div class="panel-heading">';
                    echo '<h4 class="panel-title">';
                        echo '<a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$n.'">';
                            echo $category.' ('.count($goods).' товаров)';
                        echo '</a>';
                    echo  '</h4>';
                echo '</div>';
                echo '<div id="collapse'.$n.'" class="panel-collapse collapse">';
                    echo '<div class="panel-body">';
                        foreach ($goods as $good_id => $good){
                            echo '<pre>'.$good['name'].' ('.$good['variations'].' вариации)<a href="'.Url::to(['product/update', 'id' => $good_id]).'">Редактировать товар</a></pre>';
                        }
                    echo '</div>';
                echo '</div>';

            echo '</div>';
            $n++;
        }
    echo '</div>';
    echo '<h3>Итого</h3>';
    echo '<h3>Новых товаров: '.$totalGoods.'<h3>';
    echo '<h3>Новых вариаций: '.$totalVariations.'</h3>';
}?>
