<?php

namespace app\modules\catalog\models;

use app\modules\common\models\UsersLogs;
use app\modules\managment\models\ShopGroup;
use app\modules\managment\models\ShopGroupVariantLink;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsStores;

use app\modules\wishlist\models\WishlistProducts;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property integer $type_id
 * @property integer $shop_id
 * @property integer $producer_id
 * @property integer $country_id
 * @property integer $weight_id
 * @property string $code
 * @property string $full_name
 * @property string $name
 * @property string $description
 * @property string $price
 * @property string $comission
 * @property integer $bonus
 * @property integer $order
 * @property integer $delay
 * @property integer $count_pack
 * @property integer $count_min
 * @property integer $rating
 * @property integer $discount
 * @property integer $main
 * @property integer $new
 * @property integer $sale
 * @property string $link
 * @property string $date
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property integer $user_id
 * @property integer $user_last_update
 * @property string $date_create
 * @property string $date_update
 * @property integer $position
 * @property integer $s
 * @property integer $confirm
 * @property integer $count_buy
 * @property integer $status
 */
class Goods extends \app\modules\common\models\UpdateLogs
{
    public $producer_name;
    public $price_out;
    public $productImage;
    //public $discount;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['show','type_id', 'shop_id', 'producer_id', 'country_id', 'weight_id', 'bonus', 'order', 'delay', 'count_pack', 'count_min', 'rating', 'discount', 'main', 'new', 'sale', 'user_id', 'user_last_update', 'position', 's', 'confirm','master_active','count_buy','type', 'status','iwish','hit'], 'integer'],
            [['description','color_bg'], 'string'],
            [['comission'], 'number'],
            [['code', 'full_name', 'name', 'link', 'seo_title', 'seo_description', 'seo_keywords'], 'string', 'max' => 128],
            [['producer_name'], 'string', 'max' => 128],
            [['price_out'], 'number'],
            [['productImage','date', 'date_create', 'date_update'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => Yii::t('admin', 'Тип'),
            'shop_id' => 'Shop ID',
            'producer_id' => Yii::t('admin','Производитель'),
            'color_bg'=> Yii::t('admin','Задать цвет товара'),
            'country_id' => Yii::t('admin','Страна'),
            'weight_id' => Yii::t('admin','Вес'),
            'code' => Yii::t('admin','Артикул'),
            'full_name' => Yii::t('admin','Название поставщика'),
            'name' => Yii::t('admin','Название'),
            'description' => Yii::t('admin','Описание'),
            'price' => Yii::t('admin','Цена'),
            'comission' => 'Комиссия',
            'bonus' => Yii::t('admin','Бонус'),
            'order' => Yii::t('admin','Заказ'),
            'delay' => Yii::t('admin','Задержка'),
            'count_pack' => Yii::t('admin','Количество в упаковке'),
            'count_min' => Yii::t('admin','Минимальное количество'),
            'rating' => Yii::t('admin','Рейтинг'),
            'discount' => Yii::t('admin','Скидка'),
            'main' => Yii::t('admin','На главной'),
            'new' => Yii::t('admin','Новинка'),
            'sale' => Yii::t('admin','Акция'),
            'link' => Yii::t('admin','Ссылка'),
            'date' => Yii::t('admin','Дата'),
            'seo_title' => 'Seo Title',
            'seo_description' => 'Seo Description',
            'seo_keywords' => 'Seo Keywords',
            'user_id' => 'User ID',
            'user_last_update' => 'User Last Update',
            'date_create' => Yii::t('admin','Дата создания'),
            'date_update' => Yii::t('admin','Дата обновления'),
            'position' => Yii::t('admin','Сортировка'),
            's' => 'S',
            'confirm' => Yii::t('admin','Одобрен'),
            'status' => Yii::t('admin','Статус'),
            'show' => Yii::t('admin','Показывать на сайте'),
            //'producer_name' => 'Producer Name',
            'iwish' => Yii::t('admin','Я ХОЧУ'),
            'hit' => Yii::t('admin','Хит'),
        ];
    }

    public function  getWishListGood(){
        return $this->hasOne(WishlistProducts::className(), ['product_id' => 'id'])->where(['user_id'=>Yii::$app->user->id,'status'=>1]);
    }
    public function getgoods_variations()
    {
        return $this->hasMany(GoodsVariations::className(), ['good_id' => 'id']);
    }

