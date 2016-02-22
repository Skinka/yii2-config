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
    /**
     * Type input on view of 'textInput' .
     * @see \yii\widgets\ActiveField::textInput()
     */
    const INPUT_INPUT = 'textInput';

    /**
     * Type input on view of 'textArea' .
     * @see \yii\widgets\ActiveField::textArea()
     */
    const INPUT_TEXT = 'textArea';

    /**
     * Type input on view of 'checkbox' .
     * @see \yii\widgets\ActiveField::checkbox()
     */
    const INPUT_CHECKBOX = 'checkbox';

    /**
     * Type input on view of 'checkboxList' .
     * @see \yii\widgets\ActiveField::checkboxList()
     */
    const INPUT_CHECKBOX_LIST = 'checkboxList';

    /**
     * Type input on view of 'radioList' .
     * @see \yii\widgets\ActiveField::radioList()
     */
    const INPUT_RADIO_LIST = 'radioList';

    /**
     * Type input on view of 'dropDownList' .
     * @see \yii\widgets\ActiveField::dropDownList()
     */
    const INPUT_DROPDOWN = 'dropDownList';

    /**
     * Type input on view of 'widget' .
     * @see \yii\widgets\ActiveField::widget()
     */
    const INPUT_WIDGET = 'widget';

    /**
     * Type setting of (string)
     */
    const TYPE_STRING = 0;

    /**
     * Type setting of (integer)
     */
    const TYPE_INTEGER = 1;

    /**
     * Type setting of (float)
     */
    const TYPE_FLOAT = 2;

    /**
     * Type setting of (boolean)
     */
    const TYPE_BOOLEAN = 3;

    /**
     * Prefix for cache
     * @var string
     */
    public static $cachePrefix = 'config_';

    /**
     * Instance of ConfigModel
     * @var ConfigModel[]
     */
    protected static $_instance;

    /**
     * Call setting method by name -> __CLASS__::settingName()
     * @param $name
     * @param $arguments
     * @return bool|float|int|string
     * @throws InvalidConfigException
     */
    public static function __callStatic($name, $arguments)
    {
        return static::getValue($name);
    }

    /**
     * Get setting value by name
     * @param string $name
     * @return bool|float|int|string
     * @throws InvalidConfigException
     */
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

    /**
     * Get instance ConfigModel by name setting
     * @param string $name
     * @return ConfigModel
     */
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

    /**
     * Add new setting in data base
     * Config::setNew('loginDuration', 'Login Duration', 36000, Config::TYPE_INTEGER, Config::INPUT_INPUT,  [['integer']], [], 'Time in seconds', 0)
     * @param string $name
     * @param string $alias
     * @param string $value
     * Config::TYPE_*
     * @param int $type
     * Config::INPUT_*
     * @param string $input
     * [
     *     ['string', 'max' => 255],
     *     ['trim'],
     * ]
     * @param array $rules
     * [
     *    'class' => 'example',
     * ]
     * <input class="example">
     * @param array $options
     * @param string $hint
     * @param int $sort
     * @return boolean|array
     */
    public static function setNew(
        $name,
        $alias,
        $value,
        $type = self::TYPE_STRING,
        $input = self::INPUT_INPUT,
        $rules = [],
        $options = [],
        $hint = '',
        $sort = 0
    ) {
        $model = new ConfigModel();
        $model->name = $name;
        $model->alias = $alias;
        $model->type = $type;
        $model->input = $input;
        $model->hint = $hint;
        $model->sort = $sort;
        if (is_array($rules) && !empty($rules)) {
            $model->rules = json_encode($rules);
            foreach ($rules as $rule) {
                $validatorName = $rule[0];
                unset($rule[0]);
                $model->validators[] = Validator::createValidator($validatorName, $model, 'value', $rule);
            }
        }
        $model->value = (string)$value;

        if (is_array($options) && !empty($options)) {
            $model->options = json_encode($options);
        }
        return $model->save() ?: $model->getErrors();
    }

    /**
     * Reset new value by name setting
     * @param string $name
     * @param string $newValue
     * @return boolean|array
     */
    public static function setValue($name, $newValue)
    {
        /** @var ConfigModel $model */
        $model = static::getInstance($name);
        $model->value = (string)$newValue;
        static::clearCache($name);
        return $model->save() ?: $model->getErrors();
    }

    /**
     * Reset position setting by name
     * @param string $name
     * @param integer $newPosition
     * @return array
     */
    public static function setSort($name, $newPosition)
    {
        /** @var ConfigModel $model */
        $model = static::getInstance($name);
        $model->sort = $newPosition;
        static::clearCache($name);
        return $model->save() ?: $model->getErrors();
    }

    /**
     * Clear cache setting by name
     * @param string $name
     */
    public static function clearCache($name)
    {
        \Yii::$app->cache->delete(static::$cachePrefix . $name);
    }

    /**
     * Delete setting by name
     * @param string $name
     * @return bool
     */
    public static function delete($name)
    {
        return \Yii::$app->cache->delete(static::$cachePrefix . $name) && ConfigModel::deleteAll(['name' => $name]) > 0;
    }
}