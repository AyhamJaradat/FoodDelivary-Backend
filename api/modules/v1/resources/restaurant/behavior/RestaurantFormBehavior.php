<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/6/2021
 * Time: 1:17 AM
 */

namespace api\modules\v1\resources\restaurant\behavior;


use yii\base\Behavior;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;

class RestaurantFormBehavior  extends Behavior
{

    /**
     * @inheritdoc
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
//            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
//            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
//            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
//            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
//            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }

    /**
     * Before Validate
     * - set Owner_id
     * @param ModelEvent $event
     * @throws \Throwable
     */
    public function beforeValidate(ModelEvent $event): void
    {
        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;
        // update it only on create
        if ($this->owner->owner_id == null)
            $this->owner->owner_id =$user_id;
    }
}