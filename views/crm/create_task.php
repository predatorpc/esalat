<?php

use yii\helpers\Html;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $model app\modules\crm\models\CrmTasks */

$this->title = 'Новая задача';
$this->params['breadcrumbs'][] = ['label' => 'Управление задачами', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


    <?= $this->render('_form_task', [
        'model' => $model,
        'user' => $user,
        'search_output' => !empty($search_output) ? $search_output : null,
    ]) ?>


