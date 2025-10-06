<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Customer;

/**
 * CustomerSearch represents the model behind the search form of `app\models\Customer`.
 */
class CustomerSearch extends Customer
{
    public $username;
    public $from_date;
    public $to_date;
    public $package_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'current_package', 'country_id', 'status'], 'integer'],
            [['name', 'email', 'mobile_no', 'referral_code', 'created_at', 'updated_at', 'username', 'from_date', 'to_date', 'package_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Customer::find();

        // add conditions that should always apply here
        $query->joinWith(['user', 'country', 'currentPackage']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'customer.id' => $this->id,
            'customer.user_id' => $this->user_id,
            'customer.current_package' => $this->current_package,
            'customer.country_id' => $this->country_id,
            'customer.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'customer.name', $this->name])
            ->andFilterWhere(['like', 'customer.email', $this->email])
            ->andFilterWhere(['like', 'customer.mobile_no', $this->mobile_no])
            ->andFilterWhere(['like', 'customer.referral_code', $this->referral_code])
            ->andFilterWhere(['like', 'customer.created_at', $this->created_at])
            ->andFilterWhere(['like', 'customer.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'user.username', $this->username]);

        // Date range filtering
        if (!empty($this->from_date)) {
            $query->andFilterWhere(['>=', 'customer.created_at', $this->from_date . ' 00:00:00']);
        }
        if (!empty($this->to_date)) {
            $query->andFilterWhere(['<=', 'customer.created_at', $this->to_date . ' 23:59:59']);
        }

        // Package filtering
        if (!empty($this->package_id)) {
            $query->andFilterWhere(['customer.current_package' => $this->package_id]);
        }

        return $dataProvider;
    }
}