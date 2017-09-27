<?php

namespace app\modules\common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Profile;

/**
 * ProfileSearch represents the model behind the search form about `app\modules\common\models\Profile`.
 */
class ProfileSearch extends Profile
{

    public $rate;
    //public $username;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'age', 'gender', 'pets', 'children', 'car', 'status'], 'integer'],
            [['created_at', 'updated_at', 'rate'], 'safe'],
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
        $query = Profile::find()
            ->leftJoin('users', 'users.id = profile.user_id')
            ->leftJoin('profile_links', 'profile_links.profile_id = profile.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['profileLinks.rate'] = [
            'asc' => ['profile_links.rate' => SORT_ASC],
            'desc' => ['profile_links.rate' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['user_id'] = [
            'asc' => ['users.name' => SORT_ASC],
            'desc' => ['users.name' => SORT_DESC],
        ];


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'age' => $this->age,
            'gender' => $this->gender,
            'pets' => $this->pets,
            'children' => $this->children,
            'car' => $this->car,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'profile.status' => $this->status,
        ]);

        //$query->andFilterWhere(['LIKE', 'users.name', '%' . $this->username . '%']);

        return $dataProvider;
    }
}
