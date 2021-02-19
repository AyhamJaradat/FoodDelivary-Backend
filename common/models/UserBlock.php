<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_block}}".
 *
 * @property int $id
 * @property int $owner_id
 * @property int $user_id
 *
 * @property User $owner
 * @property User $user
 */
class UserBlock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_block}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['owner_id', 'user_id'], 'required'],
            [['owner_id', 'user_id'], 'integer'],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['owner_id' => 'id']],
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
            'owner_id' => Yii::t('common', 'Owner ID'),
            'user_id' => Yii::t('common', 'User ID'),
        ];
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
     * @return \common\models\query\UserBlockQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserBlockQuery(get_called_class());
    }
}
