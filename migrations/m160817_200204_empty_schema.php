<?php

use yii\db\Schema;
use yii\db\Migration;

class m160817_200204_empty_schema extends Migration
{
    public function up()
    {
        $this->createTable('photos');
    }

    public function down()
    {
        echo "m160817_200204_empty_schema cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
