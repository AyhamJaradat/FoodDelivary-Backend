<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Meal */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Meal',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Meals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="meal-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
