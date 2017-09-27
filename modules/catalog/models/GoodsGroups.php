<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_groups".
 *
 * @property integer $id
 * @property integer $group_id
 * @property string $url
 * @property string $name
 * @property string $title_name
 * @property string $description
 * @property string $color
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property integer $position
 * @property integer $status
 */
class GoodsGroups extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'position', 'status'], 'integer'],
            [['description'], 'required'],
            [['description'], 'string'],
            [['url', 'name', 'seo_title', 'seo_description', 'seo_keywords'], 'string', 'max' => 128],
            [['title_name'], 'string', 'max' => 64],
            [['color'], 'string', 'max' => 8]
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
            'url' => 'Url',
            'name' => 'Name',
            'title_name' => 'Title Name',
            'description' => 'Description',
            'color' => 'Color',
            'seo_title' => 'Seo Title',
            'seo_description' => 'Seo Description',
            'seo_keywords' => 'Seo Keywords',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }
}
