<?php

use common\migrations\db\Migration;

/**
 * Handles the creation of table `{{%order_meal}}`.
 */
class m210203_201802_create_order_meal_table extends Migration
{

    // main table
    private $_tn_orderMeal = '{{%order_meal}}';


    // reference table
    private $_tn_order = '{{%order}}';
    private $_tn_user = '{{%user}}';
    private $_tn_meal = '{{%meal}}';
    // foreign keys
    private $_fkn_orderMeal_order = 'fk-order_meal-order_id-order-id';
    private $_fkn_orderMeal_meal = 'fk-order_meal-meal_id-meal-id';
    private $_fkn_orderMealCreatedBy_user = 'fk-order_meal-created_by-user-id';
    private $_fkn_orderMealUpdatedBy_user = 'fk-order_meal-updated_by-user-id';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_tn_orderMeal, [
            'id' => $this->primaryKey(),
            'order_id'=>$this->integer()->notNull(),
            'meal_id'=>$this->integer()->notNull(),
            'amount'=>$this->integer()->notNull()->defaultValue(1),
            'price'=>$this->double()->notNull()->defaultValue(0),
            'created_by' =>$this->integer(), // set null on deleting creator
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' =>$this->integer(),// set null on deleting updater
        ]);

        $this->addForeignKey($this->_fkn_orderMeal_order ,$this->_tn_orderMeal,'order_id', $this->_tn_order,'id','cascade', 'cascade');
        $this->addForeignKey($this->_fkn_orderMeal_meal ,$this->_tn_orderMeal,'meal_id', $this->_tn_meal,'id','cascade', 'cascade');
        $this->addForeignKey($this->_fkn_orderMealCreatedBy_user, $this->_tn_orderMeal, 'created_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey($this->_fkn_orderMealUpdatedBy_user, $this->_tn_orderMeal, 'updated_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey($this->_fkn_orderMealUpdatedBy_user, $this->_tn_orderMeal);
        $this->dropForeignKey($this->_fkn_orderMealCreatedBy_user, $this->_tn_orderMeal);
        $this->dropForeignKey($this->_fkn_orderMeal_meal, $this->_tn_orderMeal);
        $this->dropForeignKey($this->_fkn_orderMeal_order, $this->_tn_orderMeal);
        $this->dropTable($this->_tn_orderMeal);
    }
}
