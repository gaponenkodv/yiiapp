<?php

use yii\db\Migration;

/**
 * Class m180628_191036_add_invoice_amount
 */
class m180628_191036_add_invoice_amount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('invoices', 'amount', \yii\db\Schema::TYPE_MONEY);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('invoices', 'amount');
    }
}
