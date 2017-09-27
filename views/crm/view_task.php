<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\common\models\User;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\slider\Slider;
use kartik\date\DatePicker;
$users = User::find()->where(['not', ['staff' => null]])->andWhere(['status'=>1])->orderBy('name')->asArray()->All();
$users = ArrayHelper::map($users,'id','name');

/* @var $this yii\web\View */
/* @var $model app\modules\crm\models\CrmTasks */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Задание', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$arPriority = [0 => 'Низкий', 1 => 'Средний ',2 => 'Высокий'];

?>
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
    echo Html::beginForm(['crm/viewtask'], //наш урл
    'get',          //наш метод
    [           //массив опций
    'data-pjax' => true, //чтобы работал пъякс
    'enableAjaxValidation' => true, //чтобы проверял раз аякс
    'class' => 'form-inline' // класс стилей для формы
    ]);
    //Айдишник модели для которой мы делаем поиск
    echo Html::hiddenInput('id', $model->id);
    //Поисковая строка, как мы ищем по телефону или как-то еще
    echo Html::input('text', 'search', Yii::$app->request->post('string'),
    [
    'class' => 'form-control',
    'minlength' => 2,
    'maxlength' => 10
    ]);
    //бутон сабмита
    echo Html::submitButton('Найти', ['class' => 'btn btn-primary', 'name' => 'hash-button']);
    echo Html::endForm();
    //////////////////////////////////////////////////////////////////////////////////////
    // Конец формы
    //////////////////////////////////////////////////////////////////////////////////////
    echo "<br>";
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
<div class="crm-tasks-form">

    <?php $form = ActiveForm::begin(['validateOnSubmit' => false, ]);
        echo Html::hiddenInput('id_changed', 0, ['id' => 'id_changed']);
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
            'format' => 'yyyy-mm-dd'
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





<div class="crm-tasks-comment">
    <h2>Комментарии:</h2>
    <?php
    if(isset($CrmTasksComments) && count($CrmTasksComments)>0){
        echo '<table class="table table-striped">';
        echo '<tr><th>Имя</th><th>Дата</th><th>Комментарий</th><th>Прочитано</th></tr>';
        foreach ($CrmTasksComments as $comment) {
            echo '<tr>';
            $userName = User::find()->select('name')->where(['id' => $comment->user_id])->asArray()->one();
            $checked  ='';
            if($comment->read == 1){
                $checked  ='checked';
            }
            echo '<td>' . $userName['name'] . '</td><td>' . $comment->date . '</td><td>' . $comment->text . '</td><td><input class="readComment" id="'.$comment->id.'" type="checkbox" '.$checked.'></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    ?>
</div>

<?php
Modal::begin([
'header' => '<h3><b>'.Yii::t('admin', 'Добавить комментарий').':</b></h3>',
'toggleButton' => [
'tag' => 'button',
'class' => 'btn btn-primary',
'label' => Yii::t('admin', 'Добавить комментарий'),
]
]);
Pjax::begin();
echo Html::beginForm(['crm/addcomment'], 'post', ['data-pjax' => '']);
echo Html::hiddenInput('task_id', $model->id);
echo Html::hiddenInput('read', 0);
echo '<div class="form-group">';
    echo '<b>Комментарий</b><br>';
    echo Html::textarea('comment','',['class' => 'form-control']);
    echo '</div>';
echo '<div class="form-group">';
    echo Html::submitButton(Yii::t('admin', 'Добавить'), ['class' => 'btn btn-primary', 'name' => 'hash-button']);
    echo '</div>';
echo Html::endForm();
Pjax::end();
Modal::end();
?>
