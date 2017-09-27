<?php

namespace app\modules\catalog\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "tags_groups_types".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 */
class TagsGroupsTypes extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tags_groups_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 32]
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
            'status' => 'Status',
        ];
    }
}
