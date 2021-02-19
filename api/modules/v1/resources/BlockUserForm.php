<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/12/2021
 * Time: 10:36 AM
 */

namespace api\modules\v1\resources;


use common\models\UserBlock;
use yii\base\Model;

class BlockUserForm extends Model
{
    public $customer_id;
    public $is_block;


    private $_userBlock=false;
    private $user;


    public function rules()
    {
        return [
            [['customer_id', 'is_block'], 'required'],
            [['customer_id', 'is_block'], 'integer'],

            [['customer_id'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['customer_id' => 'id']],

            [['is_block'], 'default', 'value' => 1],
            [['is_block'], 'in',
                'range' => [1, 0],
                'message' => 'is_block must be integer: either 1 for YES or 0 for NO ']
        ];


    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws \Throwable
     */
    public function updateIsBlocked($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames)) {
            return false;
        }
        // Do the delete process

        $currentUser = \Yii::$app->user->getIdentity();


        // is this user is an Owner
        if($currentUser->userRole != 2){
            $this->addError('customer_id','This API is allowed only for Owners');
            return false;
        }

        if($this->is_block == 1){
            // block user
            $blockUser = UserBlock::find()->where([
                'owner_id'=>$currentUser->id,
                'user_id'=>$this->customer_id
            ])->one();
            if(!$blockUser){
                $blockUser = new UserBlock();
                $blockUser->setAttributes([
                    'owner_id'=>$currentUser->id,
                    'user_id'=>$this->customer_id
                ]);
                $blockUser->save();
            }else{
                // already blocked
            }


        }else if($this->is_block == 0){
            // unblock user
            $blockUser = UserBlock::find()->where([
                'owner_id'=>$currentUser->id,
                'user_id'=>$this->customer_id
            ])->one();
            if(!$blockUser){
              // already un blocked
            }else{
                // un block him
                $blockUser->delete();
            }


        }
        $this->user = \api\modules\v1\resources\auth\User::find()->where(['id'=>$this->customer_id])->one();

        return $this->user;

    }

}