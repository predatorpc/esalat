<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\SignupForm */
$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class=reg-block id=reg-block>
        <br />
        <br />
        <br />
        <?php
        print Yii::$app->controller->renderPartial('login', [
        'model' => $model,
        ]);

