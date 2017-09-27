<?php

namespace app\modules\catalog\models;

use app\modules\catalog\models\GoodsVariations;
use app\modules\common\models\Zloradnij;
use Yii;

/**
 * This is the model class for table "lists".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property integer $show_banners
 * @property integer $position
 * @property integer $change
 * @property integer $list_type
 * @property integer $level
 * @property string $date_create
 * @property string $date_update
 * @property integer $private
 * @property integer $status
 *
 * @property Users $user
 * @property ListsChains[] $listsChains
 * @property ListsGoods[] $listsGoods
 * @property ListsTags[] $listsTags
 */
class Lists extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lists';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'list_type'], 'required'],
            [['user_id', 'show_banners', 'position', 'change', 'list_type', 'level', 'status','private'], 'integer'],
            [['description'], 'string'],
            [['date_create', 'date_update'], 'safe'],
            [['title', 'image'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\modules\common\models\User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('admin','ID Пользователя'),
            'title' => Yii::t('admin','Название'),
            'description' => Yii::t('admin','Описание'),
            'image' => Yii::t('admin','Картинка'),
            'show_banners' => Yii::t('admin','Показ баннера'),
            'position' => Yii::t('admin','Сортировка'),
            'change' => 'Change',
            'list_type' => Yii::t('admin','Тип списка'),
            'level' => Yii::t('admin','Уровень'),
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
            'status' => Yii::t('admin','Статус'),
            'private' => Yii::t('admin','Персональный'),
        ];
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
    public function getListsChains()
    {
        return $this->hasMany(ListsChains::className(), ['list_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getListsGoods()
    {
        return $this->hasMany(ListsGoods::className(), ['list_id' => 'id'])->where(['lists_goods.status' => 1])->orderBy(['sort'=>'DESC']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getListsTags()
    {
        return $this->hasMany(ListsTags::className(), ['list_id' => 'id']);
    }

    public function getFullPrice(){
        $fullPrice = 0;
        if(!$this->listsGoods){

        }else{
            foreach ($this->listsGoods as $listsGood) {
                if(!empty($listsGood->product) && $listsGood->product->checkPay){
                    $fullPrice += $listsGood->variant->priceValue * $listsGood->amount;
                }
            }
        }
        return $fullPrice;
    }

    public function removeOldProducts(){
        if(!empty($this->listsGoods)){
            foreach ($this->listsGoods as $listsGood) {
                if(!$listsGood->variant->product->checkPay){
                    $listsGood->status = 0;
                    $listsGood->save();
                }
            }
        }
    }

    public function setSessionList(){
        $productListForSession = [];
        foreach($this->listsGoods as $product){
            if(!empty($product->variant->product->category->parent->title)) {
                $productListForSession[$product->variant->product->category->parent->title . ' / ' . $product->variant->product->category->title][] = $product;
            }
        }
        foreach($productListForSession as $categoryName => $categoryProducts){
            foreach ($categoryProducts as $product) {
                $_SESSION['catalog']['product-list'][$this->id][$categoryName][$product->variation_id] = $product->amount;
            }
        }
    }

    public static function updateSessionList($listId,$list){

        $_SESSION['catalog']['product-list'][$listId] = [];

        foreach($list as $categoryName => $categoryProducts){
            foreach ($categoryProducts as $product => $count) {
                $_SESSION['catalog']['product-list'][$listId][$categoryName][$product] = $count;
            }
        }
    }

    public function getSessionList(){
        $productsResult = $productIds = $disableVariants = [];
        $products = \Yii::$app->session['catalog']['product-list'][$this->id];
        if(!empty($products)){
            foreach ($products as $categoryName => $categoryProducts) {
                foreach ($categoryProducts as $id => $count){
                    $productIds[] = $id;
                }
            }
            $variants = GoodsVariations::find()->where(['IN','id',$productIds])->all();

            if(!empty($variants)){
                foreach ($variants as $i => $variant) {
                    if(!$variant->product->checkPay){
                        $disableVariants[] = $variant->id;
                    }
                }
            }
            foreach ($products as $categoryName => $categoryProducts) {
                foreach ($categoryProducts as $id => $count){
                    if(!in_array($id,$disableVariants)){
                        $productIds[] = $id;
                        $productsResult[$categoryName][] =  new ListsGoods([
                            'variation_id' => $id,
                            'amount' => $count,
                            'list_id' => $this->id,
                        ]);
                    }else{
                        unset($_SESSION['catalog']['product-list'][$this->id][$categoryName][$id]);
                    }
                }
            }
        }

        return $productsResult;
    }

    public function getPriceDiscount(){
        $result = 0;
        if(!Yii::$app->basket->emptyBasket()){
            foreach(Yii::$app->basket->getBasketProducts() as $product){
                $result += $product['priceDiscount'] * $product['count'];
            }
        }
        return $result;
    }
}