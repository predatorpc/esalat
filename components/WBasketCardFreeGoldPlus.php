<?php
namespace app\components;

use yii\base\Widget;
use yii\bootstrap\Progress;
use app\modules\common\models\ModFunctions;

class WBasketCardFreeGoldPlus extends Widget{


    public function run()
    {
        $textDeliveryFree = '<span class="text-min">Gold plus</span>';
        if (false) {
            echo Progress::widget([
                'percent' => '100',
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

}