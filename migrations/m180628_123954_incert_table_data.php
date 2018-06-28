<?php

use yii\db\Migration;

/**
 * Class m180628_123954_incert_table_data
 */
class m180628_123954_incert_table_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('balances', [
            'id' => 1,
            'balance' => 1000
        ]);

        $this->insert('balances', [
            'id' => 2,
            'balance' => 2000
        ]);

        $this->insert('balances', [
            'id' => 3,
            'balance' => 3000
        ]);

        $this->insert('balances', [
            'id' => 4,
            'balance' => 4000
        ]);

        $this->insert('users',
            [
                'id' => 1,
                'name' => 'Пользователь 1',
                'balance_id' => 1
            ]
        );
        $this->insert('users',
            [
                'id' => 2,
                'name' => 'Пользователь 2',
                'balance_id' => 2
            ]
        );
        $this->insert('users',
            [
                'id' => 3,
                'name' => 'Пользователь 3',
                'balance_id' => 3
            ]
        );
        $this->insert('users',
            [
                'id' => 4,
                'name' => 'Пользователь 4',
                'balance_id' => 4
            ]
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('users', ['id' => 1]);
        $this->delete('users', ['id' => 2]);
        $this->delete('users', ['id' => 3]);
        $this->delete('users', ['id' => 4]);
        $this->delete('balances', ['id' => 1]);
        $this->delete('balances', ['id' => 2]);
        $this->delete('balances', ['id' => 3]);
        $this->delete('balances', ['id' => 4]);
    }


}
