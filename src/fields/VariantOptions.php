<?php

namespace webdna\commerce\variantoptions\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\helpers\Html;
use craft\helpers\StringHelper;
use craft\helpers\Json;
use yii\db\ExpressionInterface;
use yii\db\Schema;

/**
 * Variant Options field type
 */
class VariantOptions extends Field
{
    public mixed $source = null;
    
    public static function displayName(): string
    {
        return Craft::t('variant-options', 'Variant Options');
    }

    public static function icon(): string
    {
        return 'table';
    }

    public static function phpType(): string
    {
        return 'mixed';
    }

    public static function dbType(): array|string|null
    {
        // Replace with the appropriate data type this field will store in the database,
        // or `null` if the field is managing its own data storage.
        return Schema::TYPE_STRING;
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            // ...
        ]);
    }

    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            // ...
        ]);
    }

    public function getSettingsHtml(): ?string
    {
        //return null;
        
        return Craft::$app->getView()->renderTemplate(
            'variant-options/fields/VariantOptions_settings',
            [
                'field' => $this,
            ]
        );
    }

    public function normalizeValue(mixed $value, ?ElementInterface $element): mixed
    {
        return $value;
    }

    protected function inputHtml(mixed $value, ?ElementInterface $element, bool $inline): string
    {
        //return Html::textarea($this->handle, $value);
        
        if (get_class($element) == 'craft\\commerce\\elements\\Variant') {
            
            if (!$element->product->getFieldValue($this->handle)) {
                return 'Please select the options category on the product and resave';
            }
            
            $options = [];
            $categories = [];
            
            $parent = Entry::find()->sectionId($this->getSettings()['source'])->slug($element->product->getFieldValue($this->handle))->one();
            if ($parent) {
                $categories = $parent->children->all();
            }
            foreach( $categories as $key => $category ) {
                    $options[] = [
                        $category->title,
                        isset(Json::decodeIfJson($value)[$key]) ? Json::decodeIfJson($value)[$key][1] : '',
                    ];
            }
            
            // Render the variant input template
            return Craft::$app->getView()->renderTemplate(
                'variant-options/fields/VariantOptions_input',
                [
                    'name' => $this->handle,
                    'value' => $value,
                    'options' => $options,
                    'field' => $this,
                ]
            );
        }
        
        $options = [[
            'label' => 'Please select',
            'value' => 'null',
        ]];
        foreach( Entry::find()->sectionId($this->getSettings()['source'])->level(1)->all() as $category ) {
            $options[] = [
                'label' => $category->title,
                'value' => $category->slug,
            ];
        }
        
        // Render the product input template
        return Craft::$app->getView()->renderTemplate(
            'variant-options/fields/VariantOptions_product_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'options' => $options,
                'field' => $this,
            ]
        );
    }

    public function getElementValidationRules(): array
    {
        return [];
    }

    protected function searchKeywords(mixed $value, ElementInterface $element): string
    {
        return StringHelper::toString($value, ' ');
    }

    public function getElementConditionRuleType(): array|string|null
    {
        return null;
    }

    public static function queryCondition(
        array $instances,
        mixed $value,
        array &$params,
    ): ExpressionInterface|array|string|false|null {
        return parent::queryCondition($instances, $value, $params);
    }
}
