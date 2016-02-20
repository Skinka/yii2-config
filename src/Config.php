<?php

namespace skinka\yii2\extension\config;


use yii\base\Component;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class Config
 * @package skinka\yii2\extension\config
 */
class Config extends Component
{
    const TYPE_STRING = 0;
    const TYPE_INTEGER = 1;
    const TYPE_FLOAT = 2;
    const TYPE_BOOLEAN = 3;

    public static $cachePrefix = 'config_';
    protected static $_instance;

    public static function __callStatic($name, $arguments)
    {
        return static::getValue($name);
    }

    public static function getValue($name)
    {
        /** @var ConfigModel $model */
        if (\Yii::$app->cache->exists(static::$cachePrefix . $name)) {
            $model = \Yii::$app->cache->get(static::$cachePrefix . $name);
        } else {
            $model = static::getInstance($name)->getAttributes();
            \Yii::$app->cache->set(static::$cachePrefix . $name, $model);
        }

        switch ($model['type']) {
            case self::TYPE_STRING:
                return (string)$model['value'];
                break;
            case self::TYPE_INTEGER:
                return (int)$model['value'];
                break;
            case self::TYPE_FLOAT:
                return (float)$model['value'];
                break;
            case self::TYPE_BOOLEAN:
                return (boolean)$model['value'];
                break;
        }
        throw new InvalidConfigException();
    }

    private static function getInstance($name)
    {
        if (!isset(static::$_instance[$name])) {
            $data = ConfigModel::find()->where(['name' => $name])->one();
            if (!$data) {
                throw new \BadMethodCallException();
            }
            static::$_instance[$name] = $data;
        }
        return static::$_instance[$name];
    }

    public static function setNew(
        $name,
        $alias,
        $value,
        $type = self::TYPE_STRING,
        $valid_rules = '',
        $variants = '',
        $sort = 0
    ) {
        $model = new ConfigModel();
        $model->name = $name;
        $model->alias = $alias;
        $model->type = $type;
        if (is_array($valid_rules)) {
            $model->valid_rules = json_encode($valid_rules);
            foreach ($valid_rules as $rule) {
                $validatorName = $rule[0];
                unset($rule[0]);
                $model->validators[] = Validator::createValidator($validatorName, $model, 'value', $rule);
            }
        }
        $model->value = (string)$value;

        if (is_array($variants)) {
            $model->variants = $variants;
        }
        $model->sort = $sort;
        return $model->save() ?: $model->getErrors();
    }

    public static function setValue($name, $newValue)
    {
        /** @var ConfigModel $model */
        $model = static::getInstance($name);
        $model->value = (string)$newValue;
        static::clearCache($name);
        return $model->save() ?: $model->getErrors();
    }

    public static function clearCache($name)
    {
        \Yii::$app->cache->delete(static::$cachePrefix . $name);
    }

    public static function delete($name)
    {
        return \Yii::$app->cache->delete(static::$cachePrefix . $name) && ConfigModel::deleteAll(['name' => $name]) > 0;
    }

    private static function validateValue($validator, $value)
    {
        $model = new DynamicModel(['field' => $value]);
        $model->addRule('field', $validator);
        return $model->validate() ? true : $model->getFirstError('field');
    }
}