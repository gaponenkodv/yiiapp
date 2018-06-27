<?php

use yii\db\Migration;

/**
 * Class m180627_134535_create_users
 */
class m180627_134535_create_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id'=> $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'balance_id' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }

}
