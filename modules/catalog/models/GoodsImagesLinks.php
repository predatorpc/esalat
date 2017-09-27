<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_images_links".
 *
 * @property integer $id
 * @property integer $good_id
 * @property integer $variation_id
 * @property integer $image_id
 * @property integer $position
 * @property integer $main
 * @property integer $status
 */
class GoodsImagesLinks extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_images_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['good_id', 'image_id'], 'required'],
            [['good_id', 'variation_id', 'image_id', 'position', 'main', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'good_id' => 'Good ID',
            'variation_id' => 'Variation ID',
            'image_id' => 'Image ID',
            'position' => 'Position',
            'main' => 'Main',
            'status' => 'Status',
        ];
    }
}
