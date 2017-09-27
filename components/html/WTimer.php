<?php
namespace app\components\html;
use Yii;
use yii\base\Widget;

class WTimer extends Widget
{

    public function init()
    {
        parent::init();
    }
    public function run()
    {
                // Обработка времени;
                $time_start = (!empty($_SESSION['time_start']) ? $_SESSION['time_start'] : false);
                // интервал секнунд 90 сек;
                $interval = \Yii::$app->params['intervalTimer'];

                // Расчет время интервала;
                if (!empty($time_start) && $time_start < (time() - $interval)) {
                    // Тут что то выполняем;
                    unset($_SESSION['time_start']);
                }
                // Скрипт обратный отчет;
                $time_set = ($interval - (time() - $time_start));
                $time_set = $time_set > 0 ? $time_set : 0;
                 //print_r($_SESSION['time_start']);
            ?>
            <div class="time-content" id="timer__w">
                <?php if(empty($_SESSION['time_start'])): ?>
                    <div id ="star_time"></div>
                <?php else: ?>
                    <div id ="begin_time" data-interval="<?=$interval?>"  data-time="<?=$_SESSION['time_start']?>"></div>
                    <div id ="test_time" class="hidden"><?=$time_set?></div>
                <?php endif; ?>
            </div>
   <?php }
}
?>