    public function getVariations()
    {
        return $this->hasMany(GoodsVariations::className(), ['good_id' => 'id']);
    }
    public function getGoodsComments()
    {
        return $this->hasMany(GoodsComments::className(), ['good_id' => 'id'])->where(['status' => 1]);
    }
    public function getVariationsCatalog()
    {
        return $this->hasMany(GoodsVariations::className(), ['good_id' => 'id'])->where(['status' => 1]);
    }

    public function getStickerLinks()
    {
        return $this->hasMany(StickerLinks::className(), ['good_id' => 'id'])->where(['status' => 1]);
    }

    // Обработка комиссии;
    public static function getGoodComission($good_id, $variation_id = false, $discount = 0) {
        // Загрузка данных товара;
        $good = (new Query())->from('goods')
            ->select([
                'goods.id',
                'goods.shop_id',
                'goods.count_min',
                'goods.count_pack',
                'shops.comission_id',
            ])
            ->leftJoin('shops','shops.id = goods.shop_id')
            ->where(['goods.id' => $good_id])
            ->one();

        if (isset($good) && !empty($good)) {
            // Загрузка варианта товара;

            // Загрузка варианта товара;
            $variation = (new Query())->from('goods_variations')
                ->select([
                    'price',
                    'comission',
                ])
                ->where(['good_id' => $good_id])
                ->andWhere(['status' => 1]);

            if(isset($variation_id) && !empty($variation_id)){
                $variation->andWhere(['id' => $variation_id]);
            }
            $variation = $variation->one();

            if ($variation) {
                // Обновление цены и комиссии;
                $good['price'] = $variation['price'];
                $good['comission'] = $variation['comission'];
            }
            // Проверка комиссии и рассчет цены;
            if ($good['comission_id'] == 1001) $good['comission'] = round(ceil($good['price'] * $good['count_pack']) - ($good['price'] * $good['count_pack'] * (1 - $good['comission'] / 100)), 2);
            if ($good['comission_id'] == 1002) $good['comission'] = round(ceil(($good['price'] + $good['price'] * $good['comission'] / 100) * $good['count_pack']) - ($good['price'] * $good['count_pack']) - $discount, 2);
            // Вывод цены;
            return $good['comission'];
        }
        return false;
    }

    //------------------------------ PRICE -----------------------------------
    // Вариация товара с минимальной ценой
    public function getVariantWithMinimalPrice(){
        $variantWithMinimalPrice = GoodsVariations::find()->where(['good_id' => $this->id,'status' => 1])->orderBy('price ASC')->one();
        if(!$variantWithMinimalPrice){
            $this->status = 0;
            $this->save();
            return false;
        }else{
            return $variantWithMinimalPrice;
        }
    }

    // Минимальная цена товара
    public function getPriceVariant(){
        $variantWithMinimalPrice = $this->variantWithMinimalPrice;
        if(!$variantWithMinimalPrice){
            return false;
        }else{
            return $this->comissionId == 1001 ?
                ceil($variantWithMinimalPrice->price * $this->count_pack) :
                ceil(($variantWithMinimalPrice->price + $variantWithMinimalPrice->price * $variantWithMinimalPrice->comission / 100) * $this->count_pack);
        }
    }

    // Минимальная цена товара со скидкой
    public function getPriceVariantDiscount(){
        return floor($this->priceVariant * 0.95);
    }

    public function getComissionId(){
        return ShopGroup::find()
            ->select('comission_id')
            ->leftJoin('shop_group_variant_link','shop_group_variant_link.shop_group_id = shop_group.id')
            ->where(['shop_group_variant_link.product_id' => $this->id])
            ->scalar();
    }
    //------------------------------ PRICE END -----------------------------------

