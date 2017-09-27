<?php

namespace app\modules\catalog\models;

use Yii;
use yii\web\UploadedFile;
use app\modules\common\models\ModFunctions;
/**
 * This is the model class for table "sticker".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 *
 * @property StickerLinks[] $stickerLinks
 */
class Sticker extends \yii\db\ActiveRecord
{
    public $iconFiles;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sticker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['iconFiles'], 'file', 'extensions' => 'png'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'status' => 'Активно',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStickerLinks()
    {
        return $this->hasMany(StickerLinks::className(), ['sticker_id' => 'id']);
    }
    public function upload($id)
    {
        if ($this->validate() && !empty($this->iconFiles->extension)) {
            $files = $_SERVER['DOCUMENT_ROOT'].'/files/sticker/' . $id . '.' . $this->iconFiles->extension;
            if($this->iconFiles->saveAs($files)) {
                ModFunctions::img_resize($files, $files, 40);
            }
            return true;
        } else {
            return false;
        }
    }


}
