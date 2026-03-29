<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\Condition;

use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a simple condition example.
 */
#[Condition(
  id: "custom_condition",
  label: new TranslatableMarkup("Custom Condition")
)]
class CustomCondition extends ConditionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    return 'Always true condition';
  }

}
