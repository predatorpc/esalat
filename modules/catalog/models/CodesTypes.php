<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "codes_types".
 *
 * @property integer $id
 * @property string $name
 * @property string $discount
 * @property string $fee
 * @property integer $show
 * @property integer $status
 *
 * @property Codes[] $codes
 */
class CodesTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'codes_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discount', 'fee', 'max_sum_fee', 'discount_sport', 'fee_sport','money_discount'], 'number'],
            [['show', 'status'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['period_days'], 'string', 'max' => 20]
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
            'discount' => 'Discount',
            'discount_sport' => 'Discount sport',
            'fee' => 'Fee',
            'fee_sport' => 'Fee sport',
            'show' => 'Show',
            'status' => 'Status',
            'period_days' => 'Preiod',
            'max_sum_fee' => 'Max sum of cash back',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodes()
    {
        return $this->hasMany(Codes::className(), ['type_id' => 'id']);
    }
}
