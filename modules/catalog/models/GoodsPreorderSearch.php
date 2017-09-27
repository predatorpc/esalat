<?php

namespace app\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\catalog\models\GoodsPreorder;

/**
 * GoodsPreorderSearch represents the model behind the search form about `app\modules\catalog\models\GoodsPreorder`.
 */
class GoodsPreorderSearch extends GoodsPreorder
{
    public $summ=0;
    public $addsumm=0;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id',  'count', 'status', 'summ', 'addsumm'], 'integer'],
            [['good_variant_id',], 'string'],
            [['date'], 'safe'],
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
        if((isset($params['GoodsPreorderSearch']['dateStart'])) && (strtotime($params['GoodsPreorderSearch']['dateStart'])>0) && (isset($params['GoodsPreorderSearch']['dateEnd'])) && (strtotime($params['GoodsPreorderSearch']['dateEnd'])>0) ){
            $query = GoodsPreorder::find()
                ->select('goods_preorder.*, SUM(goods_preorder.count)as summ, count(`goods_preorder`.`id`) as addsumm')
                ->where(['>', 'count', '0'])
                ->groupBy('good_variant_id')
                ->with('variation');
        }
        else {
            $query = GoodsPreorder::find()
                ->where(['>', 'count', '0'])
                ->with('variation');
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'good_variant_id' => $this->good_variant_id,
            'count' => $this->count,
            //'date' => $this->date,
            'status' => $this->status,
        ]);
        if(!empty($params['GoodsPreorderSearch']['good_variant_id'])){
            $goodsId = array_column(Goods::find()->where(['like', 'name', $params['GoodsPreorderSearch']['good_variant_id']])->asArray()->all(), 'id');
            $goodsVariant = array_column(GoodsVariations::find()->where(['good_id'=>$goodsId])->all(), 'id');
            $query->andFilterWhere(['IN','good_variant_id', $goodsVariant]);
        }
        if((isset($params['GoodsPreorderSearch']['dateStart'])) && (strtotime($params['GoodsPreorderSearch']['dateStart'])>0) && (isset($params['GoodsPreorderSearch']['dateEnd'])) && (strtotime($params['GoodsPreorderSearch']['dateEnd'])>0) ){
            $query ->andFilterWhere(['between', 'goods_preorder.date', Date('Y-m-d 00:00:00', (strtotime($params['GoodsPreorderSearch']['dateStart']))), Date('Y-m-d 23:59:59', strtotime($params['GoodsPreorderSearch']['dateEnd']))]);
        }



        if((isset($params['GoodsPreorderSearch']['date'])) && strtotime($params['GoodsPreorderSearch']['date'])>0){
            $query ->andFilterWhere(['between', 'goods_preorder.date', Date('Y-m-d 00:00:00', (strtotime($params['GoodsPreorderSearch']['date']))), Date('Y-m-d 23:59:59', strtotime($params['GoodsPreorderSearch']['date']))]);
        }

        return $dataProvider;
    }
}