    // Обработка комиссии
    public static function getProductCommission($id, $variantId = false, $discount = 0,$data =[]) {
        if(empty($data)){
            $data = (new Query())
                ->from('goods_variations')
                ->select([
                    'goods_variations.price as productPrice',
                    'goods_variations.comission as productCommission',
                    'goods_variations.id as variantId',

                    'goods.discount as productDiscount',
                    'goods.count_pack as countPack',
                    'shops.comission_id as commissionId',
                ])
                ->leftJoin('goods','goods.id = goods_variations.good_id')
                ->leftJoin('shops','goods.shop_id = shops.id')
                ->where([
                    'goods_variations.status' => 1,
                    'goods_variations.good_id' => $id,
                ]);

            if(!$variantId){
                $data->limit(1);
            }else{
                $data->andWhere(['goods_variations.id' => $variantId]);
            }
            $data = $data->one();
        }

        // Проверка комиссии и рассчет цены;
        if ($data['commissionId'] == 1001){
            $data['commission'] = round(ceil($data['productPrice'] * $data['countPack']) - ($data['productPrice'] * $data['countPack'] * (1 - $data['commission'] / 100)), 2);
        }
        if ($data['commissionId'] == 1002){
            $data['commission'] = round(ceil(($data['productPrice'] + $data['productPrice'] * $data['commission'] / 100) * $data['countPack']) - ($data['productPrice'] * $data['countPack']) - $discount, 2);
        }
        // Вывод цены;
        return $data['commission'];
    }

    public static function findProductImage($id, $exp = false){
        // Если обложка выбран то показываем;
        if(GoodsImages::find()->where(['good_id' => $id, 'cover'=>1,'status' => 1])->count() > 0) {
            $productImage = GoodsImages::find()->where(['good_id' => $id,'cover'=>1,'status' => 1])->orderBy('position')->one();
        }else{
            $productImage = GoodsImages::find()->where(['good_id' => $id, 'status' => 1])->orderBy('position')->one();
        }

        $jpg = ($exp == 'min' ? '_min.jpg' : ($exp == 'max' ? '_max.jpg' : '.jpg'));
        if(!$productImage){
            // Если обложка выбран то показываем;
            if(GoodsImages::find()->where(['good_id' => $id, 'cover'=>1,'status' => 1])->count() > 0) {
                $productImage = GoodsImages::find()->where(['good_id' => $id,'cover'=>1,'status' => 1])->orderBy('position')->one();
            }else{
                $productImage = GoodsImages::find()->where(['good_id' => $id, 'status' => 1])->orderBy('position')->one();
            }

            if(!$productImage){
                return '/good.jpg';
            }else{
                return Yii::$app->params['galleryPath']['new'] . self::image_dir($productImage->image_id) . '/' . $productImage->image_id . $jpg;
            }
        }else{
            return Yii::$app->params['galleryPath']['old'] . self::image_dir($productImage->id) . '/' . $productImage->id . $jpg;
        }
        return false;
    }
    public static function findProductImages($ids){
        $result = [];
        $productImages = GoodsImagesLinks::find()->where(['IN','good_id',$ids])->andWhere(['status' => 1])->groupBy('good_id')->orderBy('position')->all();
        if(!$productImages){
            $productImages = GoodsImages::find()
                ->where(['IN','good_id',$ids])
                ->all();

            if($productImages){
                foreach($productImages as $image){
                    $path = '/files/goods/'.substr(md5($image->id), 0,2).'/'.$image->id;

                    $result[$image->good_id][] = $path . '_min.jpg';
                }

                return $result;
            }
        }else {
            return false;
        }


        /*
        $result = [];
        $productImages = GoodsImages::find()->where(['IN','good_id',$ids])->andWhere(['status' => 1])->groupBy('good_id')->orderBy('position')->all();
        if(!$productImages){

        }else{
            foreach($productImages as $image){
                $result[$image->good_id][] = $image->id;
            }
            return $result;
        }
        return false;
        */
    }

    public static function image_dir($image_id) {
        return substr(md5($image_id), 0, 2);
    }

