<?php
namespace skinka\yii2\extension\config\actions;

use skinka\yii2\extension\config\Config;
use skinka\yii2\extension\config\ConfigModel;
use Yii;
use yii\base\Action;
use yii\base\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ConfigAction extends Action
{
    public $viewPath = '@vendor/skinka/yii2-config/src/views/index';
    public $successMessage = 'Saved';
    public function run()
    {
        $models = ConfigModel::find()->orderBy(['sort' => SORT_ASC])->indexBy('name')->all();
        if (Model::loadMultiple($models, Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($models, 'value');
            }
            $result = true;
            /** @var ConfigModel $model */
            foreach ($models as $model) {
                $result = $result && $model->save(true, ['value']);
                Config::clearCache($model->name);
            }
            if ($result) {
                Yii::$app->session->setFlash('success', $this->successMessage);
                return $this->controller->redirect([$this->id]);
            }
        }
        return $this->controller->render($this->viewPath, ['models' => $models]);
    }
}