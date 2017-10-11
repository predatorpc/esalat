<?php

namespace app\modules\catalog\models;

use app\modules\common\models\Zloradnij;
use app\modules\managment\models\Shops;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $level
 * @property string $title
 * @property string $alias
 * @property integer $sort
 * @property integer $active
 *
 * @property Category $parent
 * @property Category[] $categories
 */
class Category extends \yii\db\ActiveRecord
{
    public $productSortField = 'position';
    public $pageId = 1;
    public $pageSize = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'level', 'sort', 'active','shoping_master'], 'integer'],
    //        [['parent_id','title', 'description','alias','sort'], 'required'],
            [['title', 'description','alias','sort'], 'required'],
            [['title', 'alias'], 'string', 'max' => 128],
            [['seo_description', 'anon', 'seo_keywords','seo_title','color','description'],'string'],
        //    [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['parent_id' => 'id']],
    //    [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['parent_id' => 'id']],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => Yii::t('admin', 'ID Родительской категории'),
            'level' =>  Yii::t('admin','Уровень в меню'),
            'title' =>  Yii::t('admin','Название категории'),
            'alias' =>  Yii::t('admin','Alias (Алиас)'),
            'sort' =>  Yii::t('admin','Позиция сортировка'),
            'active' =>  Yii::t('admin','Активность'),
            'description' =>  Yii::t('admin','Описание'),
            'color' =>  Yii::t('admin','Цвет'),
            'anon' =>  Yii::t('admin','Аннонимайзер'),
            //'vegan' => 'Вегетарианство',
        ];
    }

    public function setPageId($id){
        $this->pageId = $id;
    }

    public function setPageSize($id){
        $this->pageSize = $id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    public function getNextCategory(){
        return $this->hasOne(Category::className(),['parent_id' => 'parent_id'])->where(['>','sort',$this->sort])->orderBy('sort ASC');

    }

    public function getPrevCategory(){
        return $this->hasOne(Category::className(),['parent_id' => 'parent_id'])->where(['<','sort',$this->sort])->orderBy('sort DESC');
    }

    public function getFirstSiblingCategory(){
        return $this->hasOne(Category::className(),['parent_id' => 'parent_id'])->orderBy('sort ASC');
    }

    public function getLastSiblingCategory(){
        return $this->hasOne(Category::className(),['parent_id' => 'parent_id'])->orderBy('sort DESC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id'])->where(['active' => 1])->orderBy('sort');
    }

    public function getCategoriesLoad()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id'])->where(['active' => 1])->orderBy('sort');
    }

    public function getAllChildCategories(){

    }

    public function getAllChildCategoryIds(){
        $childs[] = $this->id;
        $childCategoryLevelOne = Category::find()->where(['parent_id' => $this->id,'active' => 1])->select(['id','parent_id'])->all();
        if(!$childCategoryLevelOne){
            return $childs;
        }
        foreach($childCategoryLevelOne as $levelOne){
            $childs[] = $levelOne->id;
        }
        $childCategoryLevelTwo = Category::find()->where(['IN','parent_id', $childs])->andWhere(['active' => 1])->select(['id','parent_id'])->all();
        if(!$childCategoryLevelTwo){
            return array_unique($childs);
        }
        foreach($childCategoryLevelTwo as $levelTwo){
            $childs[] = $levelTwo->id;
        }
        return array_unique($childs);
    }

    public function getItemsForProfile() {
    }

    public function findCategoryProducts($idList,$params){
        $query = GoodsVariations::find()
            ->select([
                'goods.name AS name',

                'variants.price AS productPrice',
                'variants.comission AS productCommission',
                'variants.id AS variantId',

                'goods.id AS productId',
                'goods.discount AS productDiscount',
                'goods.count_pack AS countPack',
                'shops.comission_id AS commissionId',

                'category_links.category_id AS categoryId',
            ])
            ->from([
                'variants' => GoodsVariations::find()
                    ->where(['status' => 1])
                    ->orderBy([
                        'price' => SORT_ASC,
                    ])
            ])
            ->leftJoin(Goods::tableName(),'variants.good_id = goods.id')
            ->leftJoin(CategoryLinks::tableName(),'category_links.product_id = goods.id')
            ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
            ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
            ->leftJoin('shop_group_related','shop_group_related.shop_group_id = shop_group.id')
            ->leftJoin(Shops::tableName(),'shops.id = shop_group_related.shop_id')
            ->where(['IN','category_links.category_id',$idList])
            ->andWhere([
                'shops.status' => 1,
                'shop_group.status' => 1,
                'goods.status' => 1,
                'goods.show' => 1,
                'goods.confirm' => 1,
                'variants.status' => 1,
            ])
            ->andWhere(['OR',
                ['>','good_count(`goods`.`id`, NULL)',1],
                ['IS','good_count(`goods`.`id`, NULL)',NULL]
            ])
            ->groupBy('goods.id');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'attributes'=>[
                    'name',
                    'productPrice',
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
                'defaultPageSize' => 20,
                'pageParam' => 'page',
                'forcePageParam' => false,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            //'parent_id' => $this->parent_id,
            //'level' => $this->level,
            //'sort' => $this->sort,
            //'active' => $this->active,
        ]);
        /*
                $query->andFilterWhere(['like', 'title', $this->title])
                    ->andFilterWhere(['like', 'alias', $this->alias]);
        */
        return $dataProvider;
    }

    public function findProduct($id){
        $query = GoodsVariations::find()
            ->select([
                'goods.name AS name',

                'variants.price AS productPrice',
                'variants.comission AS productCommission',
                'variants.id AS variantId',

                'goods.id AS productId',
                'goods.discount AS productDiscount',
                'goods.count_pack AS countPack',
//                'shops.comission_id AS commissionId',

                'category_links.category_id AS categoryId',
            ])
            ->from([
                'variants' => GoodsVariations::find()
                    ->where(['status' => 1])
                    ->orderBy([
                        'price' => SORT_ASC,
                    ])
            ])
            ->leftJoin(Goods::tableName(),'variants.good_id = goods.id')
            ->leftJoin(CategoryLinks::tableName(),'category_links.product_id = goods.id')
//            ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
//            ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
//            ->leftJoin('shop_group_related','shop_group_related.shop_group_id = shop_group.id')
//            ->leftJoin(Shops::tableName(),'shops.id = shop_group_related.shop_id')
            ->where([
//                'shops.status' => 1,
//                'shop_group.status' => 1,
                'goods.id' => $id,
                'goods.status' => 1,
                'goods.show' => 1,
                'goods.confirm' => 1,
                'variants.status' => 1,
            ])
//            ->andWhere(['OR',
//                ['>','good_count(`goods`.`id`, NULL)',1],
//                ['IS','good_count(`goods`.`id`, NULL)',NULL]
//            ])
            ->groupBy('goods.id');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'attributes'=>[
                    'name',
                    'productPrice',
                ]
            ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public function findVariations($productsIds){
        $showTagsList = (new Query())->from('tags_groups')->where(['status' => 1,'show' => 1])->indexBy('id')->all();
        $showTagsListIds = [];
        foreach($showTagsList as $item){
            $showTagsListIds[] = $item['id'];
        }
        $variation = [];
        if(is_array($productsIds)){
            $query = (new Query())
                ->from('goods_variations')
                ->select([
                    'goods_variations.*',
                    'tags.id AS tagValueId',
                    'tags.value AS tagValue',
                    'tags_groups.id AS tagGroupId',
                    'tags_groups.name AS tagName',

                    'good_count(`goods_variations`.`good_id`, `goods_variations`.`id`) AS productCount',
                ])
                ->leftJoin('tags_links','tags_links.variation_id = goods_variations.id')
                ->leftJoin('tags','tags_links.tag_id = tags.id')
                ->leftJoin('tags_groups','tags.group_id = tags_groups.id')
                ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods_variations.good_id')
                ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
                ->leftJoin('shop_group_related','shop_group_related.shop_group_id = shop_group.id')
                ->leftJoin(Shops::tableName(),'shops.id = shop_group_related.shop_id')
                ->where(['IN','goods_variations.good_id',$productsIds])
                ->andWhere([
                    'goods_variations.status' => 1,
                    'tags_groups.status' => 1,
                    'tags.status' => 1,
                    'shops.status' => 1,
                    'shop_group.status' => 1,
                ])
                ->andWhere(['OR',
                    ['>','good_count(`goods_variations`.`good_id`, `goods_variations`.`id`)',0],
//                    ['IS','good_count(`goods_variations`.`good_id`, `goods_variations`.`id`)',NULL]
                ])
                ->all();

            if(!$query){
                return false;
            }else{
                foreach($query as $variant){
                    $variation[$variant['good_id']][$variant['id']]['productCount'] = $variant['productCount'];
                    if(in_array($variant['tagGroupId'],$showTagsListIds)){
                        $variation[$variant['good_id']][$variant['id']]['props'][$variant['tagGroupId']][$variant['tagValueId']] = $variant['tagValue'];
                    }
                }
            }
        }
        return $variation;
    }

    public static function getCategoryPath($id,$data = false){
        if(!$data){
            $data = Category::find()->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
        }

        if(empty($data[$id])){
            return Yii::$app->params['catalogPath'] . '/new/';
        }
        $level = $data[$id]['level'];

        $path = '';
        while($level >= 0){
            $path = $data[$id]['alias'] . '/' . $path;
            $id = $data[$id]['parent_id'];

            $level--;
        }
        return Yii::$app->params['catalogPath'] . '/' . $path;
    }

    public function getCategoryCatalogPath(){
        $level = $this->level;
        $path = $this->alias . '/';
        $element = $this;
        while($level >= 0){
            //Zloradnij::print_arr($element);die();
            if(!empty($element->parent)){
                $path = $element->parent->alias . '/' . $path;
                $element = $element->parent;
            }
            $level--;
        }
        return Yii::$app->params['catalogPath'] . '/' . $path;
    }

    public function getCatalogPath(){
        $path = $this->alias . '/';
        if($this->level > 0){
            $path = $this->parent->alias .'/'. $path;
        }
        if($this->level > 1){
            $path = $this->parent->parent->alias .'/'. $path;
        }
        return Yii::$app->params['catalogPath'] . '/' . $path;
    }

    public static function changeView($id){
        return $id ? 'product':'view';
    }

    public static function getFullCategoriesStructureList($active = false){
        $query = Category::find();
        if(!$active){

        }else{
            $query->where(['active' => 1]);
        }
        $levelOneSql = $query->all();
        $levelCategory = [];
        foreach($levelOneSql as $categoryItem){
            $levelCategory[$categoryItem->id] = $categoryItem;
        }
        foreach($levelCategory as $categoryItem){
            if($categoryItem->level == 2){
                $categoryOptions[$categoryItem->id] = [
                    'title' => (!empty($levelCategory[$levelCategory[$categoryItem->parent_id]->parent_id]->title) ? $levelCategory[$levelCategory[$categoryItem->parent_id]->parent_id]->title : '(+Erorr+)') . ' - ' .
                        $levelCategory[$categoryItem->parent_id]->title . ' - ' .
                        $categoryItem->title,

                    'id' => $categoryItem->id,
                ];
            }
            if($categoryItem->level == 1){
                $categoryOptions[$categoryItem->id] = [
                    'title' => $levelCategory[$categoryItem->parent_id]->title . ' - ' . $categoryItem->title,
                    'id' => $categoryItem->id,
                ];
            }
            if($categoryItem->level == 0){
                $categoryOptions[$categoryItem->id] = [
                    'title' => $categoryItem->title,
                    'id' => $categoryItem->id,
                ];
            }
        }
        sort($categoryOptions);

        return $categoryOptions;
    }

    public function categoryProductsQuery($new = false){
        /*$query = Goods::find()
            ->leftJoin('goods_variations','goods_variations.good_id = goods.id')
            ->leftJoin('category_links','category_links.product_id = goods.id')
            ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
            ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
            ->where([
                'IN','category_links.category_id',$this->allChildCategoryIds,
            ])
            ->andWhere(['shop_group.status' => 1])
            ->groupBy('goods.id');

        if(!$new){
            return $query->orderBy('position');
        }else{
            return $query->orderBy('goods.id DESC')->andWhere(['goods.new' => 1]);
        }*/
        if(!$new){
            $query = Goods::find()
                ->leftJoin('goods_variations','goods_variations.good_id = goods.id')
                ->leftJoin('category_links','category_links.product_id = goods.id')
                ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
                ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
                ->where([
                    'IN','category_links.category_id',$this->allChildCategoryIds,
                ])
                ->andWhere(['shop_group.status' => 1])
                ->groupBy('goods.id');
            return $query->orderBy('position');

        }
        else{
            $query = Goods::find()
                ->leftJoin('goods_variations','goods_variations.good_id = goods.id')
                ->where(['goods.new' => 1])
                ->orderBy('goods.id DESC')
                ->groupBy('goods.id');
            return $query;
        }

    }


    public function getCategoryProducts(){
        return $this->categoryProductsQuery() ? $this->categoryProductsQuery()->all() : false;
    }

    public function getCategoryActiveProductsQuery($new = false){
        return $this->categoryProductsQuery() ? $this->categoryProductsQuery($new)->andWhere(['goods.status' => 1,'goods.show' => 1,'goods.confirm' => 1,'goods_variations.status' => 1]) : false;
    }

    public function getProductsClear(){
            $session = Yii::$app->session;
            return $this->hasMany(Goods::className(), ['id' => 'product_id'])
                ->where(['status' => 1, 'show' => 1, 'confirm' => 1])
                ->viaTable(CategoryLinks::tableName(), ['category_id' => 'id'])->orderBy('position ASC');
    }

    public function getCategoryLinks(){
        return $this->hasMany(CategoryLinks::className(),['category_id' => 'id']);
    }

    public function getAllProductsClear(){
        $result = [];
        $productsClear = $this->productsClear;

        foreach ($this->categories as $category) {
            $productsClear = array_merge($productsClear,$category->productsClear);
            if(!empty($category->categories)){
                foreach ($category->categories as $categoryNext) {
                    $productsClear = array_merge($productsClear,$categoryNext->productsClear);
                }
            }
        }
        //$productsClear = $this->sequentiallySort($productsClear);
        $i = 1;
        foreach ($productsClear as $id => $product) {
            if($i > ($this->pageId - 1) * $this->pageSize && $i <= $this->pageId * $this->pageSize){
                $result[] = $product;
            }
            $i++;
        }
        //$result = $this->sequentiallySort($result);
       // print_arr($result);
        return $result;
    }

    public function getCategoryActiveProducts($params,$new = false, $pageSize = 20){
        $query = $this->getCategoryActiveProductsQuery() ? $this->getCategoryActiveProductsQuery($new) : false;
        if($new){
            $pageSize = 50;
        }
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'attributes'=>[
                    'name',
                ]
            ],
            'pagination' => [
                'pageSize' => $pageSize,
                'defaultPageSize' => 20,
                'pageParam' => 'page',
                'forcePageParam' => false,
            ]
        ]);
