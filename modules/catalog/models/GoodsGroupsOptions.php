<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_groups_options".
 *
 * @property integer $id
 * @property integer $group_id
 * @property integer $option_id
 * @property integer $status
 */
class GoodsGroupsOptions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_groups_options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'option_id', 'status'], 'integer']
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
            'option_id' => 'Option ID',
            'status' => 'Status',
        ];
    }
}
