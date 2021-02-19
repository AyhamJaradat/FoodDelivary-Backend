<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property int $status
 * @property int $user_id
 * @property int $restaurant_id
 * @property int|null $created_by
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $createdBy
 * @property OrderLog[] $orderLogs
 * @property OrderMeal[] $orderMeals
 * @property Restaurant $restaurant
 * @property User $updatedBy
 * @property User $user
 */
class Order extends \yii\db\ActiveRecord
{

    //    * Placed: Once a Regular user places an Order
    //    * Canceled: If the Regular User cancel the Order
    //    * Processing: Once the Restaurant Owner starts to make the meals
    //    * In Route: Once the meal is finished and Restaurant Owner marks it’s on the way
    //    * Delivered: Once the Restaurant Owner receives information that the meal was delivered by their staff
    //    * Received: Once the Regular User receives the meal and marks it as Received
    const ORDER_STATUS_PLACED = 1;
    const ORDER_STATUS_CANCELED = 2;
    const ORDER_STATUS_PROCESSING = 3;
    const ORDER_STATUS_IN_ROUTE = 4;
    const ORDER_STATUS_DELIVERED = 5;
    const ORDER_STATUS_RECEIVED = 6;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'user_id', 'restaurant_id', 'created_by', 'updated_by'], 'integer'],
            [['user_id', 'restaurant_id'], 'required'],
            [['status'], 'default', 'value' => self::ORDER_STATUS_PLACED],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['restaurant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Restaurant::className(), 'targetAttribute' => ['restaurant_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'status' => Yii::t('common', 'Status'),
            'user_id' => Yii::t('common', 'User ID'),
            'restaurant_id' => Yii::t('common', 'Restaurant ID'),
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
     * Gets query for [[OrderLogs]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderLogQuery
     */
    public function getOrderLogs()
    {
        return $this->hasMany(OrderLog::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderMeals]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderMealQuery
     */
    public function getOrderMeals()
    {
        return $this->hasMany(OrderMeal::className(), ['order_id' => 'id']);
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\OrderQuery(get_called_class());
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


    public static function getStatusName($status_id){


        switch ($status_id){
            case self::ORDER_STATUS_PLACED:
                return 'Placed';
            case self::ORDER_STATUS_CANCELED:
                return 'Canceled';
            case self::ORDER_STATUS_PROCESSING:
                return 'Processing';
            case self::ORDER_STATUS_IN_ROUTE:
                return 'In Route';
            case self::ORDER_STATUS_DELIVERED:
                return 'Delivered';
            case self::ORDER_STATUS_RECEIVED:
                return 'Received';
            default:
                return 'Not Set';
        }
    }
}
