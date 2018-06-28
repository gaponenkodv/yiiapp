<?php

namespace app\job;

use app\events\MyEvent;
use app\models\Balances;
use app\models\ChangeBalance;
use app\models\Invoices;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;
use yii\queue\JobInterface;
use yii\queue\Queue;

class ListenInvoices extends BaseObject implements JobInterface
{

    public $invoice;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @throws \Exception
     */
    public function execute($queue)
    {
        /** Если пустое значение инвойса, то можно обработать пустое значение
         * это если случилось что-то и накопилась большая пачка платежей, а очереди сдохли
         * можно конечно снова их заполнять, но мне кажется так проще
         *
         * вообще проще не передавать значение инвойса, а идти исключительно по возрастанию и статусу
         */
        $invoice = isset($this->invoice)
            ? Invoices::findOne(['id' => $this->invoice])
            : Invoices::find()
                ->where(['status' => ChangeBalance::STATUS_CREATED])
                ->orderBy(['id' => SORT_ASC])
                ->one();

        if(Yii::$app->cache->add(md5(self::className() . __METHOD__ . $invoice->id), '', 60))
        {
            /** Если указан 0 в качестве принимающей стороны, сразу выставляется статус "оплачено",
             * так как там нет работы с другим балансом
             */
            $invoice->status = 0 == $invoice->balance_to
                ? ChangeBalance::STATUS_PAID
                : ChangeBalance::STATUS_PROCESSING;

            $invoice->ts_updated = date("Y-m-d H:i:s");
            $invoice->save();

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try{

                $balanceTo = Balances::findOne(['id' => $invoice->balance_to]);
                $balanceTo->balance = $balanceTo->balance + $invoice->amount;

                $invoice->status = ChangeBalance::STATUS_PAID;
                $invoice->ts_updated = date("Y-m-d H:i:s");

                $balanceTo->save();
                $invoice->save();

                $transaction->commit();

            }
            catch (\Exception $e)
            {
                $transaction->rollBack();
            }
        }

    }
}