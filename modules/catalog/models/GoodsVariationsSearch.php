<?php

namespace app\modules\catalog\models;

use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

//use app\models\GoodsVariations;

/**
 * GoodsVariationsSearch represents the model behind the search form about `app\models\GoodsVariations`.
 */
class GoodsVariationsSearch extends GoodsVariations
{
    public $count;
public $show;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'good_id', 'status', 'servingforday'], 'integer'],
            [['code', 'full_name', 'name', 'description','count', 'show'], 'safe'],
            [['price', 'comission'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = GoodsVariations::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'good_id' => $this->good_id,
            'price' => $this->price,
            'comission' => $this->comission,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'producer_name', $this->producer_name]);

        return $dataProvider;
    }

    public function searchCategoryProduct($params)
    {   $category_id = $params['category'];
        $arCategory = [];
        $level = Category::find()->select('level')->where(['id'=> $category_id])->asArray()->One();

        if($level['level'] == 0){
            $arCategory1 = Category::find()->select('id')->where(['parent_id' => $category_id])->asArray()->All();
            $arCategory1 = ArrayHelper::getColumn($arCategory1, 'id');
            $arCategory2 = Category::find()->select('id')->where(['IN', 'parent_id', $arCategory1])->asArray()->All();
            $arCategory2 = ArrayHelper::getColumn($arCategory2, 'id');
            $arCategory = ArrayHelper::merge($arCategory1,$arCategory2);
        }elseif($level['level'] == 1){
            $arCategory2 = Category::find()->select('id')->where(['parent_id' => $category_id])->orWhere(['id'=>$category_id])->asArray()->All();
            $arCategory2 = ArrayHelper::getColumn($arCategory2, 'id');
            $arCategory = $arCategory2;
        }elseif ($level['level'] == 2){
            $arCategory[] = $category_id;
        }

        $arGoods = CategoryLinks::find()->select('product_id')->where(['IN','category_id', $arCategory])->asArray()->All();
        $arGoods = ArrayHelper::getColumn($arGoods, 'product_id');
        $query = GoodsVariations::find();
    	$query->select('goods.show as show, goods_variations.*');
        $query->leftJoin('goods','goods.id = goods_variations.good_id');
        $query->andWhere(['IN', 'good_id', $arGoods]);
        
        //$query->andWhere(['status'=>1]ZAsw23);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'good_id' => $this->good_id,
            'price' => $this->price,
            'comission' => $this->comission,
            'status' => $this->status,
    	    'show' => $this->show,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'producer_name', $this->producer_name]);

        return $dataProvider;
    }

    public function searchByShop($shopId, $params)
    {
        //$goods = Goods::find()->where(['shop_id'=>$shopId])->asArray()->all();
        $query = GoodsVariations::find()
            ->select('goods_variations.*, goods_counts.count')
            ->from(['shops', 'shops_stores', 'goods_counts'])
            ->leftJoin('goods_variations', 'goods_variations.id = goods_counts.variation_id')
            ->where(['shops.id'=>$shopId])
            ->andWhere('shops_stores.shop_id = shops.id')
            ->andWhere('goods_counts.store_id = shops_stores.id')
            //->andWhere(['goods_variations.status'=>1])

            //->where(['good_id'=>array_column($goods, 'id')])
            //->with('countsVariation')
            ->with('product');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'good_id' => $this->good_id,
            'price' => $this->price,
            'comission' => $this->comission,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'producer_name', $this->producer_name]);

        //Zloradnij::print_arr($query);die();

        return $dataProvider;
    }
}
