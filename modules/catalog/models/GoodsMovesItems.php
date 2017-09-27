<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_moves_items".
 *
 * @property integer $id
 * @property integer $move_id
 * @property integer $good_id
 * @property integer $count
 * @property integer $status
 */
class GoodsMovesItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_moves_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['move_id', 'good_id', 'count', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'move_id' => 'Move ID',
            'good_id' => 'Good ID',
            'count' => 'Count',
            'status' => 'Status',
        ];
    }
}
