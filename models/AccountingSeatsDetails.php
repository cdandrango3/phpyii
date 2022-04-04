<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accounting_seats_details".
 *
 * @property int $id
 * @property int $accounting_seat_id
 * @property int $chart_account_id
 * @property float $debit
 * @property float $credit
 * @property int|null $cost_center_id
 * @property bool $status
 *
 * @property AccountingSeats $accountingSeat
 * @property CostCenter $costCenter
 */
class AccountingSeatsDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accounting_seats_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accounting_seat_id', 'chart_account_id', 'debit', 'credit'], 'required'],
            [['accounting_seat_id', 'chart_account_id', 'cost_center_id'], 'default', 'value' => null],
            [['accounting_seat_id', 'chart_account_id', 'cost_center_id'], 'integer'],
            [['debit', 'credit'], 'number'],
            [['status'], 'boolean'],
            [['accounting_seat_id'], 'exist', 'skipOnError' => true, 'targetClass' => AccountingSeats::className(), 'targetAttribute' => ['accounting_seat_id' => 'id']],
            [['cost_center_id'], 'exist', 'skipOnError' => true, 'targetClass' => CostCenter::className(), 'targetAttribute' => ['cost_center_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'accounting_seat_id' => 'Accounting Seat ID',
            'chart_account_id' => 'Chart Account ID',
            'debit' => 'Debit',
            'credit' => 'Credit',
            'cost_center_id' => 'Cost Center ID',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[AccountingSeat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountingSeat()
    {
        return $this->hasOne(AccountingSeats::className(), ['id' => 'accounting_seat_id']);
    }

    /**
     * Gets query for [[CostCenter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCostCenter()
    {
        return $this->hasOne(CostCenter::className(), ['id' => 'cost_center_id']);
    }
}
