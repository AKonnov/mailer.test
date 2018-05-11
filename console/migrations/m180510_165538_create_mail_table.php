<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mail`.
 */
class m180510_165538_create_mail_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('mail', [
            'id' => $this->primaryKey(),
            'from' => $this->string()->notNull(),
            'to' => $this->string()->notNull(),
            'template' => $this->string()->notNull(),
            'data' => $this->text(),
            'status' => $this->char(1),
            'date_added' => $this->date(),
            'date_modified' => $this->date(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('mail');
    }
}
