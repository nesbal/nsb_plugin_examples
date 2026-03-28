<?php

namespace Drupal\nsb_drupal_basic_module\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a custom field formatter.
 */
#[FieldFormatter(
  id: "custom_field_formatter",
  label: new TranslatableMarkup("Custom Field Formatter"),
  field_types: ["string"]
)]
class CustomFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#markup' => $item->value,
      ];
    }

    return $elements;
  }

}
