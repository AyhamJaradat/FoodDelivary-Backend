<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%restaurant}}".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $owner_id
 * @property int $is_deleted
 * @property int|null $created_by
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $createdBy
 * @property Meal[] $meals // all meals of this restaurant
 * @property Meal[] $activeMeals // Not deleted meals of this restaurant
 * @property Order[] $orders
 * @property User $owner
 * @property User $updatedBy
 */
class Restaurant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%restaurant}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'owner_id'], 'required'],
            [['owner_id', 'is_deleted', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
//            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['owner_id' => 'id']],
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
            'owner_id' => Yii::t('common', 'Owner ID'),
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
     * Gets query for [[Meals]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MealQuery
     */
    public function getMeals()
    {
        return $this->hasMany(Meal::className(), ['restaurant_id' => 'id']);
    }

    /**
     * Gets query for [[ActiveMeals]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MealQuery
     */
    public function getActiveMeals()
    {
        return $this->hasMany(Meal::className(), ['restaurant_id' => 'id'])->andOnCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['restaurant_id' => 'id']);
    }


    /**
     * get all orders for given user id from this restaurant
     * @param $user_id
     */
    public  function getMyOrders($user_id){

         return $this->hasMany(Order::className(), ['restaurant_id' => 'id'])->andOnCondition(['user_id' => $user_id]);
    }

    /**
     * Gets query for [[Owner]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
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
     * @return \common\models\query\RestaurantQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\RestaurantQuery(get_called_class());
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
