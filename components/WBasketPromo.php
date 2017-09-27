<?php

namespace app\components;

use app\modules\catalog\models\Codes;
use yii\base\Widget;
use Yii;

class WBasketPromo extends Widget{
    public $promo;
    public $message;
    public $sort;

    public function init(){
        parent::init();
        if($this->promo === null){
            $this->promo = false;
        }
        if($this->message === null){
            $this->message = false;
        }
        if($this->sort === null){
            $this->sort = 5;
        }
    }
    public function run(){
        $promo = Codes::findOne($this->promo);
          if(Yii::$app->params['en']) {
              $result = '
                <div class="description-min">
                    * Price with promo code, if applicable.<br> Promo code is not required for purchase.
                </div>';
          }else {
              $result = '
                <div class="description-min">
                    * Цена с промо-кодом. Промо-код спрашивайте у администраторов спортзала или <a href="/promo">здесь &raquo;</a><br>Промо-код не является обязательным условием для покупки.
                </div>';
          }
            $result .= ' <div class="form___gl min">
            <div class="form-inline has-feedback '.(empty($promo)? 'has-error':'has-success').'">
                <input type="text" name="promo-code" value="'. (!empty($promo)?$promo->code:'') .'" maxlength="32" class="form-control text-center field-for-promo-insert" placeholder="'.Yii::t('app','Введите промо-код').'" />';
                if(!$this->message){

                }else{
                    $result .= '
                    <span class="glyphicon form-control-feedback '.(empty($this->promo)? 'glyphicon-remove ':' glyphicon-ok').'"></span>
                    <label class="discount control-label '.(!empty($this->promo)?'active':'').'">'.$this->message.'</label>';
                }
        $result .= '
            </div>
        </div>
        <div class="clear"></div>';

        return $result;
    }
}
