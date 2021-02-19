<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\UserBlock]].
 *
 * @see \common\models\UserBlock
 */
class UserBlockQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\UserBlock[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\UserBlock|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
