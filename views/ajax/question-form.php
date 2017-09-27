<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<h2><?=$question->question;?></h2>

<div>

    <?php $form = ActiveForm::begin(['options' => ['class' => 'ask-question']]); ?>

    <?= $form->field($answer, 'answer')->textInput()->label(false) ?>

    <?= $form->field($answer, 'question_id')->hiddenInput()->label(false); ?>

    <?= $form->field($answer, 'user_id')->hiddenInput()->label(false); ?>

    <div class="form-group">
        <button type="submit" class="button_oran" onclick="return modal_form_action('ask-question','ajax/save-answer?section=<?= str_replace('/ajax/ask-question?section=','',$_SERVER['REQUEST_URI']);?>');">Ответить</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $('#windows').on('hidden.bs.modal', function (e) {
        $.ajax({
            url: '/ajax/view-question?question_id=<?=$answer->question_id;?>&section=<?= str_replace('/ajax/ask-question?section=','',$_SERVER['REQUEST_URI']);?>',
            type: "GET",
            success: function (data) {
                console.log(data);
                return true;
            }
        });
    });
</script>