//        $dataProvider->sort->attributes['name'] = [
//            'asc' => ['name' => SORT_ASC],
//            'desc' => ['name' => SORT_DESC],
//        ];
        $dataProvider->sort->attributes['productPrice'] = [
            'asc' => ['(goods_variations.price * goods.count_pack * (1 + goods_variations.comission/100))' => SORT_ASC],
            'desc' => ['(goods_variations.price * goods.count_pack * (1 + goods_variations.comission/100))' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            //'parent_id' => $this->parent_id,
            //'level' => $this->level,
            //'sort' => $this->sort,
            //'active' => $this->active,
        ]);
        /*
                $query->andFilterWhere(['like', 'title', $this->title])
                    ->andFilterWhere(['like', 'alias', $this->alias]);
        */
        return $dataProvider;
    }

    public function getCategoryActiveProductsMini($params,$new = false){
        $query = $this->getCategoryActiveProductsQuery() ? $this->getCategoryActiveProductsQuery($new) : false;

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'attributes'=>[
                    'name',
                ]
            ],
            //'pagination' => [
//                'pageSize' => 20,
             //   'defaultPageSize' => 20,
  //              'pageParam' => 'page',
            //    'forcePageParam' => false,
            //]
        ]);
//        $dataProvider->sort->attributes['name'] = [
//            'asc' => ['name' => SORT_ASC],
//            'desc' => ['name' => SORT_DESC],
//        ];
        $dataProvider->sort->attributes['productPrice'] = [
            'asc' => ['(goods_variations.price * goods.count_pack * (1 + goods_variations.comission/100))' => SORT_ASC],
            'desc' => ['(goods_variations.price * goods.count_pack * (1 + goods_variations.comission/100))' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            //'parent_id' => $this->parent_id,
            //'level' => $this->level,
            //'sort' => $this->sort,
            //'active' => $this->active,
        ]);
        /*
                $query->andFilterWhere(['like', 'title', $this->title])
                    ->andFilterWhere(['like', 'alias', $this->alias]);
        */
        return $dataProvider;
    }

    public function sequentiallySort($list){
        uasort($list, ['self', 'objectSort']);
        return $list;
    }

    public function objectSort($listA, $listB){
        if ($listA->{$this->productSortField} === $listB->{$this->productSortField}) return 0;
        return $listA->{$this->productSortField} < $listB->{$this->productSortField} ? -1 : 1;
    }

    public function findByAlias($alias){
        $alias = explode('/',$alias);
        if(is_array($alias)){
            if(!empty($alias[0])){
                $category = Category::find()->where(['alias' => $alias[0],'active' => 1])->one();
            }
            if(!empty($alias[1]) && !empty($category)){
                $category = Category::find()->where(['alias' => $alias[1],'active' => 1,'parent_id' => $category->id])->one();
            }
            if(!empty($alias[2]) && !empty($category)){
                $category = Category::find()->where(['alias' => $alias[2],'active' => 1,'parent_id' => $category->id])->one();
            }
        }
        return !empty($category) ? $category : false;
    }

    public function getWishListActiveProducts($params, $pageSize = 20){
        $query = $query = Goods::find()
            ->leftJoin('goods_variations','goods_variations.good_id = goods.id')
            ->where(['goods.iwish' => 1])
            ->orderBy('goods.id DESC')
            ->groupBy('goods.id');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'attributes'=>[
                    'name',
                ]
            ],
            'pagination' => [
                'pageSize' => $pageSize,
                'defaultPageSize' => 20,
                'pageParam' => 'page',
                'forcePageParam' => false,
            ]
        ]);
//        $dataProvider->sort->attributes['name'] = [
//            'asc' => ['name' => SORT_ASC],
//            'desc' => ['name' => SORT_DESC],
//        ];
        $dataProvider->sort->attributes['productPrice'] = [
            'asc' => ['(goods_variations.price * goods.count_pack * (1 + goods_variations.comission/100))' => SORT_ASC],
            'desc' => ['(goods_variations.price * goods.count_pack * (1 + goods_variations.comission/100))' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            //'parent_id' => $this->parent_id,
            //'level' => $this->level,
            //'sort' => $this->sort,
            //'active' => $this->active,
        ]);
        /*
                $query->andFilterWhere(['like', 'title', $this->title])
                    ->andFilterWhere(['like', 'alias', $this->alias]);
        */
        return $dataProvider;
    }

    static function getChildrenCategory($category_id, $arCategory){
        if(Category::find()->where(['parent_id'=>$category_id])->All()){
            foreach (Category::find()->where(['parent_id'=>$category_id])->All() as $category){
                $arCategory[] = $category->id;
                $arCategory = Category::getChildrenCategory($category->id,$arCategory);
            }
        }

        return $arCategory;
    }


}