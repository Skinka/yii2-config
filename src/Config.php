<?php

namespace skinka\yii2\extension\config;


use yii\base\Component;
use yii\base\InvalidConfigException;

class Config extends Component
{
    public static function __callStatic($name, $arguments)
    {
        /** @var ConfigModel $data */
        $data = ConfigModel::getData($name);
        if ($data) {
            switch ($data->type) {
                case ConfigModel::TYPE_STRING:
                    return (string)empty($data->value) ? $data->default : $data->value;
                    break;
                case ConfigModel::TYPE_INTEGER:
                    return (int)empty($data->value) ? $data->default : $data->value;
                    break;
                case ConfigModel::TYPE_FLOAT:
                    return (float)empty($data->value) ? $data->default : $data->value;
                    break;
                case ConfigModel::TYPE_BOOLEAN:
                    return (boolean)empty($data->value) ? $data->default : $data->value;
                    break;
            }
        }
        throw new InvalidConfigException();
    }
}