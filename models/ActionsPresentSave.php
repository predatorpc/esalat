<?php

namespace app\models;

use app\modules\shop\models\Orders;
use Yii;

/**
 * This is the model class for table "actions_present_save".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $basket_id
 * @property integer $present
 * @property string $card_number
 * @property string $create_date
 * @property string $update_date
 * @property string $bought_date
 * @property integer $status
 */
class ActionsPresentSave extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'actions_present_save';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'basket_id', 'present', 'status'], 'integer'],
            [['basket_id'], 'required'],
            [['create_date', 'update_date', 'bought_date'], 'safe'],
            [['card_number'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'basket_id' => 'Basket ID',
            'present' => 'Present',
            'card_number' => 'Card Number',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
            'bought_date' => 'Bought Date',
            'status' => 'Status',
        ];
    }

    public function getOrder(){
        return $this->hasOne(Orders::className(), ['basket_id'=>'basket_id']);
    }
}
