<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "person".
 *
 * @property int $id
 * @property int $person_type_id
 * @property bool|null $special_taxpayer
 * @property string|null $ruc
 * @property string|null $cedula
 * @property string $name
 * @property string|null $commercial_name
 * @property string|null $phones
 * @property string|null $address
 * @property bool|null $foreigner
 * @property int|null $categories_id
 * @property string|null $emails
 * @property int|null $associated_person
 * @property bool $status
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property int $institution_id
 * @property int|null $city_id
 * @property string|null $id_myhouse
 *
 * @property Person $associatedPerson
 * @property PersonCategories $categories
 * @property Charges[] $charges
 * @property City $city
 * @property Clients[] $clients
 * @property Employee[] $employees
 * @property HeadFact[] $headFacts
 * @property Institution $institution
 * @property Person[] $people
 * @property PersonBankInfo[] $personBankInfos
 * @property PersonTypes $personType
 * @property Providers[] $providers
 * @property Salesman[] $salesmen
 * @property Shareholder[] $shareholders
 */
class Person extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'person';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['person_type_id', 'categories_id', 'associated_person', 'institution_id', 'city_id'], 'default', 'value' => null],
            [['person_type_id', 'categories_id', 'associated_person', 'institution_id', 'city_id'], 'integer'],
            [['special_taxpayer', 'foreigner', 'status'], 'boolean'],
            [['name', 'institution_id'], 'required'],
            [['address', 'emails', 'id_myhouse'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['ruc'], 'string', 'max' => 13],
            [['cedula'], 'string', 'max' => 20],
            [['name', 'phones'], 'string', 'max' => 255],
            [['commercial_name'], 'string', 'max' => 254],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['institution_id'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institution_id' => 'id']],
            [['associated_person'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['associated_person' => 'id']],
            [['categories_id'], 'exist', 'skipOnError' => true, 'targetClass' => PersonCategories::className(), 'targetAttribute' => ['categories_id' => 'id']],
            [['person_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PersonTypes::className(), 'targetAttribute' => ['person_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'person_type_id' => 'Person Type ID',
            'special_taxpayer' => 'Special Taxpayer',
            'ruc' => 'Ruc',
            'cedula' => 'Cedula',
            'name' => 'Name',
            'commercial_name' => 'Commercial Name',
            'phones' => 'Phones',
            'address' => 'Address',
            'foreigner' => 'Foreigner',
            'categories_id' => 'Categories ID',
            'emails' => 'Emails',
            'associated_person' => 'Associated Person',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'institution_id' => 'Institution ID',
            'city_id' => 'City ID',
            'id_myhouse' => 'Id Myhouse',
        ];
    }

    /**
     * Gets query for [[AssociatedPerson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssociatedPerson()
    {
        return $this->hasOne(Person::className(), ['id' => 'associated_person']);
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasOne(PersonCategories::className(), ['id' => 'categories_id']);
    }

    /**
     * Gets query for [[Charges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCharges()
    {
        return $this->hasMany(Charges::className(), ['person_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Clients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Clients::className(), ['person_id' => 'id']);
    }

    /**
     * Gets query for [[Employees]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['person_id' => 'id']);
    }

    /**
     * Gets query for [[HeadFacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHeadFacts()
    {
        return $this->hasMany(HeadFact::className(), ['id_personas' => 'id']);
    }

    /**
     * Gets query for [[Institution]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institution_id']);
    }

    /**
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasMany(Person::className(), ['associated_person' => 'id']);
    }

    /**
     * Gets query for [[PersonBankInfos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonBankInfos()
    {
        return $this->hasMany(PersonBankInfo::className(), ['person_id' => 'id']);
    }

    /**
     * Gets query for [[PersonType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonType()
    {
        return $this->hasOne(PersonTypes::className(), ['id' => 'person_type_id']);
    }

    /**
     * Gets query for [[Providers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProviders()
    {
        return $this->hasMany(Providers::className(), ['person_id' => 'id']);
    }

    /**
     * Gets query for [[Salesmen]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesmen()
    {
        return $this->hasMany(Salesman::className(), ['person_id' => 'id']);
    }

    /**
     * Gets query for [[Shareholders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShareholders()
    {
        return $this->hasMany(Shareholder::className(), ['person_id' => 'id']);
    }
}
