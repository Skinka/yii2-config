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
    <?php
    $field = $form->field($model, 'value',
        ['inputOptions' => ['name' => 'ConfigModel[' . $key . '][value]',]])->label($model->alias);
    switch ($model->type) {
        case Config::TYPE_BOOLEAN:
            echo $field->dropDownList([0 => 'Off', 1 => 'On'], ['class' => 'form-control']);
            break;
        case Config::TYPE_FLOAT:
        case Config::TYPE_INTEGER:
            echo $field->textInput(['class' => 'form-control']);
            break;
        case Config::TYPE_STRING:
            echo $field->textInput(['class' => 'form-control']);
            break;
    }
    ?>
<?php endforeach; ?>
<?= \yii\helpers\Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
<?php \yii\widgets\ActiveForm::end() ?>