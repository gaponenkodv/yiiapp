<?php

use yii\db\Migration;

/**
 * Class m180627_141134_create_indexes_and_relations
 */
class m180627_141134_create_indexes_and_relations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'iusers_balance_id',
            'users',
            'balance_id'
        );

        $this->createIndex(
            'invoices_balance_from_id',
            'invoices',
            'balance_from'
        );

        $this->createIndex(
            'invoices_balance_to_id',
            'invoices',
            'balance_to'
        );

        $this->addForeignKey(
            'fk_balances_balance_id',
            'users',
            'balance_id',
            'balances',
            'id'
        );

        $this->addForeignKey(
            'fk_invoices_balance_from_id',
            'invoices',
            'balance_from',
            'balances',
            'id'
        );

        $this->addForeignKey(
            'fk_invoices_balance_to_id',
            'invoices',
            'balance_to',
            'balances',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_balances_balance_id', 'users');
        $this->dropForeignKey('fk_invoices_balance_from_id', 'invoices');
        $this->dropForeignKey('fk_invoices_balance_to_id', 'invoices');

        $this->dropIndex('iusers_balance_id', 'users');
        $this->dropIndex('invoices_balance_from_id', 'invoices');
        $this->dropIndex('invoices_balance_to_id', 'invoices');

    }
}
