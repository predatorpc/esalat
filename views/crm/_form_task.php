<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\slider\Slider;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\modules\crm\models\CrmTasks */
/* @var $form yii\widgets\ActiveForm */
$arPriority = [0 => 'Низкий', 1 => 'Средний ',2 => 'Высокий'];
?>

<div class="crm-tasks-create">

    <h1><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Список задач', ['/crm/'], ['class' => 'btn btn-success']) ?>
</p>
<?php
        //////////////////////////////////////////////////////////////////////////////////////
        // MODAL Для поиска пользователей
        //////////////////////////////////////////////////////////////////////////////////////
        \yii\bootstrap\Modal::begin([
            'header' => '<h3><b>Найти пользователя:</b></h3>',
            'id' => 'search_user',
            'toggleButton' => [
                'tag' => 'button',
                'class' => 'btn btn-warning',
                'label' => 'Найти пользователя'
            ]
        ]);
        echo '<b>Введите Номер телефона без +7</b><br>';
        //////////////////////////////////////////////////////////////////////////////////////
        // Оборачиваем все в ПЪЯКС
        //////////////////////////////////////////////////////////////////////////////////////
        \yii\widgets\Pjax::begin();
        //////////////////////////////////////////////////////////////////////////////////////
        // Форма
        //////////////////////////////////////////////////////////////////////////////////////
        echo Html::beginForm(['crm/createtask'],
            'get',
            ['data-pjax' => true,
                'enableAjaxValidation' => true,
                'class' => 'form-inline'
            ]);
        echo Html::hiddenInput('id', $model->id);
        echo Html::input('text', 'search', Yii::$app->request->post('string'),
            [
                'class' => 'form-control',
                'minlength' => 2,
                'maxlength' => 10
            ]);
        echo Html::submitButton('Найти', ['class' => 'btn btn-primary', 'name' => 'hash-button']);
        echo Html::endForm();
        //////////////////////////////////////////////////////////////////////////////////////
        // Конец формы
        //////////////////////////////////////////////////////////////////////////////////////
        echo "<br>";
//print_r($search_output);
        if(!empty($search_output)) {
            foreach ($search_output as $item) {
                echo "<a href='#' id='".$item->id."' class='form_user'>"
                    .$item->phone . " "
                    . $item->name."</a><br>";
            }?>
            <script>
                $('.form_user').click(function(){
                    var id = $(this).attr('id');
                    var name = $(this).text();
                    $('#search_user').modal('hide');
                    $('#user_name').append($('<option selected></option>').val(id).html(name));
                    $('#id_changed').val(1);

                });
            </script>
        <?php }
        \yii\widgets\Pjax::end();
        \yii\bootstrap\Modal::end();
        //////////////////////////////////////////////////////////////////////////////////////
        // Конец модала и пъякса
        //////////////////////////////////////////////////////////////////////////////////////

?>
</div>
<div class="crm-tasks-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php $form = ActiveForm::begin(['validateOnSubmit' => false, ]);
    echo Html::hiddenInput('id_changed', 1, ['id' => 'id_changed']);
    if(!empty($user))
        echo $form->field($model, 'slave')->DropDownList(ArrayHelper::map($user,'id','name'),
            ['readonly' => true, 'id' => 'user_name']);
    else
        echo $form->field($model, 'slave')->DropDownList(ArrayHelper::map($user,'id','name'),
            ['disabled' => true, 'id' => 'user_name']);
    ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'progress')->widget(Slider::classname(), [
        'pluginOptions'=>[
            'min'=>0,
            'max'=>100,
            'step'=>10,
            'value' => 0,
        ]
    ]); ?>

    <?=$form->field($model, 'priority')->DropDownList($arPriority); ?>

    <?= $form->field($model, 'start')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату..'],
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd',
        ]
    ]); ?>

    <?= $form->field($model, 'deadline')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Выберите дату..'],
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd',
        ]
    ]);?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