    // Обработка наклеек;
    public static function findProductStickers($ids,$data = false) {
        $result = [];
        $products = Goods::find()->where(['IN','id',$ids])->all();

        if(!$products){

        }else{
            foreach($products as $product){
                $result[$product->id] = [];
                if(isset($product->discount) && !empty($product->discount)){
                    $result[$product->id]['discount'] = true; // Добавление наклейки (акция);
                }
                // Проверка даты добавления товара (не позднее двух недель назад);
                if (strtotime($product->date_create) > (time() - 86400 * 14)) {
                    $result[$product->id]['news'] = true; // Добавление наклейки (новинка);
                }
                // Проверка товара на продажу за бонусы;
                if(isset($product->bonus) && !empty($product->bonus)){
                    $result['bonus'] = true; // Добавление наклейки (бонусы);
                }
            }
            return $result;
        }

        return false;
    }
    /*
        // Загрузка опция вариантов цвет 1027;
        function good_variations_color($good_id) {
            global $db;
            $tags = array();
            // Загрузка групп вариантов;
            $sql = "SELECT `tags_groups`.`id`, `tags_groups`.`name`, `tags_links`.`variation_id` FROM `goods_variations` LEFT JOIN `tags_links` ON `tags_links`.`variation_id` = `goods_variations`.`id` LEFT JOIN `tags` ON `tags`.`id` = `tags_links`.`tag_id` LEFT JOIN `tags_groups` ON `tags_groups`.`id` = `tags`.`group_id` WHERE  `goods_variations`.`good_id` = '".$good_id."' AND `goods_variations`.`status` = '1' AND `tags`.`status` = '1' AND `tags_links`.`status` = '1' AND `tags_groups`.`type` = '1' AND `tags_groups`.`status` = '1' GROUP BY `tags_groups`.`id` ORDER BY `tags_groups`.`position` ASC";
            if ($tags = $db->all($sql)) {
                foreach ($tags as $i=>$tag_group) {
                    // Загрузка вариантов;
                    $sql = "SELECT `tags`.`id`, `tags`.`value` FROM `goods_variations` LEFT JOIN `tags_links` ON `tags_links`.`variation_id` = `goods_variations`.`id` LEFT JOIN `tags` ON `tags`.`id` = `tags_links`.`tag_id` LEFT JOIN `tags_groups` ON `tags_groups`.`id` = `tags`.`group_id`  WHERE `goods_variations`.`good_id` = '".$good_id."' AND `tags_groups`.`id` = '".$tag_group['id']."' AND `goods_variations`.`status` = '1' AND `tags`.`status` = '1' AND `tags_links`.`status` = '1' AND `tags_groups`.`type` = '1' AND `tags_groups`.`status` = '1' GROUP BY `tags`.`value` ORDER BY `tags`.`value` ASC";
                    $tags[$i]['values'] = $db->all($sql);
                    foreach($tags[$i]['values'] as $k =>$v) {
                        // Загрузка цвет;
                        $sql = "SELECT `color` FROM `tags_colors` WHERE `tag_id` = '" . $v['id'] . "' AND `status` = '1' LIMIT 1";
                        $tags[$i]['values'][$k]['color'] = $db->one($sql);
                        if(empty($tags[$i]['values'][$k]['color'])) unset($tags[$i]['values'][$k]['color']);
                    }
                }
            }
            return $tags;
        }*/

    public static function getPath($id,$categoryId = false,$categoryList = false){
        if(!$categoryList && !$categoryId){

        }
        $categoryLink = CategoryLinks::find()->where(['product_id' => $id])->one();
        if(!$categoryLink){
            return '/catalog/';
        }else{
            return '/' . Category::getCategoryPath($categoryLink->category_id) . $id;
        }
        return false;
    }

    public function getCatalogPath(){
        if(!empty($this->category)){
            return '/'.$this->category->categoryCatalogPath . $this->id;
        }
        return '/catalog/new/';

//        $categoryLink = CategoryLinks::find()->where(['product_id' => $this->id])->one();
//        if(!$categoryLink){
//            return '/catalog/';
//        }else{
//            return '/' . Category::getCategoryPath($categoryLink->category_id) . $this->id;
//        }
    }

    public function getCatalogUrl(){
        if(!$this->category){
            return '/catalog/';
        }else{
            return '/' . $this->category->catalogPath .$this->id;
        }
        return false;
    }

    public function getCategory(){
        return $this->hasOne(Category::className(), ['id' => 'category_id'])
            ->viaTable(CategoryLinks::tableName(), ['product_id' => 'id']);
    }

    public function getProductType(){
        return $this->hasOne(GoodsTypes::className(), ['id' => 'type_id']);
//        return GoodsTypes::findOne($this->type_id);
    }

    public static function getProductVariants($id){
        return GoodsVariations::find()
            ->select([
                'goods_variations.*',
                'tags.value AS tagValue'
            ])
            ->leftJoin('tags_links','tags_links.variation_id = goods_variations.id')
            ->leftJoin('tags','tags_links.tag_id = tags.id')
            ->where([
                'good_id' => $id,
                'goods_variations.status' => 1,
            ])
            ->all();
    }

