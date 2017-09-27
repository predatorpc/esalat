<?php

namespace app\modules\catalog\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "tags".
 *
 * @property integer $id
 * @property integer $group_id
 * @property string $value
 * @property integer $status
 */
class Tags extends ActiveRecordRelation
{
    const RELATED_PRODUCT = 1035;
    public $tagName;
    public $variationId;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tags';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'status'], 'integer'],
            [['value'], 'string', 'max' => 64],
            [['tagName','variationId'], 'safe']
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
            'value' => 'Value',
            'status' => 'Status',
        ];
    }
}
