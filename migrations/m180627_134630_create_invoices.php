<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m180627_134630_create_invoices
 */
class m180627_134630_create_invoices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('invoices', [
           'id' => $this->primaryKey(),
            'balance_from' => $this->integer(),
            'balance_to' => $this->integer(),
            'status' => $this->integer(),
            'ts_create' => Schema::TYPE_DATETIME . ' DEFAULT NOW()',
            'ts_updated' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('invoices');
    }
}
