<?php
namespace app\components;

use yii\base\Widget;
use yii\bootstrap\Progress;
use app\modules\common\models\ModFunctions;
use app\modules\catalog\models\Goods;
use app\modules\actions\models\ActionsPresentSave;

class WBasketCardFree extends Widget{
    const PRESENT_KEY = 0;

    public function run(){

        // Ключ товара акция;
        $present_key = 0;
        $_present_key = 0;
        $textDeliveryFree = '';

        // Параметры
        $presentAll =  \Yii::$app->params['presentAll'];
        $flag = false;

        // Загрузка из параметры пошаговая;
        foreach($presentAll as $key=>$presents) {
            // Определям по типу;
            if(!empty($presents['type'])&& $presents['type'] == 1) {
                $_deltaMobey = \Yii::$app->basket->priceGroupsAll($key);
                $progress = ModFunctions::moneyFloat(($_deltaMobey <= $presents['basketPriceMin']) ? $_deltaMobey * 100 / $presents['basketPriceMin'] : 100);
                //
                if($_deltaMobey <= $presents['basketPriceMin']) {
                    $present_key = $key;
                    $flag = true;
                }else{
                    $_present_key = $key;
                    $presentAllSuccess=true;
                }
                if ($flag) break;
            }
        }
        // Название товара;
        $goodsVariation = Goods::getProductVariantsId($presentAll[$present_key]['present']);

        $deltaMobey = \Yii::$app->basket->priceGroupsAll($present_key);

        if($deltaMobey <= $presentAll[$present_key]['basketPriceMin']) {
            $textDeliveryFree = '<span class="text-min"> До абонемента '.$goodsVariation['full_name'].' за '.ModFunctions::moneyFloat($goodsVariation['price']).' р. осталось  '.($presentAll[$present_key]['basketPriceMin'] - $deltaMobey).' р. </span>';
        }else{
            if(empty($presentAll[$present_key]['type']))
                $textDeliveryFree = '<span class="text-min" id="'.(empty($_SESSION['ModalStock']) ? 'stockModal' : '').'">Поздравляем, теперь можно получить абонемент '.$goodsVariation['full_name'].' за '.ModFunctions::moneyFloat($goodsVariation['price']).' р. </span>';
        }

        $basket = \Yii::$app->basket->basket;
        $show=false;

        $presentGet = ActionsPresentSave::find()->where([
            'user_id'=>\Yii::$app->user->id,
            'status'=>1,
            'present'=>$presentAll[$present_key]['present']])
            ->andWhere(['like', 'update_date', Date('Y-m-', time())])
            ->andWhere('card_number IS NOT NULL')
            ->one();
        if(empty($presentGet)){
            $presentSearch = ActionsPresentSave::find()->where(['user_id'=>\Yii::$app->user->id, 'basket_id'=>$basket->id, 'status'=>1, 'present'=>$presentAll[$present_key]['present']])->one();
            if(empty($presentSearch) ){//показывать
                $show=true;
            }
            elseif(!empty($presentSearch) && \Yii::$app->basket->presentInBasketAll($present_key)){
                $show=true;
            }
        }

        if($show) {
            // Для тип - 1 абонемент;
            if( !empty($presentAllSuccess)) {
                $_goodsVariation = Goods::getProductVariantsId($presentAll[$_present_key]['present']);
                echo Progress::widget([
                    'percent' => 100,
                    'label' => '<span class="text-min" id="____stockModal">Поздравляем, теперь можно получить абонемент '.$_goodsVariation['full_name'].' за '.ModFunctions::moneyFloat($_goodsVariation['price']).' р. </span>',
                    'barOptions' => [
                        'class' => 'progress-bar-warning'
                    ],
                    'options' => [
                        'class' => 'active progress-striped',
                        'style'=> 'position:relative',
                    ]
                ]);
            }
            if(!empty($textDeliveryFree)) {
                echo Progress::widget([
                    'percent' => ModFunctions::moneyFloat(($deltaMobey <= $presentAll[$present_key]) ? $deltaMobey * 100 / $presentAll[$present_key]['basketPriceMin'] : 100),
                    'label' => $textDeliveryFree,
                    'barOptions' => [
                        'class' => 'progress-bar-warning'
                    ],
                    'options' => [
                        'class' => 'active progress-striped',
                        'style' => 'position:relative',
                    ]
                ]);
            }
        }
        ?>

    <?php  }
}