    public static function getProductVariantsTagHash($id){
        return Tags::find()
            ->select([
                'tags.*',
                'tags_groups.name AS tagName',
                'goods_variations.id AS variationId',
            ])
            ->leftJoin('tags_groups','tags_groups.id = tags.group_id')
            ->leftJoin('tags_links','tags_links.tag_id = tags.id')
            ->leftJoin('goods_variations','goods_variations.id = tags_links.variation_id')
            ->where([
                'goods_variations.good_id' => $id,
                'tags_groups.status' => 1,
                'tags_groups.show' => 1,
            ])
            ->andWhere(['OR',
                ['>','good_count(`goods_variations`.`good_id`, `goods_variations`.`id`)',1],
                ['IS','good_count(`goods_variations`.`good_id`, `goods_variations`.`id`)',NULL]
            ])
            ->all();
    }

    public function getRelatedProducts(){
        $goods = GoodsVariations::find()->where(['good_id'=>$this->id, 'status'=>1, ])->all();
        $related=[];
        foreach ($goods as $good){//можно оптимизировать и скать только по одной вариации без объединения массивов
            $related = array_merge($related, $good->getRelatedProducts());
        }
        return Goods::find()->where(['id'=>$related, 'status'=>1, 'show'=>1])->all();
    }

    public static function getProductAllEmptyParams(){

    }

    public static function getAllDataProduct($id){
        $data = [];
        $data['tagValues'] = $data['images'] = [];
        $data['model'] = self::find()->where(['id' => $id])->one();
        $data['category'] = CategoryLinks::find()->where(['product_id' => $id])->one();
        $data['shopGroup'] = ShopGroupVariantLink::find()->where(['product_id' => $id])->one();
        $data['shopsList'] = $data['shopGroup'] ? ShopsStores::getStoreList($data['shopGroup']) : [];
        $data['shopGroup'] = $data['shopGroup'] ? $data['shopGroup'] : new ShopGroupVariantLink();
        $shopsListIds = [];
        foreach($data['shopsList'] as $item){
            $shopsListIds[] = $item->id;
        }
        $data['variations'] = GoodsVariations::find()
            ->select([
                'goods_variations.*',
                'get_tags(`goods_variations`.`id`) AS tags_name',
            ])
            ->where(['good_id' => $id])
            ->all();
        if(!$data['variations']){
            $data['tagValuesDouble'] = [];
            $data['shopStoreCount'] = [];
        }else{
            foreach($data['variations'] as $variant){
                $data['tagValuesDouble'][$variant->id] = TagsLinks::find()
                    ->select(['tags.id AS tagsLinksId','tags.value','tags_groups.id AS tagsGroupsId','tags_links.tag_id','tags_links.id'])
                    ->leftJoin('tags','tags_links.tag_id = tags.id')
                    ->leftJoin('tags_groups','tags_groups.id = tags.group_id')
                    ->where([
                        'tags_links.variation_id' => $variant->id,
                        'tags.status' => 1,
                        'tags_links.status' => 1,
                        'tags_groups.status' => 1,
                        'tags_groups.type' => 1,
//                        'tags_groups.show' => 1,
                    ])
                    ->orderBy('tags_groups.position')
                    ->all();
                foreach($data['tagValuesDouble'][$variant->id] as $item){
                    $data['tagValues'][$variant->id][$item->tagsGroupsId][] = $item;
                }

                $data['shopStoreCount'][$variant->id] = GoodsCounts::find()
                    ->where(['IN','store_id',$shopsListIds])
                    ->andWhere(['variation_id' => $variant->id])
                    ->indexBy('store_id')
                    ->all();

                $data['images'][$variant->id] = GoodsImages::find()->where(['variation_id' => $variant->id,'status' => 1])->orderBy('position')->all();
            }
        }
        return $data;
    }

    public static function getProductStoresQuery($productId){
        return ShopsStores::find()   //Выбираем все склады группы магазинов, к которой привязан товар
        ->leftJoin('shops','shops.id = shops_stores.shop_id')
            ->leftJoin('shop_group_related','shop_group_related.shop_id = shops.id')
            ->leftJoin('shop_group','shop_group.id = shop_group_related.shop_group_id')
            ->leftJoin('shop_group_variant_link','shop_group_variant_link.shop_group_id = shop_group.id')
            ->where([
                'shop_group_variant_link.product_id' => $productId,
                'shops_stores.status' =>1,
                'shops.status' =>1,
                'shop_group.status' =>1,
            ]);

    }

