<?php

namespace app\modules\catalog\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "tags_links".
 *
 * @property integer $id
 * @property integer $variation_id
 * @property integer $tag_id
 * @property integer $status
 */
class TagsLinks extends ActiveRecordRelation
{
    public $value;
    public $tagsGroupsId;
    public $tagsLinksId;
    public $uniqueHashString;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tags_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variation_id', 'tag_id', 'status'], 'integer'],
            [['value','tagsGroupsId','tagsLinksId','uniqueHashString'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'variation_id' => 'Variation ID',
            'tag_id' => 'Tag ID',
            'status' => 'Status',
        ];
    }

    public function gettags()
    {
        return $this->hasOne(Tags::className(), ['id' => 'tag_id']);
    }

    public function getGoods_variations()
    {
        return $this->hasOne(GoodsVariations::className(), ['id' => 'variation_id']);
    }
}
