<?php

namespace app\modules\my\models;
use app\modules\common\models\User;
use yii\base\Model;
use yii\web\UploadedFile;
use app\modules\my\models\MessagesImages;
use app\modules\catalog\models\GoodsImages;
use Yii;
/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property integer $type_id
 * @property integer $group_id
 * @property integer $user_id
 * @property string $name
 * @property string $topic
 * @property string $order
 * @property string $phone
 * @property string $text
 * @property string $answer
 * @property string $date
 * @property integer $show
 * @property integer $active
 * @property integer $rating
 * @property integer $status
 *
 * @property MessagesTypes $type
 * @property Users $user
 * @property MessagesTest $group
 * @property MessagesTest[] $messagesTests
 * @property MessagesImages[] $messagesImages
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }
    /**
     * @var UploadedFile[]
     */
    public $imageFiles;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'group_id', 'user_id', 'show', 'status','active','rating','order'], 'integer'],
            [['topic','answer','text'], 'string'],
            [['order'], 'required', 'message' => Yii::t('app','Введите номер заказа!')],
            ['user_id', 'default', 'value' => (!empty(Yii::$app->user->id) ? \Yii::$app->user->id : 'NULL')],
            ['type_id', 'default', 'value' => 1002],
            ['name', 'default', 'value' => (!empty(Yii::$app->user->identity->name) ? \Yii::$app->user->identity->name : 'Null')],
            ['answer','default', 'value' => ''],
            [['text'], 'required', 'message' => Yii::t('app','Введите текст!')],
            ['date', 'default', 'value' => date('Y-m-d')],
            ['status', 'default', 'value' => 0],
            ['active', 'default', 'value' => 0],
            [['imageFiles'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 4],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MessagesTypes::className(), 'targetAttribute' => ['type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Feedback::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'group_id' => 'Group ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'topic' => 'Topic',
            'order' => 'Order',
            'phone' => 'Phone',
            'text' => 'Text',
            'answer' => 'Answer',
            'date' => 'Date',
            'show' => 'Show',
            'active' => 'Active',
            'rating' => 'Rating',
            'status' => 'Status',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(MessagesTypes::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Feedback::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessagesTests()
    {
        return $this->hasMany(Feedback::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessagesImages()
    {
        return $this->hasMany(MessagesImages::className(), ['message_id' => 'id']);
    }

    // Загрузка файл;
    public function upload($message_id = false)
    {
        if ($this->validate()) {
            foreach ($this->imageFiles as $file) {
                $type = explode('/',$file->type);
                $type = end($type);
                $type = str_replace('jpeg','jpg',$type);
                $images = new MessagesImages();
                $images->message_id = $message_id;
                $images->hash = '';
                $images->exp = $type;
                $images->status = 1;
                $images->save();
                $fileName = $_SERVER['DOCUMENT_ROOT'].'/files/images/' . (!empty($images->id) ? $images->id : $file->baseName) . '.' . $file->extension;
                // Загружаем на сервер;
                $file->saveAs($fileName);
                // Обработка изображения;
                if($file->extension == 'jpg') {
                    self::loadPhoto($fileName);
                }
            }
            return true;
        } else {
            return false;
        }
    }
    // Чтение файла для записи в базу данных;
    public static function loadPhoto($upload,$width = 1024,$height =768) {

        // определим коэффициент сжатия изображения, которое будем генерить
        $ratio = $width / $height;
        // получим размеры исходного изображения
        $size_img = getimagesize($upload);
        // получим коэффициент сжатия исходного изображения
        $src_ratio=$size_img[0] / $size_img[1];
        // пропорции исходного изображения
        if ($ratio > $src_ratio) {
            $height = $width / $src_ratio;
        } else {
            $width = $height * $src_ratio;
        }
        // создадим пустое изображение по заданным размерам
        $dest_img = imagecreatetruecolor($width, $height);
        // создаем jpeg из файла
        $src_img = imagecreatefromjpeg($upload);
        // масштабируем изображение     функцией imagecopyresampled()
        // $dest_img - уменьшенная копия
        // $src_img - исходной изображение
        // $width - ширина уменьшенной копии
        // $height - высота уменьшенной копии
        // $size_img[0] - ширина исходного изображения
        // $size_img[1] - высота исходного изображения
        imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $width, $height, $size_img[0], $size_img[1]);
        // сохраняем уменьшенную копию в файл
        imagejpeg($dest_img, $upload,100);

        // чистим память от созданных изображений
        imagedestroy($dest_img);
        imagedestroy($src_img);
        return true;
    }
    //Количество не обработанный заявки;
    public static function getCountSupport()
    {
        return Feedback::find()->where(['type_id'=>1002,'status'=>0])->count();
    }
    public static function getCountCall()
    {
        return Feedback::find()->where(['type_id'=>1000,'status'=>0])->count();
    }
    public static function getCountFeed()
    {
        return Feedback::find()->where(['type_id'=>1003,'status'=>0])->count();
    }

}
