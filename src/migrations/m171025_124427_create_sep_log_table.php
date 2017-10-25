<?php

use yii\db\Migration;

/**
 * Handles the creation of table `sep_log`.
 */
class m171025_124427_create_sep_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sep_log}}', [
            'id' => $this->primaryKey(),
            'ResNum' => $this->string(),
            'RefNum' => $this->string(),
            'CardNumber' => $this->string(20),
            'data' => $this->text(),
            'status' => $this->smallInteger(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%sep_log}}');
    }
}
