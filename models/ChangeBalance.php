<?php
/**
 * Created by PhpStorm.
 * User: gaponenko
 * Date: 28.06.2018
 * Time: 13:24
 */

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

    protected $invoiceId;
    protected $queueId;

    const STATUS_CREATED = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_PAID = 2;
    const STATUS_CANCELED = 3;

    const F_BALANCE_FROM = 'balanceFrom';
    const F_BALANCE_TO = 'balanceTo';
    const F_BALANCE_AMOUNT = 'amount';

    public function rules()
    {
        return [
            [[self::F_BALANCE_FROM, self::F_BALANCE_TO, self::F_BALANCE_AMOUNT], 'number'],
            [[self::F_BALANCE_FROM, self::F_BALANCE_TO, self::F_BALANCE_AMOUNT], 'required'],
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

        try{
            $balance = $this->getBalance();

            $this->checkBalance($balance);

            $balance->balance = $balance->balance - $this->amount;

            $invoice = new Invoices();
            $invoice->balance_from = $this->balanceFrom;
            $invoice->balance_to = $this->balanceTo;
            $invoice->status = self::STATUS_CREATED;
            $invoice->amount = $this->amount;

            $balance->save();
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
            Yii::debug($e->getMessage());
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
     * Получение номера в очереди
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