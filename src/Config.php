<?php

namespace skinka\yii2\extension\config;


use yii\base\Component;
use yii\base\InvalidConfigException;

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
    private static $_instance;

    public static function __callStatic($name, $arguments)
    {
        /** @var ConfigModel $model */
        if (\Yii::$app->cache->exists(static::$cachePrefix . $name)) {
            $model = \Yii::$app->cache->get(static::$cachePrefix . $name);
        } else {
            $model = static::getInstance($name)->getAttributes();
            \Yii::$app->cache->set(static::$cachePrefix . $name, $model);
        }
        $data = empty($model['value']) ? $model['default'] : $model['value'];

        switch ($model['type']) {
            case self::TYPE_STRING:
                return (string)$data;
                break;
            case self::TYPE_INTEGER:
                return (int)$data;
                break;
            case self::TYPE_FLOAT:
                return (float)$data;
                break;
            case self::TYPE_BOOLEAN:
                return (boolean)$data;
                break;
        }
        throw new InvalidConfigException();
    }

    private static function getInstance($name)
    {
        if (!isset(static::$_instance[$name])) {
            $data = ConfigModel::find()->where(['name' => $name])->one();
            if ($data) {
                static::$_instance[$name] = $data;
            }
            throw new \BadMethodCallException();
        }
        return static::$_instance[$name];
    }

    public static function setNew(
        $name,
        $alias,
        $default,
        $type = self::TYPE_STRING,
        $value = '',
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
            //@TODO валидация $default и $value
        }
        $model->default = $default;
        $model->value = $value;

        if (is_array($variants)) {
            $model->variants = $variants;
        }
        $model->sort = $sort;
        if (!$model->save()) {
            return $model->getErrors();
        }
        return true;
    }

    public static function setValue($name, $newValue)
    {
        /** @var ConfigModel $model */
        $model = static::getInstance($name);
        //@TODO если $valid_rules заполнен то нужно перевалидировать
        $model->value = (string)$newValue;
        static::clearCache($name);
        return $model->save();
    }

    private static function clearCache($name)
    {
        \Yii::$app->cache->delete(static::$cachePrefix . $name);
    }

    public static function delete($name)
    {
        return \Yii::$app->cache->delete(static::$cachePrefix . $name) && ConfigModel::deleteAll(['name' => $name]) > 0;
    }
}