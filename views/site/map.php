<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = Yii::t('app','Карта пунктов выдачи заказов');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="content" style="height: 380px">
        <div id="poligon" style="display: block; width: 100%; position: static"></div>
    </div>
    <div class="text_min">
        <p><span style="background: #84DD7B; padding: 0px 10px; margin: 0px 5px 0px 0px;"></span><?=\Yii::t('app','Стоимость доставки')?> 250 рублей.</p>
        <p><span style="background: #FFC477; padding: 0px 10px; margin: 0px 5px 0px 0px;"></span><?=\Yii::t('app','Стоимость доставки')?> 350 рублей.</p>
    </div>
</div>
