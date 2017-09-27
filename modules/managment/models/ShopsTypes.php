<?php

namespace app\modules\managment\models;

use Yii;

/**
 * This is the model class for table "shops_types".
 *
 * @property integer $id
 * @property string $name
 * @property integer $position
 * @property integer $status
 */
class ShopsTypes extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shops_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
            'position' => 'Position',
            'status' => 'Status',
        ];
    }
}
