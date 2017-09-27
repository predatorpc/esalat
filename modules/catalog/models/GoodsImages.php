<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_images".
 *
 * @property integer $id
 * @property integer $good_id
 * @property integer $variation_id
 * @property string $hash
 * @property string $date
 * @property integer $position
 * @property integer $cover
 * @property integer $status
 */
class GoodsImages extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_images';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['good_id', 'variation_id', 'position','cover', 'status'], 'integer'],
            [['date'], 'safe'],
            [['hash'], 'string', 'max' => 32]
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
            'hash' => 'Hash',
            'date' => 'Date',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }

    public static function getImageFolder($image_id) {
        return substr(md5($image_id), 0, 2);
    }

    public static function getImagePath($variationId, $max = false) {
        $image = self::find()->where(['status' => 1, 'variation_id' => $variationId])->one();

        if(!$image){
            return false;
        }
        if(!$max){
            return '/files/goods/'. self::getImageFolder($image->id).'/'.$image->id.'.jpg';
        }
        return '/files/goods/'. self::getImageFolder($image->id).'/'.$image->id.'_max.jpg';
    }
//    public static function getImagePathElement($variationId, $max = false) {
//        $image = self::find()->where(['status' => 1, 'variation_id' => $variationId])->one();
//
//        if(!$image){
//            return false;
//        }
//
//        if(!$max){
//            return '/files/goods/'. self::getImageFolder($image->id).'/'.$image->id.'.jpg';
//        }
//        return '/files/goods/'. self::getImageFolder($image->id).'/'.$image->id.'_max.jpg';
//    }

    public static function getFileType($file, $binary = false) {
        if (!$binary) $file = file_get_contents($file);
        $bytes = bin2hex($file[0].$file[1]);
        $types = array(
            'ffd8' => 'jpg',
            '8950' => 'png',
            '4749' => 'gif',
            '4357' => 'swf'
        );
        return (isset($types[$bytes]) ? $types[$bytes] : '');
    }

    public static function imageResize($file, $width, $height) {
        // Создание изображения на основе исходного файла;
        if (!file_exists($file)) return false;
        // Проверка расширения;
        if (in_array(self::getFileType($file), array('jpg', 'jpeg'))) $source = imagecreatefromjpeg($file);
        elseif (in_array(self::getFileType($file), array('png'))) $source = imagecreatefrompng($file);
        elseif (in_array(self::getFileType($file), array('gif'))) $source = imagecreatefromgif($file);
        else return false;
        // Опеределение размеров изображения;
        $source_w = imagesx($source);
        $source_h = imagesy($source);
        // Вычисление масштаба;
        if ($source_w <= $width and $source_h <= $height) {
            $r = 1;
        } else {
            $r = max($source_w / $width, $source_h / $height);
        }
        // Вычисление пропорций;
        $image_w = round($source_w / $r);
        $image_h = round($source_h / $r);
        // Создание пустой картинки;
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        // Масштабирование изображения;
        imagecopyresampled($image, $source, ($width - $image_w) / 2, ($height - $image_h) / 2, 0, 0, $image_w, $image_h, $source_w, $source_h);
        // Вывод результата;
        imagejpeg($image, $file);
        // Удаление переменных;
        imagedestroy($image);
        imagedestroy($source);
    }

    public static function imageDelete($imageId) {
        GoodsImages::find()->where(['id' => $imageId])->one()->delete();

        $image_dir = $_SERVER['DOCUMENT_ROOT'].'/files/goods/'.(self::getImageFolder($imageId)).'/';
        $image_name = $image_dir.$imageId.'.jpg';
        $image_name_min = $image_dir.$imageId.'_min.jpg';
        $image_name_max = $image_dir.$imageId.'_max.jpg';

        unlink($image_name);
        unlink($image_name_min);
        unlink($image_name_max);
    }
    public static function images_upload($good_id, $variation_id, $images) {
        $image_position = 0;
        if ($images) {
            // Позиция фотографии;
            $image_position = GoodsImages::find()
                ->select(['MAX(`position`) AS `position`'])
                ->where(['variation_id' => $variation_id])
                ->one();
            if(!$image_position){

            }else{
                $image_position = $image_position->position;
            }

            foreach ($images as $image) {
              //  print_arr($image);
             //   die('UPLAUD');
//                $image = $image[0]['id'];
                // Обработка позиции фотографии;
                $image_position = $image_position + 2;
                // Временная фотография;
//                Zloradnij::printArray($image);
//                Zloradnij::printArray($_FILES);
//                die();
                $image_name_temp = $image;//$_SERVER['DOCUMENT_ROOT'].'/files/uploads/'.$image;
                // Добавление фотографии;
                $im = new GoodsImages();
                $im->good_id = $good_id;
                $im->variation_id = $variation_id;
                $im->hash = md5_file($image_name_temp);
                $im->date = date('Y-m-d H:i:s');
                $im->position = $image_position + 2;
                $im->status = 1;
                if($im->save()){
                    // Добавляем по умол. обложка;
                    $count = GoodsImages::find()->where(['good_id'=>$good_id,'cover' => 1])->count();
                    if(!$count) {
                        $image = GoodsImages::findOne($im->id);
                        $image->cover = 1;
                        $image->save();
                    }
//                    print 'FILE_SAVE_OK';
                }else{
//                    print 'FILE_SAVE_NOT';
                }
                // Директория фотографии;
                $image_dir = $_SERVER['DOCUMENT_ROOT'].'/files/goods/'.(self::getImageFolder($im->id)).'/';
                // Обработка директории фотографии;
                if (!is_dir($image_dir)) mkdir($image_dir);
                // Версии фотографий;
                $image_name = $image_dir.$im->id.'.jpg';
                $image_name_min = $image_dir.$im->id.'_min.jpg';
                $image_name_max = $image_dir.$im->id.'_max.jpg';
                // Перемещение фотографии;
                rename($image_name_temp, $image_name);
                // Копирование фотографии;
                copy($image_name, $image_name);
                copy($image_name, $image_name_min);
                copy($image_name, $image_name_max);
                // Изменение размеров фотографии;
                self::imageResize($image_name, 800, 600);
                self::imageResize($image_name_min, 220, 220);
                self::imageResize($image_name_max, 1024, 600);
            }
        }
    }
}
