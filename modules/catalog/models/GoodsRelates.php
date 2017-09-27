<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_relates".
 *
 * @property integer $id
 * @property integer $good_id
 * @property integer $good_relate_id
 * @property integer $status
 */
class GoodsRelates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_relates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['good_id', 'good_relate_id', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'good_id' => 'Good ID',
            'good_relate_id' => 'Good Relate ID',
            'status' => 'Status',
        ];
    }
}
