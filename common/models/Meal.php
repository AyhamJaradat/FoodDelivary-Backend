<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%meal}}".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int $restaurant_id
 * @property int $is_deleted
 * @property int|null $created_by
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $createdBy
 * @property OrderMeal[] $orderMeals
 * @property Restaurant $restaurant
 * @property User $updatedBy
 */
class Meal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%meal}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'restaurant_id'], 'required'],
            [['price'], 'number'],
            [['restaurant_id', 'is_deleted', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
            [['is_deleted'], 'default', 'value' => 0],
//            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['restaurant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Restaurant::className(), 'targetAttribute' => ['restaurant_id' => 'id']],
//            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Name'),
            'description' => Yii::t('common', 'Description'),
            'price' => Yii::t('common', 'Price'),
            'restaurant_id' => Yii::t('common', 'Restaurant ID'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
            'created_by' => Yii::t('common', 'Created By'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'updated_by' => Yii::t('common', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[OrderMeals]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderMealQuery
     */
    public function getOrderMeals()
    {
        return $this->hasMany(OrderMeal::className(), ['meal_id' => 'id']);
    }

    /**
     * Gets query for [[Restaurant]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\RestaurantQuery
     */
    public function getRestaurant()
    {
        return $this->hasOne(Restaurant::className(), ['id' => 'restaurant_id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\MealQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\MealQuery(get_called_class());
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::className(),
            ],
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }
}
