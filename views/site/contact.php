<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
?>
<script>


</script>

<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php
     // Обработка времени;
     $time_start = (!empty($_SESSION['time_start']) ? $_SESSION['time_start'] : false);
     // интервал секнунд 30 сек;
     $interval = 30;
     $flag = false;
    // Расчет время интервала;
    if (!empty($time_start) && $time_start < (time() - $interval)) {
        // Тут что то выполняем;
        $flag = true;
        unset($_SESSION['time_start']);
        //print_arr('STOP');
        //die('STOP');
    }
    // Скрипт обратный отчет;
    $time_set = ($interval - (time() - $time_start));
    $time_set = $time_set > 0 ? $time_set : 0;
   // print_arr($time_set);
    //Записываем сек;
    if(Yii::$app->request->post('time_start')) {;
        $_SESSION['time_start'] = time();
    }
    ?>

    <div class="time-content">
        <?php if(empty($_SESSION['time_start'])): ?>
            <div id ="star_time"></div>
        <?php else: ?>
            <div id ="begin_time" data-interval="<?=$interval?>"  data-time="<?=$_SESSION['time_start']?>"></div>
            <div id ="test_time"><?=$time_set?></div>
        <?php endif; ?>
    </div>




</div>
