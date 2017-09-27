<?php

namespace app\components;

use yii\base\Widget;
use Yii;
class WBasketComment extends Widget{
    public $comment;
    public $sort;

    public function init(){
        parent::init();
        if($this->comment === null){
            $this->comment = false;
        }
        if($this->sort === null){
            $this->sort = 4;
        }
    }

    public function run(){
        $result = '
        <!--Комментарий-->
        <div id="comments" class="comments form___gl form-group">
             <textarea name="order_comments" class="form-control" placeholder="'.Yii::t('app','Комментарий к заказу').'" onfocus="$(this).attr(\'placeholder\',\'\')" onblur="$(this).attr(\'placeholder\', \''.Yii::t('app','Комментарий к заказу').'\')" >'. ($this->comment?$this->comment:'') .'</textarea>
        </div>
        ';
        return $result;
    }
}
