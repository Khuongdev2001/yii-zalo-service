<?php

use yii\db\Migration;

/**
 * Class m221104_090457_create_table_access_token
 */
class m221104_090457_create_table_access_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("service_oauth", [
            "id" => $this->primaryKey(),
            "auth_client" => $this->string(500),
            "access_token" => $this->string(500),
            "refresh_token" => $this->string(500),
            "token_expires_in" => $this->string(500),
            "refresh_token_expires_in" => $this->string(500)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("service_oauth");
    }

}
