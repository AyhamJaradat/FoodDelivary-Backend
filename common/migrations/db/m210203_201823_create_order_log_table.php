<?php

use common\migrations\db\Migration;

/**
 * Handles the creation of table `{{%order_log}}`.
 */
class m210203_201823_create_order_log_table extends Migration
{

    // main table
    private $_tn_orderLog = '{{%order_log}}';
    // reference table
    private $_tn_order = '{{%order}}';
    private $_tn_user = '{{%user}}';
    // foreign keys
    private $_fkn_orderLog_order = 'fk-order_log-order_id-order-id';
    private $_fkn_orderLogCreatedBy_user = 'fk-order_log-created_by-user-id';
    private $_fkn_orderLogUpdatedBy_user = 'fk-order_log-updated_by-user-id';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_tn_orderLog, [
            'id' => $this->primaryKey(),
            'order_id'=>$this->integer()->notNull(),
            'status'=>$this->integer()->notNull(),
            'created_by' =>$this->integer(), // set null on deleting creator
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' =>$this->integer(),// set null on deleting updater
        ]);
        $this->addForeignKey($this->_fkn_orderLog_order ,$this->_tn_orderLog,'order_id', $this->_tn_order,'id','cascade', 'cascade');
        $this->addForeignKey($this->_fkn_orderLogCreatedBy_user, $this->_tn_orderLog, 'created_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey($this->_fkn_orderLogUpdatedBy_user, $this->_tn_orderLog, 'updated_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey($this->_fkn_orderLogUpdatedBy_user, $this->_tn_orderLog);
        $this->dropForeignKey($this->_fkn_orderLogCreatedBy_user, $this->_tn_orderLog);
        $this->dropForeignKey($this->_fkn_orderLog_order, $this->_tn_orderLog);
        $this->dropTable($this->_tn_orderLog);
    }
}
