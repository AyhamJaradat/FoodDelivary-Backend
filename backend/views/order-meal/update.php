<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OrderMeal */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Order Meal',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Order Meals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="order-meal-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
