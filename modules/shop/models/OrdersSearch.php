<?php

namespace app\modules\shop\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\shop\models\Orders;
use yii\db\Query;

/**
 * OrdersSearch represents the model behind the search form about `app\models\Orders`.
 */
class OrdersSearch extends Orders
{
    public $count;
    public $goodName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'code_id', 'type', 'call_status', 'status'], 'integer'],
            [['extremefitness'], 'number'],
            [['goodName','comments', 'comments_call_center', 'date'], 'safe'],
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
        $query = Orders::find();

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
            'user_id' => $this->user_id,
            'code_id' => $this->code_id,
            'type' => $this->type,
            'extremefitness' => $this->extremefitness,
            'date' => $this->date,
            'call_status' => $this->call_status,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'comments_call_center', $this->comments_call_center]);

        return $dataProvider;
    }

    public function getShopReport($shopIds,$params = []){

        $products = (new Query())
            ->from('orders')
            ->select([
                //'orders.id AS orderId',
                //'orders_groups.id AS orderGroupId',
                //'orders_items.id AS orderItemId',
                'orders.id AS id',
                'goods.id AS goodId',
                'goods.name AS goodName',
                'SUM(`orders_items`.`count`) AS count',
                'get_tags(`goods_variations`.`id`) AS `variantParams`',
            ])
            ->leftJoin('orders_groups','orders.id = orders_groups.order_id')
            ->leftJoin('orders_items','orders_groups.id = orders_items.order_group_id')
            ->leftJoin('goods','goods.id = orders_items.good_id')
            ->leftJoin('goods_variations','orders_items.variation_id = goods_variations.id')

            ->leftJoin('shops_stores','shops_stores.id = orders_items.store_id')
            ->leftJoin('shops','shops.id = shops_stores.shop_id')

            ->where(['IN','shops.id',$shopIds])

            ->groupBy('goods.id');


        if(isset($params['orders-provider-date-start']) && !empty($params['orders-provider-date-start'])){
            $products
                ->andWhere(['>=','orders_groups.delivery_date',$params['orders-provider-date-start']]);
        }
        if(isset($params['orders-provider-date-stop']) && !empty($params['orders-provider-date-stop'])){
            $products
                ->andWhere(['<=','orders.date',$params['orders-provider-date-stop']]);
        }
        if(isset($params['orders-provider-status']) && !empty($params['orders-provider-status']) && $params['orders-provider-status'] != 'all'){
            $products
                ->andWhere(['orders_items.status_id' => $params['orders-provider-status']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $products,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'count' => SORT_DESC,
                ]
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'goodName',
                'count',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $products->andFilterWhere([

        ]);

        $products->andFilterWhere(['like', 'goods.name', $this->goodName]);

        return $dataProvider;
    }
}
