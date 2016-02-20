<?php
namespace skinka\yii2\extension\config\actions;

use skinka\yii2\extension\config\Config;
use skinka\yii2\extension\config\ConfigModel;
use yii\base\Action;

class ConfigAction extends Action
{
    public $viewPath = '@vendor/skinka/yii2-config/src/views/index';
    public $successMessage = 'Saved';
    public function run()
    {
        $models = ConfigModel::find()->indexBy('name')->all();
        if (\Yii::$app->request->isPost) {
            $data = \Yii::$app->request->post('ConfigModel');
            foreach ($models as $model) {
                if ($model->value != $data[$model->name]['value']) {
                    Config::setValue($model->name, $data[$model->name]['value']);
                }
            }
            \Yii::$app->session->setFlash('success', $this->successMessage);
            return $this->controller->redirect([$this->id]);
        }
        return $this->controller->render($this->viewPath, ['models' => $models]);
    }
}