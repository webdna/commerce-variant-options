<?php

namespace webdna\commerce\variantoptions;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use webdna\commerce\variantoptions\fields\VariantOptions as VariantOptionsField;
use yii\base\Event;

/**
 * Variant-Options plugin
 *
 * @method static VariantOptions getInstance()
 * @author webdna <info@webdna.co.uk>
 * @copyright webdna
 * @license https://craftcms.github.io/license/ Craft License
 */
class VariantOptions extends Plugin
{
    public string $schemaVersion = '1.0.0';

    public static function config(): array
    {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = VariantOptionsField::class;
        });
    }
}
