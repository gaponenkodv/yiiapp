<?php

namespace tests\models;

use app\models\Balances;
use app\models\ChangeBalance;
use app\models\Invoices;
use Yii;

class ChangeBalanceTest extends \Codeception\Test\Unit
{
    /**
     * @throws \Exception
     */
    public function testChangeBalance()
    {
        $balanceId1 = 1;
        $balanceId2 = 2;
        $amount = 10;

        $balance1 = Balances::findOne(['id' => $balanceId1]);
        $balance2 = Balances::findOne(['id' => $balanceId2]);

        expect_that(!empty($balance1));
        expect_that(!empty($balance2));

        expect_that($changeModel = new ChangeBalance());
        expect_that($changeModel->balanceFrom = $balanceId1);
        expect_that($changeModel->balanceTo = $balanceId2);
        expect_that($changeModel->amount = $amount);

        $changeModel->changeBalance();

        $balanceNew1 = Balances::findOne(['id' => $balanceId1]);
        $balanceNew2 = Balances::findOne(['id' => $balanceId2]);

        expect_that(!empty($balanceNew1));
        expect_that(!empty($balanceNew2));

        $invoice = $changeModel->getInvoiceId();
        $invoiceModel = Invoices::findOne(['id' => $invoice]);

        expect_that(!empty($invoice));

        expect_that($invoiceModel->amount == $amount);
        expect_that($invoiceModel->balance_from == $balanceId1);
        expect_that($invoiceModel->balance_to == $balanceId2);
        expect_that($balanceNew1->balance == $balance1->balance - $amount);
        /** Сумма захолдированнаб, поэтому пока она не попала на счет получателю */
        expect_that($balanceNew2->balance == $balance2->balance);
        expect_that($invoiceModel->status == ChangeBalance::STATUS_CREATED);

        $queueId = $changeModel->getQueueId();

        expect_that(Yii::$app->queue->isWaiting($queueId));
        expect_not(Yii::$app->queue->isReserved($queueId));
        expect_not(Yii::$app->queue->isDone($queueId));
    }




}
