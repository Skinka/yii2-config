<?php
namespace skinka\yii2\extension\config\actions;

use skinka\yii2\extension\config\ConfigModel;
use yii\base\Action;

class ConfigAction extends Action
{
    public $viewPath = '@vendor/skinka/yii2-config/src/views/index';

    public function run()
    {
        $models = ConfigModel::find()->indexBy('name')->all();
        if (\Yii::$app->request->isPost) {

        }
        return $this->controller->render($this->viewPath, ['models' => $models]);
    }
}