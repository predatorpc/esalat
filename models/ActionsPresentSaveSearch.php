<?php

namespace app\models;

use app\modules\shop\models\Orders;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ActionsPresentSave;

/**
 * ActionsPresentSaveSearch represents the model behind the search form about `app\models\ActionsPresentSave`.
 */
class ActionsPresentSaveSearch extends ActionsPresentSave
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'basket_id', 'present', 'status'], 'integer'],
//            [['basket_id', 'status'], 'integer'],
            [['card_number', 'create_date', 'update_date', 'bought_date'], 'safe'],
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
        $query = ActionsPresentSave::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        //print_r($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
//            'id' => $this->id,
//            'user_id' => $this->user_id,
            'basket_id' => $this->basket_id,
            //'present' => $this->present,
            //          'create_date' => $this->create_date,
            //        'update_date' => $this->update_date,
            //      'bought_date' => $this->bought_date,
            'status'    => $this->status,
        ]);

        if (isset($params['ActionsPresentSaveSearch']['present']) && is_numeric($params['ActionsPresentSaveSearch']['present'])) {
            $order = Orders::find()->where(['id' => $params['ActionsPresentSaveSearch']['present']])->one()->basket_id;
            if (!empty($order)) {
                $query->andFilterWhere(['basket_id' => $order]);
            }
        }

        $query->andFilterWhere(['like', 'card_number', $this->card_number]);


        return $dataProvider;
    }
}
