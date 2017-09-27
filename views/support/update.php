<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Messages */

$this->title = Yii::t('admin', 'Редактирование сообщений').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Управление магазином'), 'url' => ['shop-management'],'template' => "/ {link} /\n",];
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Техподдержка'), 'url' => [' '],'template' => "{link} /\n",];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id],'template' => "{link} /\n",];
$this->params['breadcrumbs'][] =  ['label' => Yii::t('admin', 'Обновить'),'template' => "{link}\n",];
?>
<div class="messages-update">
    <!--Хлебная крошка-->
      <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [], ]);?>
   <!--Хлебная крошка-->
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ])
    ?>


</div>
