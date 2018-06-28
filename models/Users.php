<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property int $balance_id
 *
 * @property Balances $balance
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['balance_id'], 'default', 'value' => null],
            [['balance_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['balance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Balances::className(), 'targetAttribute' => ['balance_id' => 'id']],
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
            'balance_id' => 'Balance ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalance()
    {
        return $this->hasOne(Balances::className(), ['id' => 'balance_id']);
    }

    public function getUsers()
    {

    }
}
