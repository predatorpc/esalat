<?php

namespace app\modules\control\widgets;

use yii\base\Widget;

class UserData extends Widget{
    public $user;

    public function run(){
        if(empty($this->user)){
            return false;
        }?>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">ID</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->user->id?></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Имя</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->user->name?></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Email</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->user->email?></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Телефон</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->user->phone?></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Бонусы</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->user->bonus?></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Остаток на счёте</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->user->money?></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">&nbsp;</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->user->staff == 1 ? 'Сотрудник' : ''?></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Карты покупателя</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?php
                if(!empty($this->user->cards)){
                    foreach ($this->user->cards as $card) {
                        print $card->card_number . '<br />';
                    }
                }?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Промо-коды покупателя</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?php
                if(!empty($this->user->promoCodes)){
                    foreach ($this->user->promoCodes as $promoCode) {
                        print $promoCode->code . '<br />';
                    }
                }?>
            </div>
        </div>

        <?php
    }
}
