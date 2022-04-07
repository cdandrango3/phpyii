<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bank_details".
 *
 * @property int $id
 * @property string $name
 * @property int $number_account
 * @property int $chart_account_id
 * @property string $city_id
 * @property bool|null $status
 * @property int|null $bank_type_id
 * @property int $bank_id
 */
class BankDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'number_account', 'chart_account_id', 'city_id', 'bank_id'], 'required'],
            [['name', 'city_id'], 'string'],
            [['number_account', 'chart_account_id', 'bank_type_id', 'bank_id'], 'default', 'value' => null],
            [['number_account', 'chart_account_id', 'bank_type_id', 'bank_id'], 'integer'],
            [['status'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'number_account' => 'Number Account',
            'chart_account_id' => 'Chart Account ID',
            'city_id' => 'City ID',
            'status' => 'Status',
            'bank_type_id' => 'Bank Type ID',
            'bank_id' => 'Bank ID',
        ];
    }
}
