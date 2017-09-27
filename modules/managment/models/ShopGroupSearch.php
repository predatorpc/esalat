<?php

namespace app\modules\managment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ShopGroupSearch represents the model behind the search form about `app\models\ShopGroup`.
 */
class ShopGroupSearch extends \app\modules\managment\models\ShopGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'comission_id', 'created_at', 'updated_at', 'created_user', 'updated_user', 'status'], 'integer'],
            [['name'], 'safe'],
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
        $query = ShopGroup::find();

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
            'comission_id' => $this->comission_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_user' => $this->created_user,
            'updated_user' => $this->updated_user,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
