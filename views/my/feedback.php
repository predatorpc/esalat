<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use app\modules\common\models\ModFunctions;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Обратная связь');
$this->params['breadcrumbs'][] = $this->title;

?>

<!--Content-->
<div class="content">

    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-3">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">
            <h1 class="title"><?= $this->title; ?> </h1>
            <div class="my-feedback">
                <div class="text_min">
                    <?= \Yii::t('app','Оставьте свой отзыв о работе нашего интернет-магазина и мы ответим вам в течение двух рабочих дней. Для оперативной связи воспользуйтесь онлайн-консультантом.')?>
                </div><br>
                <!--Форма-->
                <div class="form___gl feedback_form">
                    <?php if(Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success"> <?= Yii::$app->session->getFlash('success')?></div> <!-- For success message -->
                    <?php else: ?>
                    <?php $form = ActiveForm::begin(
                              ['options' => ['class' => 'feedback-form','enctype' => 'multipart/form-data'],
                                  'enableAjaxValidation'   => false,
                                  'enableClientValidation' => true,
                                  'validateOnBlur'         => false,
                                  'validateOnType'         => false,
                                  'validateOnChange'       => false,
                                  'validateOnSubmit'       => true,
                              ]); ?>
                         <?= $form->field($model, 'order')->textInput(['maxlength' => 60 ,'class'=>'form-control placeholder number', 'autofocus' => true ,'placeholder'=>Yii::t('app',"Номер заказа"),'data-text'=>Yii::t('app',"Номер заказа")])->label(false)?>
                         <?= $form->field($model, 'topic')->textInput(['maxlength' => 255 ,'class'=>'form-control placeholder string', 'autofocus' => true ,'placeholder'=>Yii::t('app',"Тема"),'data-text'=>Yii::t('app',"Тема")])->label(false)?>
                         <?= $form->field($model, 'text')->textarea(['rows' => 2, 'cols' => 5,'class'=>'form-control placeholder string','placeholder'=>Yii::t('app',"Введите текст"),'data-text'=>Yii::t('app',"Введите текст")])->label(false);?>
                         <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->hint(Yii::t('app','Вы можете загрузить не более 4 файлов в формате jpg, png (макс. 5 мб).'))->label(Yii::t('app','Загрузить файл')) ?>
                         <div class="form-group">
                            <?= Html::submitButton(Yii::t('app','Отправить'), ['class' => 'button_oran left button__a', 'name' => 'contact-button']) ?>
                         </div>
                         <div class="form-group button-ajax hidden"><button type="submit" class="button_oran left button__a" onclick="form_action('feedback_form'); return false;"><?=\Yii::t('app','Отправить')?></button></div>
                    <?php ActiveForm::end(); ?>
                    <?php endif; ?>
                </div> <!--/Форма-->

                <!--Уведомления -->
                <div class="notice">
                    <div class="items">
                        <?php foreach($notice as $key=>$item):?>
                        <div class="item">
                            <div class="name"><?=ModFunctions::userName($item['name'])?> <span class="date"><?=ModFunctions::date($item['date'])?> г.<?php if(isset($item['order'])):?> № <?=$item['order']?><?php endif;?></span></div>
                            <?php if(isset($item['topic'])): ?><div class="topic"><b>Тема:</b> <?=$item['topic']?></div><?php endif;?>
                            <div class="text"><?=$item['text']?></div>
                            <?php if(isset($item['images'])):?>
                               <?php foreach($item['images'] as $k=>$image): ?>
                                    <div class="image"><img src="/files/images/<?=$image['id'].'.'.$image['exp']?>" ></div>
                                <?php endforeach;?>
                                <div class="clear"></div>
                            <?php endif; ?>
                        </div>
                            <?php if(isset($item['answer'])):?>
                            <div class="item admin">
                                <div class="name"><?=\Yii::t('app','Администратор')?> <span class="date"><?=ModFunctions::date($item['date'])?> г.</span></div>
                                <div class="text"><?=$item['answer']?></div>
                            </div>
                           <?php endif; ?>
                        <?php endforeach;?>
                    </div>
                </div> <!--.Уведомления -->
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <div class="clear"></div>
</div><!--/Content-->