<?php

namespace skinka\yii2\extension\config;

use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

/**
 * This is the model class for table "{{%config}}".
 *
 * @property string $name
 * @property string $alias
 * @property integer $type
 * @property string $value
 * @property string $valid_rules
 * @property string $variants
 * @property integer $sort
 */
class ConfigModel extends \yii\db\ActiveRecord
{

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
            [['name', 'alias', 'type', 'value', 'sort'], 'required'],
            [['name'], 'unique'],
            [['name'], 'trim'],
            [['name'], 'match', 'pattern' => '/^[a-z]\w*$/i'],
            [['type', 'sort'], 'integer'],
            [
                ['type'],
                'in',
                'range' => [Config::TYPE_BOOLEAN, Config::TYPE_FLOAT, Config::TYPE_INTEGER, Config::TYPE_STRING]
            ],
            [['variants', 'valid_rules'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['alias'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => 'Alias',
            'type' => 'Type',
            'value' => 'Value',
            'variants' => 'Variants',
            'valid_rules' => 'Validators',
            'sort' => 'Sort',
        ];
    }

    public function afterFind()
    {
        if (!empty($this->valid_rules) && is_array(json_decode($this->valid_rules))) {
            foreach (json_decode($this->valid_rules) as $rule) {
                $validatorName = $rule[0];
                unset($rule[0]);
                $this->validators[] = Validator::createValidator($validatorName, $this, 'value', $rule);
            }
        } else {
            $this->validators[] = Validator::createValidator('string', $this, 'value', ['max' => 255]);
        }
    }
}
