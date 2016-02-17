<?php

namespace skinka\yii2\extension\config;

use Yii;

/**
 * This is the model class for table "{{%config}}".
 *
 * @property string $name
 * @property string $alias
 * @property integer $type
 * @property string $value
 * @property string $default
 * @property string $valid_rules
 * @property string $variants
 * @property integer $sort
 */
class ConfigModel extends \yii\db\ActiveRecord
{
    const TYPE_STRING = 0;
    const TYPE_INTEGER = 1;
    const TYPE_FLOAT = 2;
    const TYPE_BOOLEAN = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'alias', 'type', 'value', 'default', 'sort'], 'required'],
            [['name'], 'unique'],
            [['name'], 'trim'],
            [['name'], 'match', 'pattern' => '/^[a-z]\w*$/i'],
            [['type', 'sort'], 'integer'],
            [['type'], 'in', 'range' => [self::TYPE_BOOLEAN, self::TYPE_FLOAT, self::TYPE_INTEGER, self::TYPE_STRING]],
            [['variants', 'valid_rules'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['alias'], 'string', 'max' => 150],
            [['value', 'default'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'alias' => Yii::t('app', 'Alias'),
            'type' => Yii::t('app', 'Type'),
            'value' => Yii::t('app', 'Value'),
            'default' => Yii::t('app', 'Default Value'),
            'variants' => Yii::t('app', 'Variants'),
            'sort' => Yii::t('app', 'sort'),
        ];
    }
}