<?php
/**
 * Created by Skinka.
 * Date: 19.02.2016 23:10
 * @var $models \skinka\yii2\extension\config\ConfigModel[]
 */
?>
<?php $form = \yii\widgets\ActiveForm::begin() ?>
<?php foreach ($models as $key => $model) : ?>
    <?= $form->field($model, 'value', ['name' => 'ConfigModel[' . $key . '][value]'])->label($model->alias) ?>
<?php endforeach; ?>
<?php \yii\widgets\ActiveForm::end() ?>