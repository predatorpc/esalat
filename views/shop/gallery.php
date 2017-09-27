<?php
use yii\widgets\ActiveForm;

$shopId = 10000062;
?>
<div id="upload-gallery-block">
    <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['/ajax/save-files-gallery'],
                'options' => ['enctype' => 'multipart/form-data','id' => 'dropzoneForm', 'class' => 'dropzone']
            ]) ?>
        <input type="hidden" value="<?=Yii::$app->request->getCsrfToken()?>" name="_csrf" />
        <div class="fallback">
            <input type="file" name="GalleryShop[imageFiles]" />
        </div>
    <?php ActiveForm::end() ?>
    <div class="row gallery-image-container">
        <?php
        if(!empty($gallery)){
            foreach($gallery as $image){
                print '
                <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 gallery-image-container">
                    <img src="'.Yii::$app->params['galleryPath'].substr(md5($image->id), 0, 2).'/'.$shopId.'_'.$image->id.'_min.jpg">
                </div>
                ';
            }
        }
        ?>
    </div>
</div>
