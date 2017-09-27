<?php

namespace app\controllers;

use app\modules\catalog\models\Sticker;
use app\modules\catalog\models\StickerLinks;
use Yii;
use yii\base\Model;
use yii\filters\VerbFilter;

use app\modules\common\controllers\FrontController;
use app\modules\catalog\models\GoodsImages;
use app\modules\catalog\models\GoodsSearch;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\CategoryLinks;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\TagsGroups;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsImagesLinks;
use app\modules\catalog\models\TagsLinks;
use app\modules\catalog\models\GoodsTypes;

use app\modules\managment\models\ShopGroupVariantLink;
use app\modules\managment\models\ShopsStores;

use app\modules\common\models\Zloradnij;
use app\modules\common\controllers\BackendController;

use app\modules\shop\models\OrdersItems;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * CategoryController implements the CRUD actions for Category model.
 */

class ProductController extends BackendController
{
    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'create',
                            'update',
                            'status-list',
                            'review',
                            'copy'
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager', 'conflictManager', 'callcenterOperator','HR'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

/////////////////////////////////////////////////////////////
//  Products control
////////////////////////////////////////////////////////////
    public function actionIndex(){
        $searchModel = new GoodsSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->searchNotDelete(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStatusList(){
        $searchModel = new GoodsSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->searchStatusList(Yii::$app->request->queryParams);

        return $this->render('status-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSetShopGroupsAll(){
        foreach(Shops::find()->where(['status' => 1])->all() as $shop){
            $shopGroup = new ShopGroup();
            $shopGroup->name = $shop->name;
            $shopGroup->comission_id = $shop->comission_id;
            $shopGroup->status = 1;
            if($shopGroup->save()){
                print 'OK ___'.$shopGroup->name . '<br>';

                $shopGroupRelated = new ShopGroupRelated();
                $shopGroupRelated->shop_group_id = $shopGroup->id;
                $shopGroupRelated->shop_id = $shop->id;
                $shopGroupRelated->status = 1;
                if($shopGroupRelated->save()){
                    print 'OK ___ related<br/>';

                    $products = Goods::find()->where(['shop_id' => $shop->id])->all();
                    if(!$products){

                    }else{
                        $count = 0;
                        foreach($products as $product){
                            $productLink = new ShopGroupVariantLink();
                            $productLink->shop_group_id = $shopGroup->id;
                            $productLink->product_id = $product->id;
                            if($productLink->save()){
                                $count++;
                            }
                        }
                        print 'OK ___ count = '+$count+'<br/>';
                    }
                }
            }
            print '<hr />';
            print '<hr />';
        }
    }

    public function actionSetorderstatus(){
        if(isset($_POST['order_id']) && !empty($_POST['order_id']) && isset($_POST['status']) && !empty($_POST['status'])){
            $orderId = $_POST['order_id'] * 1;
            $status = $_POST['status'] * 1;

            if($orderId > 0 && $status > 0){
                $orderItems = OrdersItems::find()
                    ->leftJoin('orders_groups','orders_groups.id = orders_items.order_group_id')
                    ->leftJoin('orders','orders.id = orders_groups.order_id')
                    ->where(['orders.id' => $orderId])
                    ->all();
                if(!$orderItems){

                }else{
                    foreach($orderItems as $item){
                        $item->status_id = $status;
                        if($item->save()){

                        }else{
                            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tre.txt','!---ERROR SAVE ORDER STATUS---!'."\n\r".$item->errors);
                        }
                    }
                }
            }
        }else{
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tre.txt','!---ERROR FIND ORDER---!'."\n\r".var_export($_POST));
        }
    }
    // Предосмотр;
    public function actionReview(){
        if(Yii::$app->request->post('compact')) {
            $id = intval(Yii::$app->request->post('id'));
            $model = Goods::find()
                ->where(['id' => $id])
                ->one();
            $variations = Goods::getProductVariants($id);
            $tagsHash = Goods::getProductVariantsTagHash($id);
            $productImages = GoodsImagesLinks::find()->where(['good_id' => $id])->all();
            if (!$productImages) {
                $productImages = GoodsImages::find()->where(['good_id' => $id])->all();
            }
            if (!$tagsHash) {
                $tags = [];
            } else {
                foreach ($tagsHash as $tag) {
                    $tags[$tag->variationId][$tag->group_id][$tag->id] = $tag->value;
                }
            }
            return $this->renderPartial('review', [
                'model' => $model,
                'variations' => $variations,
                'tags' => $tags,
                'productImages' => $productImages,
            ]);
        }
        //return $this->renderPartial('review');
    }

    public function actionCreate(){
        $shopsList = [];
        $shopStoreCount = $tagValues = false;

        $tagGroup = TagsGroups::find()
            ->where(['tags_groups.status' => 1,'tags_groups.type' => 1,'tags_groups.show' => 1])
            ->indexBy('id')
            ->all();
        $tagValueDouble = Tags::find()
            ->leftJoin('tags_groups','tags_groups.id = tags.group_id')
            ->where([
                'tags.status' => 1,
                'tags_groups.status' => 1,
                'tags_groups.type' => 1,
                'tags_groups.show' => 1,
            ])
            ->all();
        $tagValue = [];
        if(!$tagValueDouble){

        }else{
            foreach($tagValueDouble as $val){
                $tagValue[$val->group_id][] = $val;
            }
        }
        $id = false;
        $model = new Goods();
        if ($model->load(Yii::$app->request->post())){
            $model->date_create = $model->date_update = date('Y-m-d H:i:s');
            $model->position = !$model->position?1:$model->position;
            $model->count_pack = !$model->count_pack?1:$model->count_pack;
            $model->count_min = !$model->count_min?1:$model->count_min;
            $model->shop_id = 10000001;
            if ($model->save()) {
                $id = $model->id;
            }
        }

        if($id){
            $requestPost = Yii::$app->request->post();
            if(isset($requestPost['sticker'])){
                $arSticker = $requestPost['sticker'];
                foreach ($arSticker as $key => $sticker){
                    $modelSticker = new StickerLinks();
                    $modelSticker->good_id = $id;
                    $modelSticker->sticker_id = $sticker;
                    if(!$modelSticker->save()){
                        print_r($modelSticker->getErrors());
                        die;
                    }
                }
            }
        }

        $category = false;
        if($id){
            $category = CategoryLinks::find()->where(['product_id' => $id])->one();
        }
        if(!$category){
            $category = new CategoryLinks();
            $category->product_id = $id;
        }
        if ($category->load(Yii::$app->request->post()) && $category->save()) {

        }
        $shopGroup = false;
        if($id){
            $shopGroup = ShopGroupVariantLink::find()->where(['product_id' => $id])->one();
        }
        if(!$shopGroup){
            $shopGroup = new ShopGroupVariantLink();
            $shopGroup->product_id = $id;
        }else{
            $shopsList = ShopsStores::find()
                ->select([
                    'shops_stores.*',
                    'shop_group_related.shop_group_id AS shopGroupId',
                    'shops.name AS shopName',
                ])
                ->leftJoin('shops', 'shops.id = shops_stores.shop_id')
                ->leftJoin('shop_group_related', 'shops.id = shop_group_related.shop_id')
                ->where(['IN', 'shop_group_related.shop_group_id', $shopGroup->shop_group_id])
                ->all();
        }
        if ($shopGroup->load(Yii::$app->request->post()) && $shopGroup->save()) {

        }
        $variations = false;
        if($id){
            $variations = GoodsVariations::find()
                ->select([
                    'goods_variations.*',
                    'get_tags(`goods_variations`.`id`) AS tags_name',
                ])
                ->where(['good_id' => $id])
                ->all();
        }
        if(!$variations){
            $model->status = 0;
        }else{
            foreach($variations as $variant){
                $variantIds[] = $variant->id;
            }
            foreach($variations as $variant){
                $tagValuesDouble[$variant->id] = TagsLinks::find()
                    ->select(['tags.id AS tagsLinksId','tags.value','tags_groups.id AS tagsGroupsId','tags_links.tag_id','tags_links.id'])
                    ->leftJoin('tags','tags_links.tag_id = tags.id')
                    ->leftJoin('tags_groups','tags_groups.id = tags.group_id')
                    ->where([
                        'tags_links.variation_id' => $variant->id,
                        'tags.status' => 1,
                        'tags_links.status' => 1,
                        'tags_groups.status' => 1,
                        'tags_groups.type' => 1,
                        'tags_groups.show' => 1,
                    ])
                    ->orderBy('tags_groups.position')
                    ->all();

                if(!$tagValuesDouble[$variant->id]){

                }else{
                    foreach($tagValuesDouble[$variant->id] as $item){
                        $tagValues[$variant->id][$item->tagsGroupsId][] = $item;
                    }
                    foreach($tagGroup as $tagGroupItem){
                        if(isset(Yii::$app->request->post()['TagsLinks'][$variant->id][$tagGroupItem->id])){
                            $tagsLinksData = Yii::$app->request->post()['TagsLinks'][$variant->id][$tagGroupItem->id];

                            if(isset($tagsLinksData['tag_id']) && !empty($tagsLinksData['tag_id'])){
                                if(isset($tagValues[$variant->id][$tagGroupItem->id][0])){

                                }else{
                                    $tagValues[$variant->id][$tagGroupItem->id][0] = new TagsLinks();
                                    $tagValues[$variant->id][$tagGroupItem->id][0]->variation_id = $variant->id;
                                }
                                $tagsLinksData = ['TagsLinks' => $tagsLinksData];

                                if ($tagValues[$variant->id][$tagGroupItem->id][0]->load($tagsLinksData) && $tagValues[$variant->id][$tagGroupItem->id][0]->save()) {
                                    $variant->tags_name = GoodsVariations::find()
                                        ->select(['get_tags(`goods_variations`.`id`) AS tags_name'])
                                        ->where(['goods_variations.id' => $variant->id])
                                        ->asArray()
                                        ->one();
                                    $variant->tags_name = $variant->tags_name['tags_name'];
                                }
                            }else{
                                if(isset($tagValues[$variant->id][$tagGroupItem->id][0])){
                                    if($tagValues[$variant->id][$tagGroupItem->id][0]->delete()){
                                        unset($tagValues[$variant->id][$tagGroupItem->id]);
                                        $variant->tags_name = GoodsVariations::find()
                                            ->select(['get_tags(`goods_variations`.`id`) AS tags_name'])
                                            ->where(['goods_variations.id' => $variant->id])
                                            ->asArray()
                                            ->one();
                                        $variant->tags_name = $variant->tags_name['tags_name'];
                                    }else{
                                        Zloradnij::printArray($tagValues[$variant->id][$tagGroupItem->id][0]->errors);
                                    }
                                }
                            }
                        }
                    }
                }
                if(!$shopsList || empty($shopsList)){
                }else{
                    foreach($shopsList as $item){
                        $shopsListIds[] = $item->id;
                    }
                    $shopStoreCount[$variant->id] = GoodsCounts::find()
                        ->where(['IN','store_id',$shopsListIds])
                        ->andWhere(['variation_id' => $variant->id])
                        ->indexBy('store_id')
                        ->all();
                    if (Model::loadMultiple($shopStoreCount[$variant->id], Yii::$app->request->post()) && Model::validateMultiple($shopStoreCount[$variant->id])) {
                        foreach ($shopStoreCount[$variant->id] as $key => $item) {
                            if ($item->save()) {
                            }
                        }
                    }
                    if(!$shopStoreCount[$variant->id] || count($shopStoreCount[$variant->id] < count($shopsListIds))){
                        foreach($shopsListIds as $item){
                            if(!isset($shopStoreCount[$variant->id][$item]) || empty($shopStoreCount[$variant->id][$item])){
                                $shopStoreCount[$variant->id][$item] = new GoodsCounts();
                                $shopStoreCount[$variant->id][$item]->good_id = $id;
                                $shopStoreCount[$variant->id][$item]->variation_id = $variant->id;
                                $shopStoreCount[$variant->id][$item]->store_id = $item;
                                $shopStoreCount[$variant->id][$item]->count = 0;
                                $shopStoreCount[$variant->id][$item]->update = date('Y-m-d H:i:s');
                                $shopStoreCount[$variant->id][$item]->status = 1;
                                $shopStoreCount[$variant->id][$item]->save();
                            }
                        }
                    }
                    foreach($shopsListIds as $item){
                        if(isset(Yii::$app->request->post()['GoodsCounts'][$variant->id][$item])){
                            $goodsCountsData = Yii::$app->request->post()['GoodsCounts'][$variant->id][$item];
                            $goodsCountsData = ['GoodsCounts' => $goodsCountsData];
                            if ($shopStoreCount[$variant->id][$item]->load($goodsCountsData) && $shopStoreCount[$variant->id][$item]->save()) {

                            }
                        }
                    }
                }
            }
        }
        if ($variations && Model::loadMultiple($variations, Yii::$app->request->post()) && Model::validateMultiple($variations)) {
            foreach ($variations as $key => $item) {
                if ($item->save()) {
                }
            }
        }
        if($id){
            return $this->redirect(['/product/update', 'id' => $id]);
        }
        return $this->render('/product/create', [
            'model' => $model,
            'category' => $category,
            'variations' => $variations,
            'shopStoreCount' => $shopStoreCount,
            'variantProperties' => $tagValues,
            'tagGroup' => $tagGroup,
            'tagValue' => $tagValue,
            'shopsList' => $shopsList,
            'shopGroup' => $shopGroup,
            'images' => [],
            'menu' =>  $this->actionsMenu['products'],
        ]);
    }

    public function actionDelete($id){
        $model = Goods::find()->where(['id' => $id])->one();
        $model->status = -1;
        $model->save();

        return $this->redirect([
            '/product/index',
            'sort' => '-id',
        ]);
    }

    public function actionUpdate($id){
        $requestPost = Yii::$app->request->post();
        $shopsList = $tagValue = [];

        $updatedProduct = Goods::findOne($id);
        if(!$updatedProduct){
            return false;
        }
        $shopGroup = ShopGroupVariantLink::find()->where(['product_id' => $id])->one();
        if(!$shopGroup){
            $shopGroup = new ShopGroupVariantLink();
            $shopGroup->product_id = $id;
        }else{
            $shopsList = $updatedProduct->storeListFull;
        }
        if ($shopGroup->load($requestPost) && $shopGroup->save()) {
        }

        $category = CategoryLinks::find()->where(['product_id' => $id])->one();
        if(!$category){
            $category = new CategoryLinks();
            $category->product_id = $id;
        }
        if ($category->load($requestPost) && $category->save()) {

        }

        $userId = Yii::$app->user->identity['id'];
        $this->view->registerCssFile('/shop/css/dropzone.min.css');

        $model = Goods::find()->where(['id' => $id])->one();
        $modelVariant = GoodsVariations::find()
            ->select([
                'id',
                'code',
                'servingforday',
                'full_name',
                'name',
                'description',
                "get_tags(`id`) AS `tags_name`",
                'price',
                'comission',
                'status',
            ])
            ->where(['good_id' => $id])
            ->all();

        $modelVariantIds = [];
        foreach($modelVariant as $variant){
            $modelVariantIds[] = $variant->id;
        }



        $countVariation = false;//GoodsCounts::find()->where(['IN','variation_id',$modelVariantIds])->andWhere(['status' => 1])->indexBy('variation_id')->asArray()->all();
        foreach($modelVariantIds as $ids){
            if(!$shopsList || empty($shopsList)){

            }else{
                foreach($shopsList as $item){
                    $shopsListIds[] = $item->id;
                }

                $shopStoreCount[$ids] = GoodsCounts::find()
                    ->where(['IN','store_id',$shopsListIds])
                    ->andWhere(['variation_id' => $ids])
                    ->indexBy('store_id')
                    ->all();
                if (Model::loadMultiple($shopStoreCount[$ids], $requestPost) && Model::validateMultiple($shopStoreCount[$ids])) {
                    foreach ($shopStoreCount[$ids] as $key => $item) {
                        if ($item->save()) {

                        }
                    }
                }
                if(!$shopStoreCount[$ids] || count($shopStoreCount[$ids] < count($shopsListIds))){
                    foreach($shopsListIds as $item){
                        if(!isset($shopStoreCount[$ids][$item]) || empty($shopStoreCount[$ids][$item])){
                            $shopStoreCount[$ids][$item] = new GoodsCounts();
                            $shopStoreCount[$ids][$item]->good_id = $id;
                            $shopStoreCount[$ids][$item]->variation_id = $ids;
                            $shopStoreCount[$ids][$item]->store_id = $item;
                            $shopStoreCount[$ids][$item]->count = 0;
                            $shopStoreCount[$ids][$item]->update = date('Y-m-d H:i:s');
                            $shopStoreCount[$ids][$item]->status = 1;
                            $shopStoreCount[$ids][$item]->save();
                        }
                    }
                }
                foreach($shopsListIds as $item){
                    if(isset($requestPost['GoodsCounts'][$ids][$item])){
                        $goodsCountsData = $requestPost['GoodsCounts'][$ids][$item];
                        $goodsCountsData = ['GoodsCounts' => $goodsCountsData];
                        if ($shopStoreCount[$ids][$item]->load($goodsCountsData)) {
                            $shopStoreCount[$ids][$item]->status = 1;
                            if($shopStoreCount[$ids][$item]->save()){

                            }
                        }
                    }
                }
            }
        }

        $images = GoodsImagesLinks::find()->where(['IN','variation_id',$modelVariantIds])->all();
        $variantImages = [];
        foreach($images as $image){
            $variantImages[$image->variation_id][$image->image_id] = \Yii::$app->params['generalImagePath'] . '/' . substr(md5($image->image_id), 0, 2) . '/' . $image->image_id . '_min.jpg';
        }

        $tagsListValue = TagsLinks::find()
            ->select('tags.*, tags_links.*')
            ->joinWith(['tags'])
            ->where(['IN','tags_links.variation_id',$modelVariantIds])
            ->andWhere(['tags_links.status' => 1])
            ->all();

        $tagsListValueByGroup = [];
        foreach($tagsListValue as $item){
            $tagsListValueByGroup[$item->variation_id][$item->tags->group_id][$item->tags->id] = $item->tags->value;
        }
         // Загрузка изображения спомощью Аякса;
         if(Yii::$app->request->post('imagesAjax')) {
             // Обработка пост данные;
             $AjaxGood_id = intval(Yii::$app->request->post('good_id_a'));
             $AjaxVariant_id = intval(Yii::$app->request->post('variant_id_a'));
             // Ответ данные JSON-формат;
             $response = Yii::$app->response;
             $response->format = \yii\web\Response::FORMAT_JSON;
             // Загрузка изображения;
             if (isset($_FILES['GoodsImages']['name'][$AjaxVariant_id][0]['id']) && !empty($_FILES['GoodsImages']['name'][$AjaxVariant_id][0]['id'])) {
                 $tmp_name = $_FILES['GoodsImages']['tmp_name'][$AjaxVariant_id][0]['id'];
                 // Загружаем изображения;
                 GoodsImages::images_upload($AjaxGood_id, $AjaxVariant_id, [$tmp_name]);
             }
             return $response->data = ['success' => true];
         }
         // Добавления стикера;
         if($model->id){
            $requestPost = Yii::$app->request->post();
            if($requestPost){
                $dbStiker = ArrayHelper::getColumn(\app\modules\catalog\models\StickerLinks::find()->where(['good_id'=>$model->id])->All(),'sticker_id');
                $clearStiker = StickerLinks::find()->where(['good_id'=>$model->id])->All();
                foreach ($clearStiker as $sticker){
                    $sticker->status = 0;
                    $sticker->save();
                }
                if(isset($requestPost['sticker'])) {
                    foreach ($requestPost['sticker'] as $key => $sticker) {
                        if (in_array($sticker, $dbStiker)) {
                            $modelSticker = StickerLinks::find()->where(['sticker_id' => $sticker, 'good_id' => $model->id])->One();
                        } else {
                            $modelSticker = new StickerLinks();
                        }
                        $modelSticker->good_id = $model->id;
                        $modelSticker->sticker_id = $sticker;
                        $modelSticker->status = 1;
                        if (!$modelSticker->save()) {
                            print_r($modelSticker->getErrors());
                            die;
                        }
                    }
                }
            }
         }
        //
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            $producerAll = isset($post['producer-all'])?$post['producer-all']:false;
            $countryAll = isset($post['country-all'])?$post['country-all']:false;
            $variantsList = Yii::$app->request->post();
            if(!empty($variantsList['GoodsVariations'])) {
                $variantsList = $variantsList['GoodsVariations'];
                if (!empty($variantsList)) {
                    $connection = \Yii::$app->db;
                    $transaction = $connection->beginTransaction();
                    try {
                        if ($model->load(Yii::$app->request->post())) {
                            if (!isset($model->date_create)
                                || empty($model->date_create)
                            ) {
                                $model->date_create = date('Y-m-d H:i:s');
                            }
                            $model->date_update = date('Y-m-d H:i:s');
                            if ($model->save()) {

                                Yii::info(
                                    date("Y-m-d H:i:s") .
                                    " UserID: " . $userId .
                                    ", ProductID: " . $model->id .
                                    ", status: " . $model->status .
                                    ', Action: UPDATE PRODUCT',
                                    'Shop'
                                );
                                foreach ($variantsList as $key => $variant) {
                                    if (isset($variant['price'])
                                        && $variant['price'] > 0
                                    ) {
                                        if (isset($variant['id'])
                                            && !empty($variant['id'])
                                        ) {
                                            $modelOldVariant
                                                = GoodsVariations::findOne(
                                                $variant['id']
                                            );
                                        } else {
                                            $modelOldVariant
                                                = new GoodsVariations();
                                        }

                                        if ($modelOldVariant->load(
                                            ['GoodsVariations' => $variant]
                                        )
                                        ) {
                                            $modelOldVariant->good_id = $model->id;
                                            if ($modelOldVariant->save()) {


                                                    // Загрузка изображения;
                                                    /*
                                                    if (isset($_FILES['GoodsImages']['name'][$modelOldVariant->id][0]['id'][0]) && !empty($_FILES['GoodsImages']['name'][$modelOldVariant->id][0]['id'][0])) {
                                                      foreach($_FILES['GoodsImages']['tmp_name'][$modelOldVariant->id][0]['id'] as $k=>$GoodsImages) {
                                                          GoodsImages::images_upload($model->id, $modelOldVariant->id, [$GoodsImages]);
                                                      }
                                                    }*/
                                                $images[$modelOldVariant->id] = GoodsImages::find()
                                                    ->where(
                                                        ['variation_id' => $modelOldVariant->id,
                                                            'status' => 1]
                                                    )->orderBy('position')->all(
                                                    );


                                            } else {
                                                Zloradnij::printArray(
                                                    $modelOldVariant->errors
                                                );
                                            }

                                            if ($modelOldVariant->status > 0) {
                                                if ($modelOldVariant->price
                                                    <= 0
                                                ) {
                                                    $modelOldVariant->status
                                                        = 0;
                                                }
                                                if (isset($shopStoreCount[$modelOldVariant->id])
                                                    && !empty($shopStoreCount[$modelOldVariant->id])
                                                ) {
                                                    $zeroCount = false;
                                                    foreach (
                                                        $shopStoreCount[$modelOldVariant->id]
                                                        as $store
                                                    ) {
                                                        if ($store->count
                                                            <= 0
                                                        ) {
//                                                        Zloradnij::printArray($store);
                                                            $zeroCount = true;
                                                        }
                                                    }
                                                    if ($zeroCount) {
                                                        $modelOldVariant->status
                                                            = 0;
                                                    }
                                                } else {
                                                    $modelOldVariant->status
                                                        = 0;
                                                }
                                            }

                                            if ($modelOldVariant->save()) {

                                                $notDeleteTagsLinks
                                                    = TagsLinks::find()->where(
                                                    ['variation_id' => $modelOldVariant->id]
                                                )->all();
                                                if ($notDeleteTagsLinks) {
                                                    foreach (
                                                        $notDeleteTagsLinks as
                                                        $itemTagLinks
                                                    ) {
                                                        $notDeleteTags
                                                            = Tags::find()
                                                            ->where(
                                                                ['id' => $itemTagLinks->tag_id]
                                                            )->one();
                                                        if (!in_array(
                                                            $notDeleteTags->group_id,
                                                            Yii::$app->params['breadcrumbsTagId']
                                                        )
                                                        ) {
                                                            $itemTagLinks->delete(
                                                            );
                                                        }
                                                    }
                                                }

                                                if (!$producerAll) {

                                                } else {
                                                    $tagsLinks = new TagsLinks(
                                                    );
                                                    $tagsLinks->variation_id
                                                        = $modelOldVariant->id;
                                                    $tagsLinks->tag_id
                                                        = $producerAll;
                                                    $tagsLinks->status = 1;
                                                    $tagsLinks->save();
                                                }

                                                if (!$countryAll) {

                                                } else {
                                                    $tagsLinks = new TagsLinks(
                                                    );
                                                    $tagsLinks->variation_id
                                                        = $modelOldVariant->id;
                                                    $tagsLinks->tag_id
                                                        = $countryAll;
                                                    $tagsLinks->status = 1;
                                                    $tagsLinks->save();
                                                }

                                                if (!empty($_POST['variations_add'][$key]['tags'])) {
                                                    foreach (
                                                        $_POST['variations_add'][$key]['tags']
                                                        as $tagCode => $tagValue
                                                    ) {
                                                        if (!in_array(
                                                            $tagCode,
                                                            Yii::$app->params['breadcrumbsTagId']
                                                        )
                                                        ) {
                                                            $tagsLinks
                                                                = new TagsLinks(
                                                            );
                                                            $tagsLinks->variation_id
                                                                = $modelOldVariant->id;
                                                            $tagsLinks->tag_id
                                                                = $tagCode;
                                                            $tagsLinks->status
                                                                = 1;

                                                            $tagsLinks->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }


                                $transaction->commit();
                                $allData = Goods::getAllDataProduct($id);

                                return $this->redirect(
                                    ['/product/update', 'id' => $id]
                                );
                            }
                            else {
                                //Zloradnij::printArray($model->errors);
                            }
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                }
            }

        }

        $allData = Goods::getAllDataProduct($id);

        // Обновления фото;
        if(Yii::$app->request->post('imagesAjaxUpdate')) {
            $variant_id = Yii::$app->request->post('variant_id');
            return \app\components\shopProducts\WImages::widget(['images'=>$allData['images'],'model'=>$model,'variant_id'=>$variant_id]);
        }
        // Смена обложка;
        if(Yii::$app->request->post('coverImages')) {
            $image_id = Yii::$app->request->post('image_id');
            $variant_id = Yii::$app->request->post('variant_id');
            // Проверка данные;
            if(Yii::$app->request->post('coverImages') && $model->id && $image_id) {
                $image = GoodsImages::findOne($image_id);
                $image->cover = ($image->cover ? 0 : 1);
                if ($image->save()) {
                    $images = GoodsImages::find()->where(['good_id' => $model->id])->andWhere('id != :id', ['id' => $image_id])->all();
                    foreach ($images as $value) {
                        $value->cover = 0;
                        $value->update(false);
                    }
                }
                return true;
                // Обновления;
               // return \app\components\shopProducts\WImages::widget(['images' => $allData['images'], 'model' => $model, 'variant_id' => $variant_id]);
            }else{
                return false;
            }
        }



        return $this->render('/product/update', [
            'model' => $model,
            'typeProduct' => GoodsTypes::find()->all(),
            'producers' => Tags::find()->where(['group_id' => 1008])->andWhere(['status' => 1])->orderBy('value')->all(),
            'country' => Tags::find()->where(['group_id' => 1007])->andWhere(['status' => 1])->orderBy('value')->all(),
            'modelVariant' => $modelVariant,
            'tagsList' => TagsGroups::find()->where(['type' => 1])->andWhere(['status' => 1])->orderBy('position')->all(),
            'tagsListValue' => $tagsListValueByGroup,
            'tags' => new Tags(),
            'variantImages' => $variantImages,
            'countVariation' => $countVariation,
            'category' => $category,
            'shopsList' => $allData['shopsList'],
            'shopGroup' => $allData['shopGroup'],
            'shopStoreCount' => $allData['shopStoreCount'],
            'images' => $allData['images'],
            'menu' =>  $this->actionsMenu['products'],
        ]);
    }

    public function actionCopy($id){
        $model = new Goods();
        $model->shop_id = 10000001;
        //Создаем категорию
        $category = new CategoryLinks();
//        print_r(Yii::$app->request->post());die;
        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()){

            $category->load(Yii::$app->request->post());
            $category->product_id = $model->id;
            $category->save();

            $producerAll = isset($post['producer-all'])?$post['producer-all']:false;
            $countryAll = isset($post['country-all'])?$post['country-all']:false;
            $variantsList = Yii::$app->request->post();
            if(!empty($variantsList['GoodsVariations'])) {
                $userId = Yii::$app->user->getId();
                $variantsList = $variantsList['GoodsVariations'];
                if (!empty($variantsList)) {
                    $connection = \Yii::$app->db;
                    $transaction = $connection->beginTransaction();
                                foreach ($variantsList as $key => $variant) {
                                    if (isset($variant['price']) && $variant['price'] > 0) {
                                        $modelOldVariant = new GoodsVariations();

                                        if ($modelOldVariant->load(['GoodsVariations' => $variant])) {
                                            $modelOldVariant->good_id = $model->id;
                                            if ($modelOldVariant->save()) {
                                                $images[$modelOldVariant->id] = GoodsImages::find()
                                                    ->where(['variation_id' => $variant['id'],'status' => 1])
                                                    ->orderBy('position')->all();
                                                foreach (GoodsImages::find()
                                                             ->where(['variation_id' => $variant['id'],'status' => 1])
                                                             ->orderBy('position')->all() as $goodsImage){
                                                    echo $path = GoodsImages::getImagePath($variant['id']);
                                                    GoodsImages::images_upload($model->id, $modelOldVariant->id, [trim($path,'/')]);
                                                }

                                            }
                                            if ($modelOldVariant->status > 0) {
                                                if ($modelOldVariant->price
                                                    <= 0
                                                ) {
                                                    $modelOldVariant->status
                                                        = 0;
                                                }
                                                if (isset($shopStoreCount[$modelOldVariant->id])
                                                    && !empty($shopStoreCount[$modelOldVariant->id])
                                                ) {
                                                    $zeroCount = false;
                                                    foreach (
                                                        $shopStoreCount[$modelOldVariant->id]
                                                        as $store
                                                    ) {
                                                        if ($store->count
                                                            <= 0
                                                        ) {
//                                                        Zloradnij::printArray($store);
                                                            $zeroCount = true;
                                                        }
                                                    }
                                                    if ($zeroCount) {
                                                        $modelOldVariant->status
                                                            = 0;
                                                    }
                                                } else {
                                                    $modelOldVariant->status
                                                        = 0;
                                                }
                                            }

                                            if ($modelOldVariant->save()) {

                                                $notDeleteTagsLinks
                                                    = TagsLinks::find()->where(
                                                    ['variation_id' => $modelOldVariant->id]
                                                )->all();
                                                if ($notDeleteTagsLinks) {
                                                    foreach (
                                                        $notDeleteTagsLinks as
                                                        $itemTagLinks
                                                    ) {
                                                        $notDeleteTags
                                                            = Tags::find()
                                                            ->where(
                                                                ['id' => $itemTagLinks->tag_id]
                                                            )->one();
                                                        if (!in_array(
                                                            $notDeleteTags->group_id,
                                                            Yii::$app->params['breadcrumbsTagId']
                                                        )
                                                        ) {
                                                            $itemTagLinks->delete(
                                                            );
                                                        }
                                                    }
                                                }

                                                if (!$producerAll) {

                                                } else {
                                                    $tagsLinks = new TagsLinks(
                                                    );
                                                    $tagsLinks->variation_id
                                                        = $modelOldVariant->id;
                                                    $tagsLinks->tag_id
                                                        = $producerAll;
                                                    $tagsLinks->status = 1;
                                                    $tagsLinks->save();
                                                }

                                                if (!$countryAll) {

                                                } else {
                                                    $tagsLinks = new TagsLinks(
                                                    );
                                                    $tagsLinks->variation_id
                                                        = $modelOldVariant->id;
                                                    $tagsLinks->tag_id
                                                        = $countryAll;
                                                    $tagsLinks->status = 1;
                                                    $tagsLinks->save();
                                                }

                                                if (!empty($_POST['variations_add'][$key]['tags'])) {
                                                    foreach (
                                                        $_POST['variations_add'][$key]['tags']
                                                        as $tagCode => $tagValue
                                                    ) {
                                                        if (!in_array(
                                                            $tagCode,
                                                            Yii::$app->params['breadcrumbsTagId']
                                                        )
                                                        ) {
                                                            $tagsLinks
                                                                = new TagsLinks(
                                                            );
                                                            $tagsLinks->variation_id
                                                                = $modelOldVariant->id;
                                                            $tagsLinks->tag_id
                                                                = $tagCode;
                                                            $tagsLinks->status
                                                                = 1;

                                                            $tagsLinks->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }


                                $transaction->commit();
                                $allData = Goods::getAllDataProduct($id);

                                return $this->redirect(
                                    ['/product/update', 'id' => $model->id]
                                );


                }
            }

            if(isset($requestPost['sticker'])){
                $arSticker = $requestPost['sticker'];
                foreach ($arSticker as $key => $sticker){
                    $modelSticker = new StickerLinks();
                    $modelSticker->good_id = $id;
                    $modelSticker->sticker_id = $sticker;
                    if(!$modelSticker->save()){
                        print_r($modelSticker->getErrors());
                        die;
                    }
                }
            }


            return $this->redirect(
                ['/product/update', 'id' => $model->id]
            );
        }

        //Получаем модель оригинала
        $origModel = Goods::find()->where(['id'=>$id])->One();
        if(!$origModel){
            return false;
        }

        //Делаем новую можель и присваеиваем ей оргинал, удаляя ID
        $origModel->id = '';

        //Получаем вариации оригинальной модели
        $modelVariant = GoodsVariations::find()
            ->select([
                'id',
                'code',
                'servingforday',
                'full_name',
                'name',
                'description',
                "get_tags(`id`) AS `tags_name`",
                'price',
                'comission',
                'status',
            ])
            ->where(['good_id' => $id])
            ->all();

        //Получаем изображения вариации оринальной модели
        $modelVariantIds = [];
        foreach($modelVariant as $variant){
            $modelVariantIds[] = $variant->id;
        }

        $images = GoodsImagesLinks::find()->where(['IN','variation_id',$modelVariantIds])->all();
        $variantImages = [];
        foreach($images as $image){
            $variantImages[$image->variation_id][$image->image_id] = \Yii::$app->params['generalImagePath'] . '/' . substr(md5($image->image_id), 0, 2) . '/' . $image->image_id . '_min.jpg';
        }
        //Получаем ТЭГИ оригинальной модели
        $tagsListValue = TagsLinks::find()
            ->select('tags.*, tags_links.*')
            ->joinWith(['tags'])
            ->where(['IN','tags_links.variation_id',$modelVariantIds])
            ->andWhere(['tags_links.status' => 1])
            ->all();

        $tagsListValueByGroup = [];
        foreach($tagsListValue as $item){
            $tagsListValueByGroup[$item->variation_id][$item->tags->group_id][$item->tags->id] = $item->tags->value;
        }
        //Не знаю что это
        $countVariation = false;
        $allData = Goods::getAllDataProduct($id);
//        print_r($allData['shopsList']);die;
//        $allData['shopsList'] = [];
//        $allData['shopGroup'] = NULL;




        return $this->render('/product/copy', [
            'model' => $origModel,
            'typeProduct' => GoodsTypes::find()->all(),
            'producers' => Tags::find()->where(['group_id' => 1008])->andWhere(['status' => 1])->orderBy('value')->all(),
            'country' => Tags::find()->where(['group_id' => 1007])->andWhere(['status' => 1])->orderBy('value')->all(),
            'modelVariant' => $modelVariant,
            'tagsList' => TagsGroups::find()->where(['type' => 1])->andWhere(['status' => 1])->orderBy('position')->all(),
            'tagsListValue' => $tagsListValueByGroup,
            'tags' => new Tags(),
            'variantImages' => $variantImages,
            'countVariation' => $countVariation,
            'category' => $category,
            'shopsList' => $allData['shopsList'],
            'shopGroup' => $allData['shopGroup'],
            'shopStoreCount' => $allData['shopStoreCount'],
            'images' => $allData['images'],
            'menu' =>  $this->actionsMenu['products'],
        ]);
    }






}
