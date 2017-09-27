<?php

namespace app\modules\catalog\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "tags_groups".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property integer $show
 * @property integer $position
 * @property integer $status
 *
 * @property Tags[] $tags
 */
class TagsGroups extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tags_groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'show', 'position', 'status'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'show' => 'Show',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tags::className(), ['group_id' => 'id']);
    }

    public static function getFrontWhere(){
        return self::find()
            ->where([
                'status' => 1,
                'show' => 1,
                'type' => 1,
            ])
            ->select('id')
            ->column();
    }

    public function getBackendWhere(){
        return false;
    }
}
