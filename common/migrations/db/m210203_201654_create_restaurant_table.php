<?php

use common\migrations\db\Migration;

/**
 * Handles the creation of table `{{%restaurant}}`.
 */
class m210203_201654_create_restaurant_table extends Migration
{
    // main table
    private $_tn_restaurant = '{{%restaurant}}';

    // reference table
    private $_tn_user = '{{%user}}';
    // foreign keys
    private $_fkn_restaurant_user = 'fk-restaurant-owner_id-user-id';
    private $_fkn_restaurantCreatedBy_user = 'fk-restaurant-created_by-user-id';
    private $_fkn_restaurantUpdatedBy_user = 'fk-restaurant-updated_by-user-id';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_tn_restaurant, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description'=>$this->string()->notNull(),
            'owner_id'=>$this->integer()->notNull(),
            'is_deleted'=>$this->boolean()->notNull()->defaultValue(false),
            'created_by' =>$this->integer(), // set null on deleting creator
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' =>$this->integer(),// set null on deleting updater
        ]);

        $this->addForeignKey($this->_fkn_restaurant_user ,$this->_tn_restaurant,'owner_id', $this->_tn_user,'id','cascade', 'cascade');
        $this->addForeignKey($this->_fkn_restaurantCreatedBy_user, $this->_tn_restaurant, 'created_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey($this->_fkn_restaurantUpdatedBy_user, $this->_tn_restaurant, 'updated_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey($this->_fkn_restaurantUpdatedBy_user, $this->_tn_restaurant);
        $this->dropForeignKey($this->_fkn_restaurantCreatedBy_user, $this->_tn_restaurant);
        $this->dropForeignKey($this->_fkn_restaurant_user, $this->_tn_restaurant);
        $this->dropTable($this->_tn_restaurant);
    }
}
