<?php

use yii\db\Migration;

class m151206_181613_config extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%config}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'alias' => $this->string(150)->notNull(),
            'type' => $this->smallInteger(1)->notNull(),
            'value' => $this->string()->notNull(),
            'default' => $this->string()->notNull(),
            'variants' => $this->text()->defaultValue(null)
        ], $tableOptions);
        $this->createIndex('idx_config_name', '{{%config}}', 'name');
    }

    public function down()
    {
        $this->dropIndex('idx_config_name', '{{%config}}');
        $this->dropTable('{{%config}}');
    }
}
