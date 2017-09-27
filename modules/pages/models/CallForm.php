<?php

namespace app\modules\pages\models;
use yii\base\Model;


class CallForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required', 'message' => 'Введите имя!'],
            ['phone', 'required', 'message' => 'Введите телефон!'],
            ['text', 'default', 'value' => ''],
            ['answer', 'default', 'value' => ''],
            ['type_id', 'default', 'value' => 1000],
            ['date', 'default', 'value' => date('Y-m-d')],
            ['status', 'default', 'value' => 0],
        ];

    }

}
