<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_links".
 *
 * @property integer $id
 * @property integer $group_id
 * @property integer $good_id
 * @property integer $status
 */
class GoodsLinks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'good_id', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'Group ID',
            'good_id' => 'Good ID',
            'status' => 'Status',
        ];
    }
}
