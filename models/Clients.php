<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property int $chart_account_id
 * @property int|null $sales_person_id
 * @property float|null $discount
 * @property float|null $initial_balance
 * @property int|null $pvp_id
 * @property bool|null $manual_pvp
 * @property bool|null $exportation
 * @property int|null $cost_center_id
 * @property bool|null $credit
 * @property int $person_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 *
 * @property Person $person
 * @property Pvp $pvp
 */
class Clients extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chart_account_id', 'person_id'], 'required'],
            [['chart_account_id', 'sales_person_id', 'pvp_id', 'cost_center_id', 'person_id'], 'default', 'value' => null],
            [['chart_account_id', 'sales_person_id', 'pvp_id', 'cost_center_id', 'person_id'], 'integer'],
            [['discount', 'initial_balance'], 'number'],
            [['manual_pvp', 'exportation', 'credit'], 'boolean'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_id' => 'id']],
            [['pvp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pvp::className(), 'targetAttribute' => ['pvp_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chart_account_id' => 'Chart Account ID',
            'sales_person_id' => 'Sales Person ID',
            'discount' => 'Discount',
            'initial_balance' => 'Initial Balance',
            'pvp_id' => 'Pvp ID',
            'manual_pvp' => 'Manual Pvp',
            'exportation' => 'Exportation',
            'cost_center_id' => 'Cost Center ID',
            'credit' => 'Credit',
            'person_id' => 'Person ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[Person]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    /**
     * Gets query for [[Pvp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPvp()
    {
        return $this->hasOne(Pvp::className(), ['id' => 'pvp_id']);
    }
}
