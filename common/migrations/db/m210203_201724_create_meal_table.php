<?php

use common\migrations\db\Migration;

/**
 * Handles the creation of table `{{%meal}}`.
 */
class m210203_201724_create_meal_table extends Migration
{


    // main table
    private $_tn_meal = '{{%meal}}';

    // reference table
    private $_tn_restaurant = '{{%restaurant}}';
    private $_tn_user = '{{%user}}';
    // foreign keys
    private $_fkn_meal_restaurant = 'fk-meal-restaurant_id-restaurant-id';
    private $_fkn_mealCreatedBy_user = 'fk-meal-created_by-user-id';
    private $_fkn_mealUpdatedBy_user = 'fk-meal-updated_by-user-id';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_tn_meal, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description'=>$this->string()->notNull(),
            'price'=>$this->double()->notNull()->defaultValue(0),
            'restaurant_id'=>$this->integer()->notNull(),
            'is_deleted'=>$this->boolean()->notNull()->defaultValue(false),
            'created_by' =>$this->integer(), // set null on deleting creator
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' =>$this->integer(),// set null on deleting updater

        ]);

        $this->addForeignKey($this->_fkn_meal_restaurant ,$this->_tn_meal,'restaurant_id', $this->_tn_restaurant,'id','cascade', 'cascade');
        $this->addForeignKey($this->_fkn_mealCreatedBy_user, $this->_tn_meal, 'created_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey($this->_fkn_mealUpdatedBy_user, $this->_tn_meal, 'updated_by', $this->_tn_user, 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey($this->_fkn_mealUpdatedBy_user, $this->_tn_meal);
        $this->dropForeignKey($this->_fkn_mealCreatedBy_user, $this->_tn_meal);
        $this->dropForeignKey($this->_fkn_meal_restaurant, $this->_tn_meal);
        $this->dropTable($this->_tn_meal);
    }
}
