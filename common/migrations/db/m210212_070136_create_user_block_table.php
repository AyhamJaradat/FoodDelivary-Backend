<?php

use common\migrations\db\Migration;

/**
 * Handles the creation of table `{{%user_block}}`.
 */
class m210212_070136_create_user_block_table extends Migration
{
    // main table
    private $_tn_userBlock = '{{%user_block}}';

    // reference table
    private $_tn_user = '{{%user}}';
    // foreign keys
    private $_fkn_userBlockOwner_user = 'fk-user_block-owner_id-user-id';
    private $_fkn_userBlockUser_user = 'fk-user_block-user_id-user-id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_tn_userBlock, [
            'id' => $this->primaryKey(),
            'owner_id'=>$this->integer()->notNull(),
            'user_id'=>$this->integer()->notNull(),
        ]);

        $this->addForeignKey($this->_fkn_userBlockOwner_user ,$this->_tn_userBlock,'owner_id', $this->_tn_user,'id','cascade', 'cascade');
        $this->addForeignKey($this->_fkn_userBlockUser_user ,$this->_tn_userBlock,'user_id', $this->_tn_user,'id','cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey($this->_fkn_userBlockUser_user, $this->_tn_userBlock);
        $this->dropForeignKey($this->_fkn_userBlockOwner_user, $this->_tn_userBlock);
        $this->dropTable($this->_tn_userBlock);
    }
}
