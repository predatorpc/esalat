<?php

namespace app\modules\catalog\models;

use app\modules\common\models\Zloradnij;
use Yii;

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
class Catalog extends \yii\db\ActiveRecord
{
    public function getCategory(){

    }

    public function findByAlias($alias)
    {
        return Category::findOne(['alias'=>$alias]);
    }

    public static function findBreadcrumbs($aliases,$data = false)
    {
        if(empty(end($aliases))){
            array_pop($aliases);
        }

        $categories = Category::find()->where(['IN','alias',$aliases])->indexBy('id')->orderBy('level')->asArray()->all();

        if(!$categories){
            return false;
        }

        $flag = 0;
        foreach($categories as $i => $category){
            if($flag > 0){
                if(empty($categories[$category['parent_id']])){
                    unset($categories[$i]);
                }
            }
            $flag = 1;
        }

        return $categories;
    }

    public static function getChilds($id){
        return Category::find()->where(['parent_id' => $id])->all();
    }

    public function getChildsObjects(){
        return $this->hasMany(Category::classname(),['parent_id' => 'id']);
    }

    public static function getChildsIds($id){
        $childs = false;
        $childCategoryLevelOne = Category::find()->where(['parent_id' => $id])->select(['id','parent_id'])->all();
        if(!$childCategoryLevelOne){
            return false;
        }
        foreach($childCategoryLevelOne as $levelOne){

            $childs[] = $levelOne->id;
            $childs[] = $levelOne->parent_id;
        }
        $childCategoryLevelOne = Category::find()->where(['IN','parent_id', $childs])->select(['id','parent_id'])->all();
        if(!$childCategoryLevelOne){
            return $childs;
        }
        foreach($childCategoryLevelOne as $levelOne){
            $childs[] = $levelOne->id;
        }
        return $childs;
    }

    public function getParent() {
        return (new Category)->hasOne(self::classname(),['id' => 'parent_id'])->from(Category::tableName() . ' AS parent');
    }
    public function getParentTitle() {
        return ($this->parent)?$this->parent->title:'';
    }

    public function findByAttributes($params){
        return Category::findOne($params);

    }

    public static function buildTree(array &$elements, array &$urls, $parentId = 0) {
        $branch = array();
        $urls[0] = '/' . Yii::$app->params['catalogPath'];

        foreach ($elements as $key=>$element) {
            if ($element['parent_id'] == $parentId) {

                $urls[$element['id']] = (isset($urls[$parentId])) ? $urls[$parentId] .'/'. $element['alias']:'/catalog/' . $element['alias'] . '/';

                $children = self::buildTree($elements,$urls,$element['id']);
                if ($children) {
                    $element['items'] = $children;
                }

                $branch[$element['id']] = $element;
                $branch[$element['id']]['label'] = $element['title'];
                $branch[$element['id']]['url'] = (isset($urls[$parentId]))?$urls[$parentId] .'/'. $element['alias'] . '/':'/catalog/' . $element['alias'];
                //unset($elements[$element['id']]);
            }
        }
        return $branch;
    }

    public static function activityTest($ids,$variantIds = false){
        $query = GoodsVariations::find()
            ->select([
                'goods.name AS name',

                'goods_variations.price AS productPrice',
                'goods_variations.id AS variantId',

                'goods.id AS productId',
                'category_links.category_id AS categoryId',
            ])
            ->leftJoin(Goods::tableName(),'goods_variations.good_id = goods.id')
            ->leftJoin(CategoryLinks::tableName(),'category_links.product_id = goods.id')
            ->leftJoin(Category::tableName(),'category_links.category_id = category.id')
            ->leftJoin(Shops::tableName(),'goods.shop_id = shops.id')
            ->where(['IN','goods.id',$ids]);

        if(!$variantIds){

        }else{
            $query->andWhere(['IN','goods_variations.id',$variantIds]);
        }

        $query->andWhere([
            'shops.status' => 1,
            'goods.status' => 1,
            'goods.show' => 1,
            'goods.confirm' => 1,
            'goods_variations.status' => 1,
            'category.active' => 1,
        ])
            ->andWhere(['OR',
                ['>','good_count(`goods`.`id`, NULL)',1],
                ['IS','good_count(`goods`.`id`, NULL)',NULL]
            ]);
        //->groupBy('goods.id');

        $query = $query->all();

        if(!$query){
            return false;
        }
        $response = [];
        foreach($query as $product){
            $response[] = $product->variantId;
        }

        return $response;
    }

    // Подсчет количетсво товара в категории;
    public static function getCategoryGoodsCount($id=null) {
        if(!empty($id)) {

            $catalogAll = Catalog::getChildsIds($id);

            $counts = CategoryLinks::find()->leftJoin('goods', 'goods.id=category_links.product_id')
                ->where(['IN', 'category_links.category_id', (!empty($catalogAll) ? $catalogAll : $id)])->andwhere(['goods.status' => 1, 'goods.show' => 1])
                ->count('category_links.id');
            return $counts;
        }else{
            return false;
        }

    }
}