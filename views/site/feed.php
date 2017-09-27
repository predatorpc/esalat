<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\rating\StarRating;
use yii\widgets\LinkPager;
use app\modules\common\models\ModFunctions;
use app\modules\my\models\Feedback;

$this->title = 'Отзывы об интернет иагазине  экстрим шоп';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-feed">
    <h1>Ваши Отзывы</h1>

    <div class="row">
        <div class="chat col-xs-12">
            <!--Форма-->
            <div class="feedback_form ">
                <div class="min-text text-center">Ваше мнение важно для нас.<br>
                    Оставьте сообщение, и мы ответим в ближайшее время.</div>
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
                    <?= $form->field($model, 'rating')->widget(StarRating::classname(), [
                        'pluginOptions' => [
                            'showClear'=>false,
                            'max' => 5,
                            'min' => 0,
                            'value' => 0,
                            'step' => 1,
                            'showCaption' => false,
                        ]
                    ])->label('Ваша оценка');
                    ?>
                    <?= $form->field($model, 'order')->textInput(['maxlength' => 60 ,'class'=>'form-control number', 'autofocus' => true])->label('Номер заказа')?>

                    <?= $form->field($model, 'text')->textarea(['rows' => 5, 'cols' => 5,'class'=>'form-control string','placeholder'=>Yii::t('app',"Укажите максимально подробную информацию") ])->label('Ваше сообщение');?>
                    <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->hint(Yii::t('app','Вы можете загрузить не более 4 файлов в формате jpg, png (макс. 5 мб).'))->label(Yii::t('app','Загрузить файл')) ?>
                    <div class="form-group">
                    <?php if(!Yii::$app->user->isGuest): ?>
                        <?= Html::submitButton(Yii::t('app','Отправить'), ['class' => 'btn btn-danger btn-lg','style'=>'float: right;', 'name' => 'contact-button']) ?>
                    <?php else: ?>
                        <div class="text">Необходимо <a href="/" onclick="return window_show('login','Вход');">войти</a> или <a href="/" onclick="return window_show('signup','Регистрация');">зарегистрироваться</a></div>
                    <?php  endif; ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <div class="clear"></div>
                <?php endif; ?>
            </div> <!--/Форма-->
            <div class="chat-history">
                <ul class="chat-ul">
              <?php if(!empty($notice)): ?>
                    <?php
                    $className = '';
                    foreach($notice as $key=>$item):
                        if($item->rating == 1 || $item->rating == 2) {
                            $className ='danger-com';
                        }elseif($item->rating == 3){
                            $className ='warning-com';
                        }elseif($item->rating == 4 || $item->rating == 5) {
                            $className ='success-com';
                        }else{
                            $className = 'default-com';
                        }
                        ?>
                    <li>
                        <div class="message-data">
                            <div class="user-feed">
                                <img src="/images/no_photo.png" alt="" class="<?=$className?>"/>
                                <div class="user-writer">
                                    <div class="rating"><div class="rating-icon rating-<?=$item->rating?>"></div></div>
                                    <div class="message-name"><?=ModFunctions::userName($item->name,true)?></div>
                                    <div class="message-date"><?=Yii::$app->formatter->asDate($item->date,'d MMMM')?></div>
                                </div>
                            </div>
                        </div>
                        <div class="message <?=$className?>">
                            <?php if(isset($item->topic)): ?><h4><?=$item->topic?></h4><?php endif;?>
                           <div class="text"><?=$item->text?></div>
                            <?php if(isset($item->messagesImages)):?>
                                <div class="image">
                                <?php foreach($item->messagesImages as $k=>$image): ?>
                                   <a class="no-border" href="/files/images/<?=$image->id.'.'.$image->exp?>" target="_blank"><img src="/files/images/<?=$image->id.'.'.$image->exp?>" ></a>
                                <?php endforeach;?>
                                </div>
                                <div class="clear"></div>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php if(isset($item->answer)):?>
                      <li class="clearfix">
                        <div class="message-data align-right call">
                            <div class="user-feed">
                                <div class="user-writer">
                                    <div class="message-name">Отдел лояльности</div>
                                </div>
                                <img src="/images/photo_call.png" alt="" class="primary-com" />
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="message float-right me-message primary-com"><?=$item->answer?></div>
                    </li>
                    <?php endif; ?>
                    <?php endforeach;?>
                   <?php else: ?>
                     <li><b>Отзывов пока нет, будьте первым!</b></li>
                   <?php endif; ?>
                </ul>
                <div class="clear"></div>
                <?php
                // отображаем постраничную разбивку
                echo LinkPager::widget([
                    'pagination' => $pages,
                    'registerLinkTags' => true
                ]);
                ?>
                <div class="mores hidden"><b>Более старые отзывы</b></div>
            </div> <!-- end chat-history -->
        </div> <!-- end chat -->
        <div class="hidden rating_com col-xs-4">
            <div class="rating-block">
                <h2 class="bold padding-bottom-7">0.0</h2>
                <h4>средняя оценка</h4>
            </div>
            <div class="pull-left">
                <div class="pull-left" style="width:35px; line-height:1;">
                    <div style="height:9px; margin:5px 0;">5 <span class="glyphicon glyphicon-star"></span></div>
                </div>
                <div class="pull-left" style="width:180px;">
                    <div class="progress" style="height:9px; margin:8px 0;">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="5" style="width: <?=Feedback::find()->where(['type_id'=>1003,'rating'=>5,'status'=>1])->count();?>%">
                        </div>
                    </div>
                </div>
                <div class="pull-right" style="margin-left:10px;"><?=Feedback::find()->where(['type_id'=>1003,'rating'=>5,'status'=>1])->count();?></div>
            </div>
            <div class="pull-left">
                <div class="pull-left" style="width:35px; line-height:1;">
                    <div style="height:9px; margin:5px 0;">4 <span class="glyphicon glyphicon-star"></span></div>
                </div>
                <div class="pull-left" style="width:180px;">
                    <div class="progress" style="height:9px; margin:8px 0;">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="4" aria-valuemin="0" aria-valuemax="5" style="width: <?=Feedback::find()->where(['type_id'=>1003,'rating'=>4,'status'=>1])->count();?>%">

                        </div>
                    </div>
                </div>
                <div class="pull-right" style="margin-left:10px;"><?=Feedback::find()->where(['type_id'=>1003,'rating'=>4,'status'=>1])->count();?></div>
            </div>
            <div class="pull-left">
                <div class="pull-left" style="width:35px; line-height:1;">
                    <div style="height:9px; margin:5px 0;">3 <span class="glyphicon glyphicon-star"></span></div>
                </div>
                <div class="pull-left" style="width:180px;">
                    <div class="progress" style="height:9px; margin:8px 0;">
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="3" aria-valuemin="0" aria-valuemax="5" style="width: <?=Feedback::find()->where(['type_id'=>1003,'rating'=>3,'status'=>1])->count();?>%">
                        </div>
                    </div>
                </div>
                <div class="pull-right" style="margin-left:10px;"><?=Feedback::find()->where(['type_id'=>1003,'rating'=>3,'status'=>1])->count();?></div>
            </div>
            <div class="pull-left">
                <div class="pull-left" style="width:35px; line-height:1;">
                    <div style="height:9px; margin:5px 0;">2 <span class="glyphicon glyphicon-star"></span></div>
                </div>
                <div class="pull-left" style="width:180px;">
                    <div class="progress" style="height:9px; margin:8px 0;">
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="5" style="width: <?=Feedback::find()->where(['type_id'=>1003,'rating'=>2,'status'=>1])->count();?>%">
                        </div>
                    </div>
                </div>
                <div class="pull-right" style="margin-left:10px;"><?=Feedback::find()->where(['type_id'=>1003,'rating'=>2,'status'=>1])->count();?></div>
            </div>
            <div class="pull-left">
                <div class="pull-left" style="width:35px; line-height:1;">
                    <div style="height:9px; margin:5px 0;">1 <span class="glyphicon glyphicon-star"></span></div>
                </div>
                <div class="pull-left" style="width:180px;">
                    <div class="progress" style="height:9px; margin:8px 0;">
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="5" style="width: <?=Feedback::find()->where(['type_id'=>1003,'rating'=>1,'status'=>1])->count();?>%">

                        </div>
                    </div>
                </div>
                <div class="pull-right" style="margin-left:10px;"><?=Feedback::find()->where(['type_id'=>1003,'rating'=>1,'status'=>1])->count();?></div>
            </div>
        </div>
    </div>
</div>
