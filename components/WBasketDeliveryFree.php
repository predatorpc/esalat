<?php
namespace app\components;

use yii\base\Widget;
use yii\bootstrap\Progress;
use app\modules\common\models\ModFunctions;
use app\modules\actions\models\ActionsPresentSave;

class WBasketDeliveryFree extends Widget{
    const PRESENT_KEY = 1;

    public function run(){
/*
        $deltaMobey = \Yii::$app->basket->priceGroupsAll(self::PRESENT_KEY);
        //$deltaMobey = \Yii::$app->basket->priceGroups;
        if($deltaMobey <=  \Yii::$app->params['presentAll'][self::PRESENT_KEY]['basketPriceMin']) {
            //$textDeliveryFree = '<span class="text-min"> До бесплатной доставки осталось  '.(3000 - $deltaMobey).' р.</span>';
            $textDeliveryFree = '<span class="text-min"> До абонемента Platinum на 1 месяц за 1000 р. осталось  '.( \Yii::$app->params['presentAll'][self::PRESENT_KEY]['basketPriceMin'] - $deltaMobey).' р.</span>';
        }
        else{
            //$textDeliveryFree = '<span class="text-min">Поздравляем, теперь у Вас бесплатная доставка!</span>';
            $textDeliveryFree = '<span class="text-min" id="'.(empty($_SESSION['ModalStock']) ? 'stockModal' : '').'">Поздравляем, теперь можно получить абонемент Platinum на 1 месяц за 1000 р!</span>';
        }
        $basket = \Yii::$app->basket->basket;
        $show=false;
        $presentGet = ActionsPresentSave::find()->where([
            'user_id'=>\Yii::$app->user->id,
            'status'=>1,
            'present'=>\Yii::$app->params['presentAll'][self::PRESENT_KEY]['present']])
            ->andWhere(['like', 'update_date', Date('Y-m-', time())])
            ->andWhere('card_number IS NOT NULL')
            ->one();
        if(empty($presentGet)){
            $presentSearch = ActionsPresentSave::find()->where(['user_id'=>\Yii::$app->user->id, 'basket_id'=>$basket->id, 'status'=>1, 'present'=>\Yii::$app->params['presentAll'][self::PRESENT_KEY]['present']])->one();
            if( ($deltaMobey >  0 && empty($presentSearch)) ){//показывать
                $show=true;
            }
            elseif($deltaMobey > 0 && !empty($presentSearch) && \Yii::$app->basket->presentInBasketAll(self::PRESENT_KEY)){
                $show=true;
            }
        }

        if($show){
            echo Progress::widget([
                'percent' =>  ModFunctions::moneyFloat(($deltaMobey <=  \Yii::$app->params['presentAll'][self::PRESENT_KEY]['basketPriceMin']) ? $deltaMobey * 100 /  \Yii::$app->params['presentAll'][self::PRESENT_KEY]['basketPriceMin'] : 100),
                'label'   => $textDeliveryFree,
                'barOptions' => [
                    'class' => 'progress-bar-success'
                ],
                'options' => [
                    'class' => 'active progress-striped',
                    'style'=> 'margin-bottom:0px; position:relative',
                ]
            ]);
        }*/
    }
}