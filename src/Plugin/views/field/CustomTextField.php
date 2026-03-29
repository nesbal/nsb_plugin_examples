<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\views\field;

use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Provides a simple custom Views field.
 */
#[ViewsField("nsb_custom_text")]
class CustomTextField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values): array {
    return [
      '#markup' => 'Welcome to my world!',
    ];
  }

}
