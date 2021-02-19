<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OrderMeal */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Order Meal',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Order Meals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-meal-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
