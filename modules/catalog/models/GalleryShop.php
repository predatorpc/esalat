<?php

namespace app\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\web\UploadedFile;

class GalleryShop extends Model
{
    /**
    * @var UploadedFile[]
    */
    public $imageFiles;

    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif', 'maxFiles' => 20],
        ];
    }

    public static function getImagePath($good_id,$variation_id){
        $image = GoodsImagesLinks::find()->where(['good_id' => $good_id])->andWhere(['variation_id' => $variation_id])->one();
        if(!$image){
            $image = GoodsImages::find()
                ->where(['good_id' => $good_id])
                //->andWhere(['variation_id' => $variation_id])
                ->one();

            if($image){
                $path = '/files/goods/'.substr(md5($image->id), 0,2).'/'.$image->id;
                $result = [];
                $result['normal'] = $path . '.jpg';
                $result['min'] = $path . '_min.jpg';
                $result['max'] = $path . '_max.jpg';

                $image = $result;
            }
        }else {
            $shopId = (new Query())->
                from('goods')
                ->select('shop_id')
                ->where(['id' => $good_id])
                //->indexBy('shop_id')
                ->one();
            //print '<pre style="display: none;">';print_r($shopId);print '</pre>';
            if($shopId){
                $shopId = $shopId['shop_id'];
            }elseif(isset($image->shop_id)){
                $shopId = $image->shop_id;
            }else{
                $shopId = '123';
            }
            $path = \Yii::$app->params['galleryPath'] . substr(md5($shopId), 0, 2) . '/';
            $result = [];
            $result['normal'] = $path . $shopId . '_' . $image->image_id . '.jpg';
            $result['min'] = $path . $shopId . '_' . $image->image_id . '_min.jpg';
            $result['max'] = $path . $shopId . '_' . $image->image_id . '_max.jpg';

            $image = $result;

            if(file_exists($_SERVER['DOCUMENT_ROOT'] . $result['normal'])){

            }else{
                $image = GoodsImages::find()
                    ->where(['good_id' => $good_id])
                    //->andWhere(['variation_id' => $variation_id])
                    ->one();

                if($image){
                    $path = '/files/goods/'.substr(md5($image->id), 0,2).'/'.$image->id;
                    $result = [];
                    $result['normal'] = $path . '.jpg';
                    $result['min'] = $path . '_min.jpg';
                    $result['max'] = $path . '_max.jpg';

                    $image = $result;
                }
            }
        }

        return $image;
    }

    public static function getImagesPath($idList){
        $result = [];
        $images = ShopsImages::find()->where(['IN','id',$idList])->all();
        foreach($images as $image){
            $path = Yii::$app->params['galleryPath'].substr(md5($image->id), 0, 2).'/';
            $result[$image->id]['normal'] = $path.$image->shop_id.'_'.$image->id.'.jpg';
            $result[$image->id]['min'] = $path.$image->shop_id.'_'.$image->id.'_min.jpg';
            $result[$image->id]['max'] = $path.$image->shop_id.'_'.$image->id.'_max.jpg';
        }

        return $result;
    }

    public function upload()
    {
        $shopId = UserShop::getIdentityShop();
        if ($this->validate()) {
            $filesPath = [];
            $file = $this->imageFiles;
            //print_r($file);
            foreach ($this->imageFiles as $file) {
                $hashShopImage = $shopId . '_' . md5_file($file->tempName);
                $searchImage = ShopsImages::find()->where(['hash' => $hashShopImage])->one();
                if($searchImage){
                    // Этот файл уже загружался
                }else{
                    $shopImages = new ShopsImages();
                    $shopImages->shop_id = $shopId;
                    $shopImages->hash = $hashShopImage;
                    $shopImages->date = date('Y-m-d H:i:s');
                    $shopImages->status = 1;
                    $shopImages->save();

                    // Директория фотографии;
                    $imageDir = $_SERVER['DOCUMENT_ROOT'].Yii::$app->params['galleryPath'].substr(md5($shopImages->id), 0, 2).'/';
                    // Обработка директории фотографии;
                    if (!is_dir($_SERVER['DOCUMENT_ROOT'].Yii::$app->params['galleryPath'])) mkdir($_SERVER['DOCUMENT_ROOT'].Yii::$app->params['galleryPath']);
                    if (!is_dir($imageDir)) mkdir($imageDir);
                    // Версии фотографий;
                    $imageName = $imageDir.$shopId.'_'.$shopImages->id.'.jpg';
                    $imageName_min = $imageDir.$shopId.'_'.$shopImages->id.'_min.jpg';
                    $imageName_max = $imageDir.$shopId.'_'.$shopImages->id.'_max.jpg';
                    // Копирование фотографии;
                    copy($file->tempName, $imageName);
                    copy($file->tempName, $imageName_min);
                    copy($file->tempName, $imageName_max);
                    // Изменение размеров фотографии;
                    $this->imageResize($imageName, $file->extension, Yii::$app->params['galleryImageSizes']['normal'][0], Yii::$app->params['galleryImageSizes']['normal'][1]);
                    $this->imageResize($imageName_min, $file->extension, Yii::$app->params['galleryImageSizes']['small'][0], Yii::$app->params['galleryImageSizes']['small'][1]);
                    $this->imageResize($imageName_max, $file->extension, Yii::$app->params['galleryImageSizes']['big'][0], Yii::$app->params['galleryImageSizes']['big'][1]);

                    $filesPath[] = [
                        'id' => $shopImages->id,
                        'link' => Yii::$app->params['galleryPath'].substr(md5($shopImages->id), 0, 2).'/'.$shopId.'_'.$shopImages->id.'_min.jpg'
                    ];
                }
                unlink($file->tempName);
            }
            return ['status' => 'ok','filesPath' => $filesPath];
        } else {
            return false;
        }
    }

    public static function deleteImage($imageId)
    {
        $shopId = UserShop::getIdentityShop();

        $productImagesLink = GoodsImagesLinks::find()->where(['image_id' => $imageId])->all();

        if($productImagesLink){
            foreach($productImagesLink as $variant){
                $variant->delete();
            }
        }

        ShopsImages::findOne($imageId)->delete();
        $imageDir = $_SERVER['DOCUMENT_ROOT'].Yii::$app->params['galleryPath'].substr(md5($imageId), 0, 2).'/';
        $imageName = $imageDir.$shopId.'_'.$imageId.'.jpg';
        $imageName_min = $imageDir.$shopId.'_'.$imageId.'_min.jpg';
        $imageName_max = $imageDir.$shopId.'_'.$imageId.'_max.jpg';

        unlink($imageName);
        unlink($imageName_min);
        unlink($imageName_max);

        return true;

        //return false;
    }

    protected function imageResize($file,$extension, $width, $height) {
        // Создание изображения на основе исходного файла;
        if (!file_exists($file)) return false;
        // Проверка расширения;
        if (in_array($extension, array('jpg', 'jpeg'))) $source = imagecreatefromjpeg($file);
        elseif (in_array($extension, array('png'))) $source = imagecreatefrompng($file);
        elseif (in_array($extension, array('gif'))) $source = imagecreatefromgif($file);
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
}