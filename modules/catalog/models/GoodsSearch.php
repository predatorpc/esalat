<?php

namespace app\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * GoodsSearch represents the model behind the search form about `app\models\Goods`.
 */
class GoodsSearch extends Goods
{
    public $producer_name;
    public $shop;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['show','id', 'type_id', 'shop_id', 'producer_id', 'country_id', 'weight_id', 'bonus', 'order', 'delay', 'count_pack', 'count_min', 'rating', 'discount', 'main', 'new', 'sale', 'user_id', 'user_last_update', 'position', 's', 'confirm', 'status'], 'integer'],
            [['code', 'full_name', 'name', 'description', 'link', 'date', 'seo_title', 'seo_description', 'seo_keywords', 'date_create', 'date_update'], 'safe'],
            [['price', 'comission'], 'number'],
            [['producer_name','shop'], 'string', 'max' => 128],
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
        $query = Goods::find();

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
            'type_id' => $this->type_id,
            'shop_id' => $this->shop_id,
            'producer_id' => $this->producer_id,
            'country_id' => $this->country_id,
            'weight_id' => $this->weight_id,
            'price' => $this->price,
            'comission' => $this->comission,
            'bonus' => $this->bonus,
            'order' => $this->order,
            'delay' => $this->delay,
            'count_pack' => $this->count_pack,
            'count_min' => $this->count_min,
            'rating' => $this->rating,
            'discount' => $this->discount,
            'main' => $this->main,
            'new' => $this->new,
            'sale' => $this->sale,
            'date' => $this->date,
            'user_id' => $this->user_id,
            'user_last_update' => $this->user_last_update,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
            'position' => $this->position,
            's' => $this->s,
            'confirm' => $this->confirm,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'seo_title', $this->seo_title])
            ->andFilterWhere(['like', 'seo_description', $this->seo_description])
            ->andFilterWhere(['like', 'seo_keywords', $this->seo_keywords]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchForProvider($params)
    {
        $shopId = UserShop::getIdentityShop();
        $query = GoodsVariations::find()
            ->from([
                'variants' => GoodsVariations::find()
                    //->where(['>','price', 0])
                    ->orderBy([
                        'price' => SORT_ASC,
                    ])
            ])
            ->select('
				goods.*,
				variants.price AS price_out,
				variants.good_id,
				producers.name AS producer_name,
				goods.price AS price_outs
			')
            ->joinWith(['producer'])
            ->where(['=','goods.shop_id', $shopId])
            ->andWhere('goods.status >= 0')
            ->andWhere('goods.show > 0')
            ->groupBy('variants.good_id')
            ->limit(20);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>'goods.id asc']
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'id',
                /*'producer_name' => [
                    'asc' => ['producers.name' => SORT_ASC],
                    'desc' => ['producers.name' => SORT_DESC],
                    'label' => 'Producer Name',
                    'default' => SORT_ASC
                ],*/
                'name',
                'price_out',
                'date_create',
                'confirm',
                'status',
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type_id' => $this->type_id,
            'shop_id' => $this->shop_id,
            //'producer_id' => $this->producer_id,
            'country_id' => $this->country_id,
            'weight_id' => $this->weight_id,
            'price' => $this->price,
            'comission' => $this->comission,
            'bonus' => $this->bonus,
            'order' => $this->order,
            'delay' => $this->delay,
            'count_pack' => $this->count_pack,
            'count_min' => $this->count_min,
            'rating' => $this->rating,
            'discount' => $this->discount,
            'main' => $this->main,
            'new' => $this->new,
            'sale' => $this->sale,
            'date' => $this->date,
            'user_id' => $this->user_id,
            'user_last_update' => $this->user_last_update,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
            'position' => $this->position,
            'confirm' => $this->confirm,
            'goods.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'goods.full_name', $this->full_name])
            ->andFilterWhere(['like', 'goods.name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'seo_title', $this->seo_title])
            ->andFilterWhere(['like', 'seo_description', $this->seo_description])
            ->andFilterWhere(['like', 'seo_keywords', $this->seo_keywords])
            //->andFilterWhere(['like', 'producers.name', $this->producer_name])
            ->andFilterWhere(['like', 'goods.date_create', $this->date_create]);

        return $dataProvider;
    }

    public static function getMissingVariants($id){
        $variants = GoodsCounts::getVariantCountList($id);
        if(!empty($variants)){
            foreach($variants as $variant){

            }
        }

    }
    public static function getVariants($id,$active = true){
        $variants = GoodsVariations::find()->where(['good_id' => $id]);
        if($active){
            $variants->andWhere(['status' => 1]);
        }
        return $variants->all();
    }


    public function searchNotDelete($params){
        $query = Goods::find()->where(['>=','status',0]);

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
            'type_id' => $this->type_id,
            'comission' => $this->comission,
            'bonus' => $this->bonus,
            'order' => $this->order,
            'delay' => $this->delay,
            'count_pack' => $this->count_pack,
            'count_min' => $this->count_min,
            'rating' => $this->rating,
            'discount' => $this->discount,
            'main' => $this->main,
            'new' => $this->new,
            'sale' => $this->sale,
            'date' => $this->date,
            'user_id' => $this->user_id,
            'user_last_update' => $this->user_last_update,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
            'position' => $this->position,
            'confirm' => $this->confirm,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'seo_title', $this->seo_title])
            ->andFilterWhere(['like', 'seo_description', $this->seo_description])
            ->andFilterWhere(['like', 'seo_keywords', $this->seo_keywords]);

        return $dataProvider;
    }

    public function searchStatusList($params){
        $query = Goods::find();
        $query->joinWith(['shop']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['shop'] = [
            'asc' => ['shop_group.name' => SORT_ASC],
            'desc' => ['shop_group.name' => SORT_DESC],
        ];
        $dataProvider->setPagination(['pageSize' => !empty($params['page-size']) ? intval($params['page-size']) : 20]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type_id' => $this->type_id,
            'comission' => $this->comission,
            'bonus' => $this->bonus,
            'order' => $this->order,
            'delay' => $this->delay,
            'count_pack' => $this->count_pack,
            'count_min' => $this->count_min,
            'rating' => $this->rating,
            'discount' => $this->discount,
            'main' => $this->main,
            'new' => $this->new,
            'sale' => $this->sale,
            'date' => $this->date,
            'user_id' => $this->user_id,
            'user_last_update' => $this->user_last_update,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
            'position' => $this->position,
            'confirm' => $this->confirm,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'seo_title', $this->seo_title])
            ->andFilterWhere(['like', 'seo_description', $this->seo_description])
            ->andFilterWhere(['like', 'seo_keywords', $this->seo_keywords])
            ->andFilterWhere(['like', 'shop_group.name', $this->shop]);
        return $dataProvider;
    }
}
