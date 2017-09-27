<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_options_units".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 */
class GoodsOptionsUnits extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_options_units';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
        ];
    }
}
