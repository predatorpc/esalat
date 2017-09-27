<?php

namespace app\components;

use app\models\RegionDistrict;
use yii\base\Widget;

class WWindowSetAddress extends Widget{

    public function run(){
        $districts = RegionDistrict::find()
            ->where([
                'status' => 1,
            ])
            ->all();

        $result = '
        <div id="shadow" style="top:0;">
            <div class="content">
                <div id="address" class="window mobile">
                    <div class="close"></div>
                    <div class="title">Заполните форму</div>
                    <div class="form">
                        <form action="" method="post">
                            <div class="item">
                                <div class="input">
                                    <div class="label">Район</div>
                                    <select name="district_id" class="list">
                                        <option value=""></option>
                ';
                                        foreach($districts as $district){
                                            $result .= '
                                            <option value="'.$district->id.'">'.$district->name.'</option>
                                            ';
                                        }
                $result .= '
                                    </select>
                                </div>
                            </div>
                            <div class="item">
                                <div class="input">
                                    <div class="label">Улица</div>
                                    <input type="text" name="street" value="" maxlength="64" autocomplete="off" class="string" />
                                </div>
                            </div>
                            <div class="item">
                                <div class="input">
                                    <div class="label">Дом</div>
                                    <input type="text" name="house" value="" maxlength="8" autocomplete="off" class="string" />
                                </div>
                            </div>
                            <div class="item">
                                <div class="input">
                                    <div class="label">Квартира</div>
                                    <input type="text" name="room" value="" maxlength="4" autocomplete="off" class="string" />
                                </div>
                            </div>
                            <div class="item">
                                <div class="input phone">
                                    <div class="label">Телефон</div>
                                    <span>+7</span>
                                    <input type="text" name="phone" value="{$user.phone|phone}" maxlength="10" autocomplete="off" class="string number" />
                                </div>
                            </div>
                            <div class="item">
                                <div class="input">
                                    <div class="label">Комментарий</div>
                                    <textarea name="comments" class="text"></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="form_address" value="true" />
                            <div class="error"></div>
                            <div class="button" onclick="return window_action_new_address(\'address\');">
                                <div>Сохранить</div>
                            </div>
                            <div class="button_load"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="load"></div>
        </div>
        ';

        return $result;
    }
}

