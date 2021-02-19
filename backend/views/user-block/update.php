<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserBlock */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'User Block',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Blocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-block-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
