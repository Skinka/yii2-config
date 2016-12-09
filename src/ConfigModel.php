<?php

namespace skinka\yii2\extension\config;

use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

/**
 * This is the model class for table "{{%config}}".
 *
 * @property string $name
 * @property string $alias
 * @property integer $type
 * @property string $input
 * @property string $value
 * @property string $rules
 * @property string $options
 * @property string $hint
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
            [['name', 'alias', 'type', 'sort', 'input'], 'required'],
            [['name'], 'unique'],
            [['name'], 'trim'],
            [['name'], 'match', 'pattern' => '/^[a-z]\w*$/i'],
            [['type', 'sort'], 'integer'],
            [
                ['type'],
                'in',
                'range' => [Config::TYPE_BOOLEAN, Config::TYPE_FLOAT, Config::TYPE_INTEGER, Config::TYPE_STRING]
            ],
            [
                ['input'],
                'in',
                'range' => [
                    Config::INPUT_INPUT,
                    Config::INPUT_CHECKBOX,
                    Config::INPUT_CHECKBOX_LIST,
                    Config::INPUT_DROPDOWN,
                    Config::INPUT_RADIO_LIST,
                    Config::INPUT_TEXT,
                    Config::INPUT_WIDGET
                ]
            ],
            [['options', 'value', 'rules', 'hint', 'input'], 'string'],
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
            'input' => 'Input',
            'options' => 'Options',
            'rules' => 'Validators',
            'hint' => 'Hint',
            'sort' => 'Sort',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        if (!empty($this->rules) && is_array(json_decode($this->rules, true))) {
            foreach (json_decode($this->rules) as $rule) {
                $validatorName = $rule[0];
                unset($rule[0]);
                $this->validators[] = Validator::createValidator($validatorName, $this, 'value', $rule);
            }
        } else {
            $this->validators[] = Validator::createValidator('string', $this, 'value', []);
        }
    }

    public function getConfigInput($form, $options = [], $inputOptions = [])
    {
        /** @var ActiveField $input */
        /** @var ActiveForm $form */
        $field = 'value';
        $input = $form->field($this, "[{$this->name}]{$field}", $options);
        $input = call_user_func_array([$input, $this->input],
            ['options' => ArrayHelper::merge(json_decode($this->options, true), $inputOptions)]);
        $input = $input->label($this->alias);
        if (!empty($this->hint)) {
            $input = $input->hint($this->hint);
        }
        return $input;
    }
}
