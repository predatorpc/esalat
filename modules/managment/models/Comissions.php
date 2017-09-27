<?php

namespace app\modules\managment\models;

use Yii;

/**
 * This is the model class for table "comissions".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property integer $position
 * @property integer $status
 *
 * @property Shops[] $shops
 */
class Comissions extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comissions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['position', 'status'], 'integer'],
            [['name'], 'string', 'max' => 64]
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
            'value' => 'Value',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShops()
    {
        return $this->hasMany(Shops::className(), ['comission_id' => 'id']);
    }
}
