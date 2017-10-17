<?php

namespace app\modules\catalog\models;

use andreykluev\shopbasket\behaviors\BasketUserBehavior;
use yii\helpers\ArrayHelper;
use app\modules\common\models\User;
use app\modules\common\models\UsersLogs;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsStores;
use app\modules\managment\models\ShopStoresTimetable;
use Yii;

/**
 * This is the model class for table "goods_variations".
 *
 * @property integer $id
 * @property integer $good_id
 * @property string $code
 * @property string $full_name
 * @property string $name
 * @property string $description
 * @property string $price
 * @property string $comission
 * @property integer $status
 */
class GoodsVariations extends \app\modules\common\models\UpdateLogs //\app\modules\common\models\ActiveRecordRelation
{
    public $producer_name;
    public $price_out;
    public $date_create;
    public $confirm;
    public $count;
    public $tags_name;

    public $productId;
    public $variantId;
    public $productPrice;
    public $productCommission;
    public $productDiscount;
    public $countPack;
    public $commissionId;
    public $categoryId;
    public $bonus = 0;
    public $amount = 0;
    public $sort = 0;

    public $show=0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_variations';
    }

    public function behaviors()
    {
        return [

        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['good_id', 'status','count','confirm', 'servingforday'], 'integer'],
            [['description'], 'string'],
            [['price', 'comission','price_out'], 'number'],
            [['code', 'full_name', 'name'], 'string', 'max' => 128],
            [['categoryId','productId','variantId','productPrice','productCommission','productDiscount','countPack','commissionId','date_create'], 'safe'],
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
            'code' => Yii::t('admin', 'Артикул'),
            'servingforday' => Yii::t('admin', 'На сколько дней хватит'),
            'full_name' => Yii::t('admin', 'Поставщик'),
            'name' => Yii::t('admin', 'Название'),
            'description' => Yii::t('admin', 'Описание'),
            'price' => Yii::t('admin', 'Цена'),
            'comission' => Yii::t('admin', 'Комиссия'),
            'status' => Yii::t('admin', 'Активность'),
            'confirm' => Yii::t('admin', 'Модерация'),
            'producer_name' => Yii::t('admin', 'Производитель'),
            'price_out' => Yii::t('admin', 'Цена'),
            'date_create' => Yii::t('admin', 'Дата создания'),
            'count' => Yii::t('admin', 'Количество'),
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    public function getTags(){
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable('tags_links', ['variation_id' => 'id']);
    }

    public function getRelatedProducts(){
        $tags = Tags::find()
            ->from('tags, tags_links')
            ->where(['tags.group_id'=>Tags::RELATED_PRODUCT])
            ->andWhere(['tags_links.variation_id'=>$this->id])
            ->andWhere('tags_links.tag_id=tags.id')->all();
        $result = [];
        foreach ($tags as $tag){
            $result[]=$tag->value;
        }
        return $result;
    }

    public function getWeight(){
        $weight = $this->hasOne(Tags::className(), ['id' => 'tag_id'])
            ->viaTable('tags_links', ['variation_id' => 'id'])->where(['group_id'=>1014])->one();
        if(empty($weight)){
            $weight = [];
        }
        else{
            $razmernost = $weight->value;
            $razmernost = trim(preg_replace('/([.]*[~\d+,.\d+\s])/', '', $razmernost));
            //$razmernost = trim(preg_replace('/((\d+(\.|,|.)\d*))/', '', $razmernost));
            //var_dump($razmernost);
            if(strcmp($razmernost, 'кг' )==0){
                //$weight->value = floatval(preg_replace('/[^((\d+(\.|,)\d*))]/', '', $weight->value))*1000;
                $weight->value = floatval(str_replace(',','.',preg_replace('/([^0-9,.]+[^0-9]*)/', '', $weight->value)))*1000;
                //$weight->value = floatval(preg_replace('/([^0-9,.]+[^0-9]*)/', '', $weight->value))*1000;
            }
            else{
                //$weight->value = floatval(preg_replace('/([^0-9,.]+[^0-9]*)/', '', $weight->value));
                $weight->value = floatval(str_replace(',','.', preg_replace('/([^0-9,.]+[^0-9]*)/', '', $weight->value)));
            }

        }
        return $weight;

    }

    public function getPriceValueWithOutBonus(){
        return $this->price_out;
    }

    public function setPriceValue(){
        $this->price_out = $this->product->shop->comission_id == 1001 ?
            ceil($this->price * $this->product->count_pack) :
            ceil(($this->price + $this->price * $this->comission / 100) * $this->product->count_pack);

        $this->price_out -= $this->bonus;
    }

    public function getPriceValue(){
        return $this->product->shop->comission_id == 1001 ?
            ceil($this->price * $this->product->count_pack) :
            ceil(($this->price + $this->price * $this->comission / 100) * $this->product->count_pack);
    }

    public function getDiscount(){

    }

    public function getCountPack(){
        return $this->product->count_pack;
    }

    public function getDiscountPrice($percent = false){
        if(Yii::$app->user->identity && $this->product->discount == 1 && !empty($percent)){
//            return floor(($this->price_out) * (100 - User::findOne(Yii::$app->user->identity->getId())->discount)/100);
            return floor(($this->price_out) * (100 - $percent)/100);
        }else{
            return floor($this->price_out);
        }
    }

    public function getCommissionValue($discountPercent = 0){
//        $a = [
//            'comissionId' => $this->product->shop->comission_id,
//            'price' => $this->price,
//            'count_pack' => $this->product->count_pack,
//            'comission' => $this->comission,
//        ];
//        Zloradnij::print_arr($a);
        if($this->product->shop->comission_id == 1001){
            return round(ceil($this->price * $this->product->count_pack) - ($this->price * $this->product->count_pack * (1 - $this->comission / 100)), 2);
        }
        if($this->product->shop->comission_id == 1002){
            return round(ceil(($this->price + $this->price * $this->comission / 100) * $this->product->count_pack) - ($this->price * $this->product->count_pack) - ($this->priceValue - $this->bonus - $this->getDiscountPrice($discountPercent)), 2);
        }

    }

    public function getImage(){
        return GoodsImages::find()->where(['variation_id' => $this->id])->orderBy('position')->one();
    }

    public function getImagePath(){
        return $this->image ? Yii::$app->params['galleryPath']['old'] . '' . Goods::image_dir($this->image->id) . '/' . $this->image->id . '.jpg' : false;
    }

    public function getTitleWithProperties(){
        return self::find()->where(['id' => $this->id])->select(['get_tags(id) AS titleWithProperties'])->scalar();
    }

    public function getTitleWithPropertiesForCatalog(){
        return self::find()->where(['id' => $this->id])->select(['get_tags_catalog(id) AS titleWithProperties'])->scalar();
    }

    public function getMaxCount(){
        return GoodsCounts::find()->where(['variation_id' => $this->id])->min('count');//->orderBy('count')->select('count')->scalar();
    }

    public function getMaxCounts(){
        return GoodsCounts::find()->where(['variation_id' => $this->id])->all();
    }

    public function getPropertyGroups(){
        return TagsGroups::find()
            ->leftJoin('tags','tags.group_id = tags_groups.id')
            ->leftJoin('tags_links','tags_links.tag_id = tags.id')
            ->where([
                'tags_links.variation_id' => $this->id,
                'tags_groups.status' => 1,
                'tags_groups.show' => 1,
                'tags_groups.type' => 1,
            ])
            ->orderBy('tags_groups.id')
            ->all();
    }

    public function getProperties(){
        return Tags::find()
            ->leftJoin('tags_links','tags_links.tag_id = tags.id')
            ->where([
                'tags_links.variation_id' => $this->id,
            ])
            ->all();
    }

    public function getPropertiesIndexed(){
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable('tags_links', ['variation_id' => 'id'])
            ->indexBy('id');
    }

    public function setBonusValue($bonus){
        $this->bonus = $bonus;
    }

    public function getPropertiesFrontVisible(){
        return Tags::find()
            ->leftJoin('tags_groups','tags.group_id = tags_groups.id')
            ->leftJoin('tags_links','tags_links.tag_id = tags.id')
            ->where([
                'tags_links.variation_id' => $this->id,
                'tags_groups.status' => 1,
                'tags_groups.show' => 1,
                'tags_groups.type' => 1,
                'tags.status' => 1,
            ])
            ->orderBy('position')
            ->all();

//        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
//            ->leftJoin('tags_groups','tags.group_id = tags_groups.id')
//            ->orderBy('tags_groups.id')
//            ->viaTable('tags_links', ['variation_id' => 'id'])
//            ->where([
//                'tags_groups.status' => 1,
//                'tags_groups.show' => 1,
//                'tags_groups.type' => 1,
//                'tags.status' => 1,
//            ]);
    }

    public function getCountRows(){
        return GoodsCounts::find()->where(['variation_id' => $this->id])->all();
    }

    public function getCountOnStores(){
        return $this->hasMany(GoodsCounts::className(),['variation_id' => 'id']);
    }
    public function getCountsVariation(){
        return $this->hasOne(GoodsCounts::className(),['variation_id' => 'id'])->select('good_id, variation_id, sum(count) as count')->groupBy('good_id, variation_id');
    }

    public function getStore(){
        return $this->hasOne(ShopsStores::className(),['id' => 'store_id'])->via('countOnStores');
    }

    public function getDateOfAvailible($storeId){
        /*
        $minday = strtotime("midnight"); //начало текущего дня
//        if($minday == strtotime('midnight') && date('H')>=8){
//            $minday = $minday + 60*60*24;
//        }
        $startTimestampWeek = strtotime("midnight") - (date("N")) * 60 * 60 * 24; //По умолчанию работаем с текущей неделей
        $storeTimetable = ShopStoresTimetable::find()->where(['store_id' => $storeId, 'status' => 1])->All();// Рассписание работы склада
        if ($storeTimetable) {
            //echo 'lastweek';
            if (ShopStoresTimetable::find()->where(['store_id' => $storeId, 'status' => 1])->max('day') <= date("N")) { //Если максимальный день работы склада меньше текущего дня, работаем со след.недели
                //echo 'nextweek';
                $startTimestampWeek = $startTimestampWeek + 7 * 24 * 60 * 60;
            }
            foreach ($storeTimetable as $workDay) {//Перебираем рабочие дни недели
                //echo $workDay->day.'/';
                $checkingDay = $startTimestampWeek + $workDay->day * 60 * 60 * 24; //Вычислем timestamp начала рабочего склада
                if(strtotime('now') > strtotime(date('Y-m-d '.$workDay->time_end.':00')) && $workDay->day == date('N') && $minday == strtotime('midnight')){
                    //echo '<br>fucking time 1<br>';
                    $minday = $minday + 24*60*60*2;
                }elseif(strtotime('now') <  strtotime(date('Y-m-d '.$workDay->time_begin.':00')) && $workDay->day == date('N') && $minday == strtotime('midnight')){
                    //echo '<br>fucking time 2<br>';
                    $minday = $minday + 24*60*60;
                }
                if ($checkingDay >= $minday) {//если рабочий день магазина совпадает или больше текущего минимального дня, то заменяем на проверяемую дату
                    $minday = $checkingDay;
                    if($minday == strtotime('midnight') && date('H')>=8){
                        $minday = $minday + 24*60*60;
                    }
                    break;
                }
            }
            //echo date('d-m-Y H-i-s',$minday).'|'.date('N',$minday);

            return ($minday - strtotime("midnight"))/60/60/24;
            //echo ($minday - strtotime("midnight"))/60/60/24;
        }
        */
        if(Date('H')>20){
            $result=1;
        }
        else{
            $result=0;
        }
        return $result;

    }

    public function getShop(){
        return $this->hasOne(Shops::className(),['id' => 'shop_id'])->via('store');
    }

    public function getStoreIds(){
        return $this->hasMany(ShopsStores::className(),['variation_id' => 'id'])
            ->where([''])
            ->via(GoodsCounts::tableName(),['store_id' => 'id']);
    }

    public function setStatusEmptyCount(){
        $counts = $this->countRows;
        if(!empty($counts)){
            foreach ($counts as $count) {
                $count->count = 0;
                $count->save();
            }
        }
        $this->status = 0;
        $this->save();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        // Если вариацию отключили
        if(!$insert && (!empty($changedAttributes['status']) && $changedAttributes['status'] == 1 && $this->status == 0)){
            $product = $this->product;
            if(!$product->variations){

            }else{
                $statusCount = false;
                foreach ($product->variations as $variant) {
                    if($variant->status == 1){
                        $statusCount = true;
                    }
                }
                if(!$statusCount){
                    $product->status = 0;
                    $product->save();
                }
            }
        }

        if($this->status == 1){
            if(!empty($this->product->storeListFull)){
                $storesIds = [];
                foreach ($this->product->storeListFull as $item) {
                    $storesIds[] = $item->id;
                }
                $counts = $this->countRows;
                if(!empty($counts)){
                    foreach ($counts as $count) {
                        if(in_array($count->store_id,$storesIds)){

                        }else{
                            $count->delete();
                        }
                    }
                }
            }
        }
        /*if ($insert && $this->status == 1) {
            $model = new UsersLogs();
            $model->user_id = (\Yii::$app->user->id) ? \Yii::$app->user->id : '';
            $model->good_id = $this->product;
            $model->variations_id = $this->id;
            $model->type = 1;
            $model->save();
        }
        elseif (!$insert) {
            $model = new UsersLogs();
            $model->user_id = (\Yii::$app->user->id) ? \Yii::$app->user->id : '';
            $model->good_id = $this->product;
            $model->variations_id = $this->id;
            $model->type = 2;
            $model->save();
        }*/
        if ($insert) {

            $product = $this->product;
            $now_time = time() - 300;
            $variant_arr = '';
            if ($product->variations) {
                foreach ($product->variations as $variant) {
                    if (strtotime($variant->created_at) > $now_time) {
                        $good_id = $variant->good_id;
                        $var_id = $variant->id;
                    }
                }
            }

            if (!empty($good_id) && !empty($var_id)) {
                $model = new UsersLogs();
                $model->user_id = (\Yii::$app->user->id) ? \Yii::$app->user->id : '';
                $model->good_id = $good_id;
                $model->variations_id = $var_id;
                $model->type = 1;
                $model->save();
            }

        }
        // update
        /*elseif (!$insert) {

            $product = $this->product;
            $now_time = time() - 300;
            $variant_arr = '';
            if ($product->variations) {
                foreach ($product->variations as $variant) {
                    if (strtotime($variant->updated_at) > $now_time) {
                        $good_id = $variant->good_id;
                        $var_id = $variant->id;
                    }
                }
            }
            if (!empty($good_id) && !empty($var_id)) {
                $model = new UsersLogs();
                $model->user_id = (\Yii::$app->user->id) ? \Yii::$app->user->id : '';
                $model->good_id = $good_id;
                $model->variations_id = $var_id;
                $model->type = 2;
                $model->save();
            }
        }*/
    }
}
