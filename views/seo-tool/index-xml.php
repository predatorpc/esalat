<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = Yii::t('admin','Выгрузка YML/XML');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-xml">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="category-select">
        <b class="title"><?=\Yii::t('admin','Список категорий');?></b>
        <div class="items">
            +Потом сделаю+
        </div>
    </div>
    <?php if(!empty($filelist)): ?>
     <div class="path-f col-xs-6">
         <label><?=\Yii::t('admin','Пути');?></label>
        <div class="path-content">
             <?php foreach($filelist as $key=>$value): ?>
                <div>http://www.esalad.ru/files/xml/<?=$value?></div>
             <?php endforeach; ?>
        </div>
     </div>
    <?php endif;?>
    <div class="clear"></div>
    <?= Html::beginForm(['/seo-tool/index-xml'], 'post', ['class' => 'xml-form']) ?>

    <div class="files">
        <b class="title"><?=\Yii::t('admin','Список файлов');?></b>
        <div class="xml">
            <?php if(!empty($filelist)): ?>
                <?php foreach($filelist as $key=>$value): ?>
                    <div><a href="/files/xml/<?=$value?>" target="_blank"><?=$value?></a> <a href="/files/xml/<?=$value?>" download><span class="glyphicon glyphicon-cloud-download" title="<?=\Yii::t('admin','Скачать');?>"></span></a>  <?= Html::submitButton( '&times;', ['class' => 'close','name'=> 'delete', 'value'=> $value]); ?></div>
                 <?php endforeach; ?>
            <?php else:?>
                  <div><?=\Yii::t('admin','Нет записей');?></div>
            <?php endif;?>
        </div>
    </div>

    <?php if (Yii::$app->session->hasFlash('successFiles')): ?>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong><?=\Yii::t('admin','Успешно сгенирирован');?></strong> <a href="/files/xml/<?=!empty($filelist[0])? $filelist[0] : ''?>"  download><?=!empty($filelist[0])? $filelist[0] : 'Ооопс! файла то нет?'?></a>
        </div>
    <?php endif;?>
    <b class="title"><?=\Yii::t('admin','Сгенерировать');?></b>
    <div>
        <?= Html::submitButton( 'YML', ['class' => 'btn btn-primary','name'=> 'YML', 'value'=> '1']); ?>
        <?= Html::submitButton( 'XML', ['class' => 'btn btn-primary', 'name'=> 'XML', 'value'=> '1']); ?>
    </div>

    <?= Html::endForm(); ?>
</div>
