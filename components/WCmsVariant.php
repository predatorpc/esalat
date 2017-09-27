<?php

namespace app\components;

use app\models\GoodsImages;
use app\models\Tags;
use app\models\TagsLinks;
use app\models\Zloradnij;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class WCmsVariant extends Widget
{
    public $variant;
    public $shopsList;
    public $shopStoreCount;
    public $tagGroup;
    public $variantProperties;
    public $i;
    public $form;
    public $tagValue;
    public $images;

    public function init()
    {
        parent::init();
        if ($this->variant === null) {
            $this->variant = false;
        }
        if ($this->shopsList === null) {
            $this->shopsList = false;
        }
        if ($this->shopStoreCount === null) {
            $this->shopStoreCount = false;
        }
        if ($this->tagGroup === null) {
            $this->tagGroup = false;
        }
        if ($this->variantProperties === null) {
            $this->variantProperties = false;
        }
        if ($this->i === null) {
            $this->i = 0;
        }
        if ($this->form === null) {
            $this->form = false;
        }
        if ($this->tagValue === null) {
            $this->tagValue = false;
        }
        if ($this->images === null) {
            $this->images = false;
        }
    }

    public function run(){
        if(!$this->variant){
            return false;
        }else{
//            Zloradnij::printArray($this->shopStoreCount);die();
            if (!isset($this->variant->id)) {
                $variantIdStatic = ($this->i > 0)?$this->i:0;
                ?>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 regular" data-variant="<?=$variantIdStatic?>">
                    <div class="col-xs-3 col-sm-3 col-md-4 col-lg-4"><?= $this->i+1 ?></div>
                    <div class="col-xs-9 col-sm-9 col-md-8 col-lg-8">
                        <div class="form-group">
                            <label class="control-label">
                                <span class="<?=($this->variant->status == 1)?'active':'passive'?>">
                                    Новая вариация товара
                                </span>
                            </label>
                        </div>
                    </div>
                </div><?php
            }else{
                $variantIdStatic = $this->variant->id;
                ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 regular" data-variant="<?= $this->variant->id?>">
                <div class="col-xs-3 col-sm-3 col-md-4 col-lg-4"><?= $this->i+1 ?></div>
                <div class="col-xs-9 col-sm-9 col-md-8 col-lg-8">
                    <span class="<?=($this->variant->status == 1)?'active':'passive'?>">
                        <?= $this->variant->id. ' - ' . (!$this->variant->full_name?'':$this->variant->full_name . ' - ') .$this->variant->tags_name?>
                    </span>
                </div>
            </div><?php
            } ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div colspan="2"><?= $this->form->field($this->variant, "[$this->i]full_name")->textInput(['maxlength' => true,'class' => 'form-control']); ?></div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div colspan="2"><?= $this->form->field($this->variant, "[$this->i]price")->textInput(['maxlength' => true,'class' => 'form-control price_in']); ?></div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div colspan="2"><?= $this->form->field($this->variant, "[$this->i]comission")->textInput(['maxlength' => true,'id' => 'comission_id','class' => 'comission']); ?></div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div colspan="2">
                    <div class="form-group field-goodsvariations-0-comission">
                        <label class="control-label" for="goodsvariations-0-comission">Цена выходная</label>
                        <input type="text" class="price_out form-control" value="<?=((1+$this->variant->comission/100) * $this->variant->price)?>" />
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div colspan="2">
                    <?php
                    if(isset($this->shopsList) && !empty($this->shopsList)){?>
                        <label class="control-label">Товар на складах</label>
                        <?php
                    }?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div class="col-xs-3 col-sm-3 col-md-4 col-lg-4">
                </div>
                <div class="col-xs-9 col-sm-9 col-md-8 col-lg-8">
                    <?php
                    if($this->shopStoreCount && isset($this->shopsList) && !empty($this->shopsList)){
                        foreach($this->shopsList as $item){
//                            print '--';
//                            Zloradnij::printArray($this->shopStoreCount[$variantIdStatic]);die();?>
                            <?= $this->form->field($this->shopStoreCount[$variantIdStatic][$item->id], "[".$variantIdStatic."][$item->id]id")->hiddenInput()->label(''); ?>
                            <?= $this->form
                                ->field($this->shopStoreCount[$variantIdStatic][$item->id], "[".$variantIdStatic."][$item->id]count")
                                ->textInput(['maxlength' => true])
                                ->label($item->shopName.'<br />'.$item->name.'<br />'.$item->address); ?>
                            <?php
                        }
                    }else{

                    }?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div colspan="2"><label class="control-label">Свойства товара</label></div>
            </div>
            <?php
            foreach($this->tagGroup as $tag){
                if(isset($this->variantProperties[$variantIdStatic][$tag->id])){?>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                        <div class="col-xs-3 col-sm-3 col-md-4 col-lg-4">- <?= $tag->name ?></div>
                        <div class="col-xs-9 col-sm-9 col-md-8 col-lg-8">
                            <?= $this->form->field($this->variantProperties[$variantIdStatic][$tag->id][0], "[".$variantIdStatic."][".$tag->id."]tag_id")->DropDownList(ArrayHelper::map($this->tagValue[$tag->id],'id','value'),['prompt' => 'выберите значение'])->label(''); ?>
                            <?php
//                            Zloradnij::printArray($this->variantProperties[$this->variant->id][$tag->id]);
//                            foreach($this->variantProperties[$this->variant->id][$tag->id] as $item){
//                                print $item->value . ', ';
//                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }?>
            <?php
            foreach($this->tagGroup as $tag){
                if(!isset($this->variantProperties[$variantIdStatic][$tag->id])){
                    $this->variantProperties[$variantIdStatic][$tag->id][0] = new TagsLinks();
                    ?>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                        <div class="col-xs-3 col-sm-3 col-md-4 col-lg-4">- <?= $tag->name ?></div>
                        <div class="col-xs-9 col-sm-9 col-md-8 col-lg-8">
                            <?= $this->form->field($this->variantProperties[$variantIdStatic][$tag->id][0], "[".$variantIdStatic."][".$tag->id."]tag_id")->DropDownList(ArrayHelper::map($this->tagValue[$tag->id],'id','value'),['prompt' => 'выберите значение'])->label(''); ?>
                        </div>
                    </div>
                    <?php
                }
            }?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div colspan="2">
                    <?= $this->form->field($this->variant, "[$this->i]description")->widget(\dosamigos\ckeditor\CKEditor::className(), [
                        'options' => ['rows' => 6],
                        'preset' => 'basic'
                    ]) ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div class="col-xs-3 col-sm-3 col-md-4 col-lg-4"><label class="control-label">Загрузить фото</label></div>
                <div class="col-xs-9 col-sm-9 col-md-8 col-lg-8">
                    <?php
                    $this->images[$variantIdStatic][] = new GoodsImages();
                    $j = 0;
                    foreach($this->images[$variantIdStatic] as $image){
                        if(!isset($image->id)){?>
                            <?php
                            print $this->form->field($image, "[".$variantIdStatic."][$j]id")->fileInput()->hint('Загрузить фото товара')->label('');
                            ?><?php
                        }else{?>
                            <img src="<?= GoodsImages::getImagePath($variantIdStatic) ?>" style="width:150px;" /><?php
                        }
                        $j++;
                    }?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 not-visible" data-variant="<?= $variantIdStatic?>">
                <div colspan="2"><?= $this->form->field($this->variant, "[$this->i]status")->checkbox();?></div>
            </div>
<?php
        }
    }
}

