<?php

namespace app\components;

use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\TagsGroups;
use yii\base\Widget;


class WVariantForm extends Widget{
    public $key;

    public function init(){
        parent::init();
        if($this->key === null){
            $this->key = 0;
        }
    }

    public function run(){
//        $form = new ActiveForm();
        $variant = new GoodsVariations();

        $tagsList = TagsGroups::find()->where(['type' => 1])->andWhere(['status' => 1])->orderBy('position')->all();

        ?>
        <div class="variants" data-key="<?=$this->key?>">
            <div class="variant-title">
                Новая вариация
            </div>
            <div class="variant-body close">
                <div class="form-group field-goodsvariations-<?= $this->key?>-full_name">
                    <label class="control-label" for="goodsvariations-<?= $this->key?>-full_name">Полное наименование</label>
                    <input id="goodsvariations-<?= $this->key?>-full_name" class="form-control" type="text" maxlength="128" name="GoodsVariations[<?= $this->key?>][full_name]">
                    <div class="help-block"></div>
                </div>
                <div class="form-group field-goodsvariations-<?= $this->key?>-code">
                    <label class="control-label" for="goodsvariations-<?= $this->key?>-code">Артикул</label>
                    <input id="goodsvariations-<?= $this->key?>-code" class="form-control" type="text" maxlength="128" name="GoodsVariations[<?= $this->key?>][code]">
                    <div class="help-block"></div>
                </div>
                <div class="form-group field-goodsvariations-<?= $this->key?>-price required">
                    <label class="control-label" for="goodsvariations-<?= $this->key?>-price">Цена входная *</label>
                    <input id="goodsvariations-<?= $this->key?>-price" class="form-control price_in" type="text" name="GoodsVariations[<?= $this->key?>][price]">
                    <div class="help-block"></div>
                </div>
                <div class="form-group field-goodsvariations-<?= $this->key?>-comission required">
                    <label class="control-label" for="goodsvariations-<?= $this->key?>-comission">Комиссия *</label>
                    <input id="goodsvariations-<?= $this->key?>-comission" class="form-control comission money" type="text" name="GoodsVariations[<?= $this->key?>][comission]">
                    <div class="help-block"></div>
                </div>
                <div class="form-group has-success">
                    <label class="control-label" for="goodsvariations-<?= $this->key?>-price">Цена выходная</label>
                    <input id="goodsvariations-<?= $this->key?>-price-output" class="form-control price_out" type="text" name="" value="">
                    <div class="help-block"></div>
                </div>

                <!-- add tags -->
                <div class="variation">
                    <?php
                    foreach($tagsList as $k => $tagItem){
                        $oldTagsVariantsString = '';
                        if(isset($tagsListValue[$variant->id][$tagItem->id])){
                            $oldTagsVariantsString = '<span class="tag">';
                            foreach($tagsListValue[$variant->id][$tagItem->id] as $idTag => $valTag){
                                $oldTagsVariantsString .= '
                                                    <input type="hidden" value="Natural" name="variations_add['.$this->key.'][tags]['.$idTag.']">
                                                    '.$valTag.'
                                                    <a onclick="$(this).parent().remove(); return false;" title="Удалить тег" href="/">X</a>
                                                    ';
                            }
                            $oldTagsVariantsString .= '</span>';
                        }
                        print '
                                            <div class="form-group field-tagsgroups-name i options">
                                                <label class="control-label" for="tagsgroups-name">'.$tagItem->name.'</label>
                                                <div class="value value-'.$tagItem->id.'">
                                                    <input type="text" name="" value="" maxlength="64" group="'.$tagItem->id.'" class="string form-control">
                                                    <div class="load"></div>
                                                    <div class="values"></div>
                                                    '.$oldTagsVariantsString.'
                                                </div>
                                            </div>
                                            ';
                    }
                    ?>
                </div>
                <!-- END >> add tags -->
                <div class="form-group field-goodsvariations-<?= $this->key?>-status">
                    <input type="hidden" value="0" name="GoodsVariations[<?= $this->key?>][status]">
                    <label>
                        <input id="goodsvariations-<?= $this->key?>-status" type="checkbox" value="1" name="GoodsVariations[<?= $this->key?>][status]">
                        Активность
                    </label>
                    <div class="help-block"></div>
                </div>

            </div>
        </div>
        <hr />
        <?php
    }
}

/*
class WVariantForm extends CWidget {

    public function init() {
        return 'qwe';
    }

}*/