<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoices".
 *
 * @property int $id
 * @property int $balance_from
 * @property int $balance_to
 * @property int $status
 * @property number $amount
 * @property string $ts_create
 * @property string $ts_updated
 *
 * @property Balances $balanceFrom
 * @property Balances $balanceTo
 */
class Invoices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['balance_from', 'balance_to', 'status'], 'default', 'value' => null],
            [['balance_from', 'balance_to', 'status'], 'integer'],
            [['ts_create', 'ts_updated'], 'safe'],
            [['balance_from'], 'exist', 'skipOnError' => true, 'targetClass' => Balances::className(), 'targetAttribute' => ['balance_from' => 'id']],
            [['balance_to'], 'exist', 'skipOnError' => true, 'targetClass' => Balances::className(), 'targetAttribute' => ['balance_to' => 'id']],
            [['amount'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'balance_from' => 'Balance From',
            'balance_to' => 'Balance To',
            'status' => 'Status',
            'amount' => 'Amount',
            'ts_create' => 'Ts Create',
            'ts_updated' => 'Ts Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceFrom()
    {
        return $this->hasOne(Balances::className(), ['id' => 'balance_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceTo()
    {
        return $this->hasOne(Balances::className(), ['id' => 'balance_to']);
    }
}
