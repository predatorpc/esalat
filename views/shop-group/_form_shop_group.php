<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\managment\models\Shops;
use app\modules\managment\models\Comissions;

$shopsList = ArrayHelper::map(Shops::find()->where(['status' => 1])->all(),'id','name');
$shopsListAll[] = Yii::t('admin', 'выберите Магазин');
foreach($shopsList as $ix => $shop){
    $shopsListAll[$ix] = $shop;
}
?>

<div class="shops-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype'=>'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'comission_id')->DropDownList(ArrayHelper::map(Comissions::find()->all(),'id','name'))->label(Yii::t('admin', 'Тип комиссии')) ?>
    <?= $form->field($model, 'status')->checkbox()->label(Yii::t('admin', 'Активность')) ?>

    <table class="teacher-portfolio-table table table-striped table-bordered">
        <tr>
            <td><div class="form-group"><label class="control-label">#</label></div></td>
            <td><div class="form-group"><label class="control-label"><?= Yii::t('admin', 'Группа магазинов') ?></label></div></td>
            <td></td>
        </tr>
        <?php
        if(isset($shops) && !empty($shops)){
            foreach($shops as $i=>$item) {
                if (!isset($item->id)) {
                    ?>
                    <tr>
                        <td colspan="9"><?= Yii::t('admin', 'Добавить') ?></td>
                    </tr><?php
                } ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= $form->field($item, "[$i]shop_group_id")->hiddenInput(['maxlength' => true,'value' => $model->id])->label('');print $model->id; ?></td>
                    <td><?= $form->field($item, "[$i]shop_id")->DropDownList($shopsListAll)->label(Yii::t('admin', 'Магазин')) ?></td>
                    <td><?php
                        if (isset($item->id)) {
                            ?><a data-confirm="Вы уверены?" data-method="post" href="/shop-management/shop-group-related-delete?group=<?= $model->id?>&id=<?=$item->id?>">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a><?php
                        }?>
                    </td>
                </tr>
                <?php
            }
        }?>

    </table>

    <?php
    echo Html::submitButton(Yii::t('admin', 'Сохранить'), ['class' => 'btn btn-primary']);
    ActiveForm::end();
    ?>
</div>