<?php

namespace app\modules\actions\models;
use app\modules\common\models\User;
use Yii;

/**
 * This is the model class for table "actions_accumulation".
 *
 * @property integer $id
 * @property integer $basket_id
 * @property integer $product_id
 * @property integer $current_value
 * @property integer $currency_id
 * @property integer $action_id
 * @property integer $action_param_value_id
 * @property integer $status
 *
 * @property ActionsParamsValue $actionParamValue
 * @property Basket $basket
 * @property ActionCurrency $currency
 */
class ActionsAccumulation extends \yii\db\ActiveRecord
{
    public $count_row;
    public $active_row;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'actions_accumulation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'current_value', 'action_id', 'action_param_value_id'], 'required'],
            [['user_id', 'order_id', 'product_id', 'current_value', 'currency_id', 'action_id', 'action_param_value_id', 'active', 'status'], 'integer'],
            [['action_param_value_id'], 'exist', 'skipOnError' => true, 'targetClass' => ActionsParamsValue::className(), 'targetAttribute' => ['action_param_value_id' => 'id']],
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
            'order_id'=>'Order ID',
            'product_id' => 'Product ID',
            'current_value' => 'Current Value',
            'currency_id' => 'Currency ID',
            'action_id' => 'Action ID',
            'action_param_value_id' => 'Action Param Value ID',
            'active' => 'Active',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionParamValue()
    {
        return $this->hasOne(ActionsParamsValue::className(), ['id' => 'action_param_value_id']);
    }
    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getAction(){//для отчета поэтому ONE
        return $this->hasOne(Actions::className(), ['id' => 'action_id']);
    }
    public function getParamName(){//для отчета
        return $this->hasOne(ActionsParams::className(), ['id' => 'param_id'])->viaTable(ActionsParamsValue::tableName(), ['id' => 'action_param_value_id']);
    }


}
