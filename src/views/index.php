<?php
/**
 * Created by Skinka.
 * Date: 19.02.2016 23:10
 * @var $models \skinka\yii2\extension\config\ConfigModel[]
 */
use skinka\yii2\extension\config\Config;

?>
<?php $form = \yii\widgets\ActiveForm::begin([
    'enableClientScript' => false,
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
]) ?>
<?php foreach ($models as $key => $model) : ?>
    <?= $model->getConfigInput($form) ?>
<?php endforeach; ?>
<?= \yii\helpers\Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
<?php \yii\widgets\ActiveForm::end() ?>