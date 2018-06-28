<?php

use yii\db\Migration;

/**
 * Class m180627_212024_create_queue
 */
class m180627_212024_create_queue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('queue',[
            'id' => $this->primaryKey(),
            'channel' => $this->string(255)->notNull(),
            'job' => 'bytea NOT NULL',
            'pushed_at' => $this->integer(11)->notNull(),
            'ttr' => $this->integer(11)->notNull(),
            'delay' => $this->integer(11)->notNull()->defaultValue(0),
            'priority' => $this->integer(11)->unsigned()->notNull()->defaultValue(1024),
            'reserved_at' => $this->integer(11)->null()->defaultValue(null),
            'attempt' => $this->integer(11)->null()->defaultValue(null),
            'done_at' => $this->integer(11)->null()->defaultValue(null),

        ]);

        $this->createIndex('channel', 'queue', 'channel');
        $this->createIndex('reserved_at', 'queue', 'reserved_at');
        $this->createIndex('priority', 'queue', 'priority');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('channel', 'queue');
        $this->dropIndex('reserved_at', 'queue');
        $this->dropIndex('priority', 'queue');

        $this->dropTable('queue');
    }


}
