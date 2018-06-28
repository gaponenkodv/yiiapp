<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m180627_134616_create_balances
 */
class m180627_134616_create_balances extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('balances', [
           'id' => $this->primaryKey(),
           'balance'=> $this->money()->defaultValue(0)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('balances');
    }

}
