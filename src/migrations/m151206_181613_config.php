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
            'name' => $this->string(100)->notNull()->unique(),
            'alias' => $this->string(150)->notNull(),
            'type' => $this->smallInteger(1)->notNull(),
            'value' => $this->string()->notNull(),
            'default' => $this->string()->notNull(),
            'valid_rules' => $this->text()->defaultValue(null),
            'variants' => $this->text()->defaultValue(null),
            'sort' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->createIndex('idx_config_name', '{{%config}}', 'name', true);
    }

    public function down()
    {
        $this->dropIndex('idx_config_name', '{{%config}}');
        $this->dropTable('{{%config}}');
    }
}
