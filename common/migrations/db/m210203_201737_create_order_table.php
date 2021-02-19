<?php

use common\migrations\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m210203_201737_create_order_table extends Migration
{

    // main table
    private $_tn_order = '{{%order}}';

    // reference table
    private $_tn_restaurant = '{{%restaurant}}';
    private $_tn_user = '{{%user}}';
    // foreign keys
    private $_fkn_order_restaurant = 'fk-order-restaurant_id-restaurant-id';
    private $_fkn_order_user = 'fk-order-user_id-user-id';
    private $_fkn_orderCreatedBy_user = 'fk-order-created_by-user-id';
    private $_fkn_orderUpdatedBy_user = 'fk-order-updated_by-user-id';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_tn_order, [
            'id' => $this->primaryKey(),
            'status'=>$this->integer()->notNull()->defaultValue(0),// current status of the order
            'user_id'=>$this->integer()->notNull(),
            'restaurant_id'=>$this->integer()->notNull(),
            'created_by' =>$this->integer(), // set null on deleting creator
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' =>$this->integer(),// set null on deleting updater
        ]);

        $this->addForeignKey($this->_fkn_order_user ,$this->_tn_order,'user_id', $this->_tn_user,'id','cascade', 'cascade');
        $this->addForeignKey($this->_fkn_order_restaurant ,$this->_tn_order,'restaurant_id', $this->_tn_restaurant,'id','cascade', 'cascade');
        $this->addForeignKey($this->_fkn_orderCreatedBy_user, $this->_tn_order, 'created_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey($this->_fkn_orderUpdatedBy_user, $this->_tn_order, 'updated_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey($this->_fkn_orderUpdatedBy_user, $this->_tn_order);
        $this->dropForeignKey($this->_fkn_orderCreatedBy_user, $this->_tn_order);
        $this->dropForeignKey($this->_fkn_order_restaurant, $this->_tn_order);
        $this->dropForeignKey($this->_fkn_order_user, $this->_tn_order);
        $this->dropTable($this->_tn_order);
    }
}