    public function getQueryCurrentVariantsActive(){
        return $this->hasMany(GoodsVariations::className(), ['good_id' => 'id'])->where(['status' => 1]);
    }

    public function getCurrentVariantsActive(){
        $currentVariants = $this->queryCurrentVariantsActive;
        return $currentVariants ? $currentVariants: 'false';
    }

    public function getQueryCurrentVariantsFull(){
        return $this->hasMany(GoodsVariations::className(), ['good_id' => 'id']);
    }

    public function getCurrentVariantsFull(){
        $currentVariants = $this->queryCurrentVariantsFull;
        return $currentVariants ? $currentVariants: 'false';
    }

    public function getPropertyHash(){
        $variantsIds = [];
        if(!$this->variationsCatalog){
            return false;
        }else{
            foreach ($this->variationsCatalog as $variation) {
                $variantsIds[] = $variation->id;
            }
            return Tags::find()
                ->leftJoin('tags_links','tags_links.tag_id = tags.id')
                ->where([
                    'IN','tags_links.variation_id',$variantsIds,
                ])
//                ->andWhere([
//                    'IN','tags.group_id',
//                ])
                ->orderBy('id')
                ->all();

        }
    }

    public function getPropertyIndexed(){
        if(!$this->propertyHash){
            return false;
        }else{
            $propertySorted = [];
            foreach ($this->propertyHash as $item) {
                $propertySorted[$item->group_id][$item->id] = $item;
            }
            return $propertySorted;
        }
    }

    public function getImages(){
        $result = [];
        $productImages = GoodsImagesLinks::find()->where(['good_id' => $this->id,'status' => 1])->orderBy('position')->all();
        if(!$productImages){
            $productImages = GoodsImages::find()
                ->where(['good_id' => $this->id])
                ->all();
            if($productImages){
                foreach($productImages as $key=> $image){
                    $path = '/files/goods/'.substr(md5($image->id), 0,2).'/'.$image->id;
                    $result[] = $path . '_min.jpg';
                }

                return $result;
            }
        }else {
            return false;
        }
    }
    // Выбор вариция в миниатюре;
    public function getImagesVariants(){
        $result = [];
        $productImages = GoodsImagesLinks::find()->where(['good_id' => $this->id,'status' => 1])->orderBy('position')->all();
        if(!$productImages){
            $productImages = GoodsImages::find()->select('goods_images.*')
                            ->leftJoin('goods_variations','goods_images.variation_id = goods_variations.id')
                            ->where(['goods_images.good_id' => $this->id,'goods_images.status' => 1,'goods_variations.status'=>1])->all();
            if($productImages){
                foreach($productImages as $key=> $image){
                    $path = '/files/goods/'.substr(md5($image->id), 0,2).'/'.$image->id;
                    $result[] = array(
                        'id' => $image->id,
                        'good_id' => $image->good_id,
                        'variation_id' => $image->variation_id,
                        'img' => $path.'_min.jpg',
                    );
                }
                return $result;
            }
        }else {
            return false;
        }
    }
    // Загрузка вариация;
    public static function getProductVariantsId($variant_id){
        return GoodsVariations::find()
            ->select([
                'goods_variations.*',
                'tags.value AS tag_name',
                'tags.id AS tag_id',
            ])
            ->leftJoin('tags_links','tags_links.variation_id = goods_variations.id')
            ->leftJoin('tags','tags_links.tag_id = tags.id')
            ->where([
                'goods_variations.id' => $variant_id,
                'goods_variations.status' => 1,
            ])
            ->indexBy('id')->asArray()->one();
    }

    public function getImagesRelation(){
        return $this->hasMany(GoodsImages::className(),['good_id' => 'id'])->orderBy('position');
    }

    public function getImagesRelationWithPath(){
        $images = [];
        if(!empty($this->imagesRelation)){
            foreach ($this->imagesRelation as $item) {
                $images[] = '/files/goods/'.substr(md5($item->id), 0,2).'/'.$item->id . '_min.jpg';
            }
        }
        return $images;
    }

    // get one image
    public function getImageSimple(){
        $result = [];
        $productImages = GoodsImagesLinks::find()->where(['good_id' => $this->id,'status' => 1])->orderBy('position')->one();
        if(!$productImages){
            $productImages = GoodsImages::find()
                ->where(['cover'=>1,'good_id' => $this->id,'status' => 1])
                ->one();

            if($productImages){
                $path = '/files/goods/'.substr(md5($productImages->id), 0,2).'/'.$productImages->id;
                $result = $path . '_min.jpg';
                // Проверка на существ. изображения;
                if(!file_exists($_SERVER['DOCUMENT_ROOT'].$result))   return false;
                return $result;
            }
        }else {
            return false;
        }
    }

