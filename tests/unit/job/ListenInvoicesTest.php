<?php

namespace tests\models;



use app\job\ListenInvoices;
use app\models\Balances;
use app\models\ChangeBalance;
use app\models\Invoices;
use yii\queue\redis\Queue;

class ListenInvoicesTest extends \Codeception\Test\Unit
{
    private $model;
    /**
     * @var \UnitTester
     */
    public $tester;

    /**
     * @dataProvider datasetForQueue
     *
     * @param Invoices $invoice Модель инвойса
     * @param string $expectedAction
     * @throws \Exception
     */
    public function testQueueInvoices($invoice, $expectedAction)
    {
        $invoice->save();

        $job = new ListenInvoices();
        $job->invoice = $invoice->id;

        switch ($expectedAction)
        {
            case 'up':
                $balanceUp = Balances::findOne(['id' => $invoice->balance_to]);

                $job->execute(new Queue());
                $newInvoice = Invoices::findOne(['id' => $invoice->id]);
                $balanceNewUp = Balances::findOne(['id' => $invoice->balance_to]);

                expect_that($newInvoice->status == ChangeBalance::STATUS_PAID);
                expect_that($balanceUp->balance + $invoice->amount == $balanceNewUp->balance);
                break;

            case 'none':
                $balanceFrom = Balances::findOne(['id' => $invoice->balance_to]);

                $job->execute(new Queue());
                $newInvoice = Invoices::findOne(['id' => $invoice->id]);
                $balanceNewFrom = Balances::findOne(['id' => $invoice->balance_from]);

                expect_that($newInvoice->status == ChangeBalance::STATUS_PAID);
                expect_that($balanceFrom->balance == $balanceNewFrom->balance);
                break;

            case 'change':
                $changeFrom = Balances::findOne(['id' => $invoice->balance_from]);
                $changeTo = Balances::findOne(['id' => $invoice->balance_to]);

                $job->execute(new Queue());
                $newInvoice = Invoices::findOne(['id' => $invoice->id]);
                $changeNewFrom = Balances::findOne(['id' => $invoice->balance_from]);
                $changeNewTo = Balances::findOne(['id' => $invoice->balance_to]);

                expect_that($newInvoice->status == ChangeBalance::STATUS_PAID);
                expect_that($changeFrom->balance == $changeNewFrom->balance);
                expect_that($changeNewTo->balance + $invoice->amount == $changeNewTo->balance);
                break;
            default:
                throw new \Exception('Что то пошло не так');
        }

    }

    /**
     * датапровайдер для проверки списания,
     * пополнения и пеервода между балансами
     */
    public function datasetForQueue()
    {
        $up = new Invoices();
        $up->balance_from = 0;
        $up->balance_to =1;
        $up->amount = 10;
        $up->status = ChangeBalance::STATUS_CREATED;
        $upAction = 'up';

        $down = new Invoices();
        $down->balance_from = 1;
        $down->balance_to =0;
        $down->amount = 10;
        $down->status = ChangeBalance::STATUS_CREATED;
        $downAction = 'none';

        $change = new Invoices();
        $change->balance_from = 1;
        $change->balance_to =2;
        $change->amount = 10;
        $change->status = ChangeBalance::STATUS_CREATED;
        $changeAction = 'change';

        return [
            [$up, $upAction],
            [$down, $downAction],
            [$change, $changeAction]
        ];
    }
}
