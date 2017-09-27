<?php

namespace app\modules\coders\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property integer $id
 * @property string $session_id
 * @property integer $user_id
 * @property integer $order_id
 * @property string $user_agent
 * @property string $remote_addr
 * @property integer $master
 */
class ClientLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['session_id'], 'required'],
            [['user_id', 'master','order_id', 'terminal'], 'integer'],
            [['user_agent'], 'string'],
            [['session_id','remote_addr'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_id' => 'Session ID',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
            'terminal' => 'Terminal',
            'user_agent' => 'User Agent',
            'terminal' => 'Terminal',
            'remote_addr' => 'Rempte Address',
            'master' => 'Master Time',
        ];
    }
}
