<?php

namespace app\modules\managment\models;


use yii\db\ActiveRecord;

Class ShopsCallback extends ActiveRecord{

    public static function tableName()
    {
        return 'shops_callback';
    }
}