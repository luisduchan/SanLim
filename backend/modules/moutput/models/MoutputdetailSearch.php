<?php

namespace backend\modules\moutput\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\moutput\models\MaterialOutDetail;

/**
 * MoutputdetailSearch represents the model behind the search form about `backend\modules\moutput\models\MaterialOutDetail`.
 */
class MoutputdetailSearch extends MaterialOutDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['division_code', 'workcenter_code', 'machine_code', 'item_code', 'document_no', 'uom', 'create_date', 'last_update'], 'safe'],
            [['used_mass'], 'number'],
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
        $query = MaterialOutDetail::find();

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
            'used_mass' => $this->used_mass,
            'create_date' => $this->create_date,
            'last_update' => $this->last_update,
        ]);

        $query->andFilterWhere(['like', 'division_code', $this->division_code])
            ->andFilterWhere(['like', 'workcenter_code', $this->workcenter_code])
            ->andFilterWhere(['like', 'machine_code', $this->machine_code])
            ->andFilterWhere(['like', 'item_code', $this->item_code])
            ->andFilterWhere(['like', 'document_no', $this->document_no])
            ->andFilterWhere(['like', 'uom', $this->uom]);

        return $dataProvider;
    }
}
