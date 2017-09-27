<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "logs_double".
 *
 * @property integer $id
 * @property string $time
 * @property integer $user_id
 * @property string $action
 * @property string $table_edit
 * @property string $calum_edit
 * @property integer $row_edit_id
 * @property string $new_val
 * @property string $old_val
 */
class LogsDouble extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs_double';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time'], 'safe'],
            [['user_id', 'row_edit_id'], 'integer'],
            [['action', ], 'string', 'max' => 20],
            [['table_edit', 'colum_edit'], 'string', 'max' => 50],
            [['new_val', 'old_val'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'time' => Yii::t('app', 'Time'),
            'user_id' => Yii::t('app', 'User ID'),
            'action' => Yii::t('app', 'Action'),
            'table_edit' => Yii::t('app', 'Table Edit'),
            'colum_edit' => Yii::t('app', 'Colum Edit'),
            'row_edit_id' => Yii::t('app', 'Row Edit ID'),
            'new_val' => Yii::t('app', 'New Val'),
            'old_val' => Yii::t('app', 'Old Val'),
        ];
    }
}
