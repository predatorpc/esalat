<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_options_values".
 *
 * @property integer $id
 * @property integer $option_id
 * @property integer $good_id
 * @property integer $unit_id
 * @property string $value
 * @property integer $status
 */
class GoodsOptionsValues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_options_values';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_id', 'good_id', 'unit_id', 'status'], 'integer'],
            [['value'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'option_id' => 'Option ID',
            'good_id' => 'Good ID',
            'unit_id' => 'Unit ID',
            'value' => 'Value',
            'status' => 'Status',
        ];
    }
}
