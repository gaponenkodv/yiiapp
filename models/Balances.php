<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "balances".
 *
 * @property int $id
 * @property float $balance
 *
 * @property Invoices[] $invoices
 * @property Invoices[] $invoices0
 * @property Users[] $users
 */
class Balances extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'balances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['balance'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'balance' => 'Balance',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicesFrom()
    {
        return $this->hasMany(Invoices::className(), ['balance_from' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicesTo()
    {
        return $this->hasMany(Invoices::className(), ['balance_to' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['balance_id' => 'id']);
    }
}