    // Store for Basket >>
    public function getStoreList(){
        return ShopsStores::find()
            ->select([
                'shops_stores.address_id',
                'shops_stores.shop_id',
                'CONCAT(`address`.`street`,\' \',`address`.`house`) AS address',
            ])
            ->leftJoin('address','address.id = shops_stores.address_id')
            ->leftJoin('shop_group_related','shop_group_related.shop_id = shops_stores.shop_id')
            ->leftJoin('shop_group_variant_link','shop_group_variant_link.shop_group_id = shop_group_related.shop_group_id')
            ->where([
                'shop_group_variant_link.product_id' => $this->id,
                'shops_stores.status' => 1,
            ])
            ->all();
    }

    public function getStoreListFull(){
        return ShopsStores::find()
            ->leftJoin('shop_group_related','shop_group_related.shop_id = shops_stores.shop_id')
            ->leftJoin('shop_group_variant_link','shop_group_variant_link.shop_group_id = shop_group_related.shop_group_id')
            ->where([
                'shop_group_variant_link.product_id' => $this->id,
                'shops_stores.status' => 1,
            ])
            ->all();
    }

    public function getStoreListJson(){
        $storeList = $this->storeList;

        if(!$storeList){
            return false;
        }else{
            $storeListForJs = [];
            foreach($storeList as $storeItem){
                $storeListForJs[$this->id][] = [
                    'address' => $storeItem['address'],
                    'id' => $storeItem['address_id'],
                    'product_id' => $this->id,
                ];
            }
            return json_encode($storeListForJs);
        }
    }

    public function getStoreListArray(){
        $storeList = $this->storeList;

        if(!$storeList){
            return false;
        }else{
            $storeListForJs = [];
            foreach($storeList as $storeItem){
                $storeListForJs[$this->id][] = [
                    'address' => $storeItem['address'],
                    'id' => $storeItem['address_id'],
                    'product_id' => $this->id,
                ];
            }
            return $storeListForJs;
        }
    }

    public function getDefaultStore(){
        $storeList = $this->storeList;

        if(!$storeList){
            return false;
        }else{
            return !empty($storeList[0]['address_id']) ? $storeList[0]['address_id'] : false;
        }
    }
    // Store for Basket END <<

    public function getShop(){
        return $this->hasOne(ShopGroup::className(), ['id' => 'shop_group_id'])
            ->viaTable('shop_group_variant_link', ['product_id' => 'id']);
    }

    public function getShopLink(){
        return $this->hasOne(ShopGroupVariantLink::className(), ['product_id' => 'id']);
    }

    public function getCheckPay(){
        return Goods::find()
            ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
            ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
            ->where([
                'goods.id' => $this->id,
                'goods.status' => 1,
                'goods.show' => 1,
                'goods.confirm' => 1,
                'shop_group.status' => 1
            ])->one() ? 1: 0;
    }


    public function getStickers(){
        return $this->hasMany(StickerLinks::className(),['good_id' => 'id'])->where(['status'=>1]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        // Если товар отключили
        if(!$insert && (!empty($changedAttributes['status']) && $changedAttributes['status'] == 1 && $this->status == 0)){
//            $variants = $this->queryCurrentVariantsFull;
//            if(!$variants){
//
//            }else{
//                foreach ($variants as $variant) {
//                    $variant->status = 0;
//                    $variant->save();
//                }
//            }
        }
        // Если товар создали/включили
//        if(($insert && $this->status == 1) || ($changedAttributes['status'] == 0 && $this->status == 1)){
//
//        }
        // При создании товара
        if ($insert) {
            $model = new UsersLogs();
            $model->user_id = (\Yii::$app->user->id) ? \Yii::$app->user->id : '';
            $model->good_id = $this->id;
            $model->type = 1;
            $model->save();
        } /*elseif (!$insert) {
            $model = new UsersLogs();
            $model->user_id = (\Yii::$app->user->id) ? \Yii::$app->user->id : '';
            $model->good_id = $this->id;
            //$model->variations_id = ;
            $model->type = 2;
            $model->save();
        }*/

    }
}
