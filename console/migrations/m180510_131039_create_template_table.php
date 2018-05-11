<?php

use yii\db\Migration;

/**
 * Handles the creation of table `template`.
 */
class m180510_131039_create_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('template', [
            'id' => $this->primaryKey(),
            'code' => $this->string()->notNull(),
            'modified_at' => $this->date()->notNull(),
            'data_type' => 'ENUM("text", "html")',
            'subject' => $this->string()->notNull(),
            'body' => $this->text()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('template');
    }
}
