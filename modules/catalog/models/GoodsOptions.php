<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_options".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $name_all
 * @property integer $max
 * @property integer $position
 * @property integer $status
 */
class GoodsOptions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['max', 'position', 'status'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name', 'name_all'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'name_all' => 'Name All',
            'max' => 'Max',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }
}
