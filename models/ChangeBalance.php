<?php

namespace app\models;

use app\events\MyEvent;
use app\job\ListenInvoices;
use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

class ChangeBalance extends ActiveRecord
{
    public $balanceFrom;
    public $balanceTo;
    public $amount;
    public $hash;

    protected $invoiceId;
    protected $queueId;

    const STATUS_CREATED = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_PAID = 2;
    const STATUS_CANCELED = 3;

    const F_BALANCE_FROM = 'balanceFrom';
    const F_BALANCE_TO = 'balanceTo';
    const F_BALANCE_AMOUNT = 'amount';
    const F_HASH = 'hash';

    public function rules()
    {
        return [
            [[self::F_BALANCE_FROM, self::F_BALANCE_TO, self::F_BALANCE_AMOUNT], 'number'],
            [[self::F_BALANCE_FROM, self::F_BALANCE_TO, self::F_BALANCE_AMOUNT, self::F_HASH], 'required'],
            [self::F_BALANCE_AMOUNT, 'number', 'min' => 0]
        ];
    }

    /**
     * Выполнение операции изменения баланса
     *
     * @throws \Exception
     */
    public function changeBalance()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try
        {
            $balance = $this->getBalance();

            $this->checkBalance($balance);

            $invoice = new Invoices();
            $invoice->balance_from = $this->balanceFrom;
            $invoice->balance_to = $this->balanceTo;
            $invoice->status = self::STATUS_CREATED;
            $invoice->amount = $this->amount;

            /**
             * Нулевое значение это что то обезличенное для
             * пополнения и списания платежей персонально со счета
             */
            if($this->balanceFrom != 0)
            {
                $balance->balance = $balance->balance - $this->amount;
                $balance->save();
            }

            $invoice->save();

            $this->invoiceId = $invoice->id;

            $transaction->commit();

            $this->setQueue($invoice->id);

            // Какое то событие
            $this->on('some_event', [new MyEvent, 'loadPaymentEventHendler']);

            return true;
        }
        catch (\Exception $e)
        {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Получение номера вставленного инвойса
     *
     * @return integer
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * Получение номера в очереди(для тестов)
     *
     * @return mixed
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

    /**
     * Получение списка пользователей
     *
     * @return array
     */
    public function getBalances()
    {
        return ArrayHelper::map(Users::find()->all(), 'id', 'name');
    }

    /**
     * Постановка в очереди
     *
     * @param integer $invoice Номер инвойса
     */
    protected function setQueue($invoice)
    {
        $this->queueId = Yii::$app->queue->push(new ListenInvoices([
            'invoice' => $invoice
        ]));
    }

    /**
     * Получение модели баланса
     *
     * @return Balances|null
     */
    protected function getBalance()
    {
        return Balances::findOne(['id' => $this->balanceFrom]);
    }

    /**
     * Проверка наличия средств
     *
     * @param Balances $balance Модель баланса
     *
     * @throws \Exception
     */
    protected function checkBalance($balance)
    {
        if($balance->balance < $this->amount)
            throw new \Exception('Баланса недостаточно');
    }
}