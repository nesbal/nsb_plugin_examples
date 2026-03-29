<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a custom number input widget with decimal normalization.
 */
#[FieldWidget(
  id: "nsb_number_widget",
  label: new TranslatableMarkup("Number (NSB)"),
  field_types: ["decimal", "float"]
)]
class NumberFieldWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $value = $items[$delta]->value ?? '';

    $element['value'] = [
      '#type' => 'textfield',
      '#default_value' => $value,
      '#element_validate' => [
        [$this, 'normalizeAndValidate'],
      ],
    ];

    return $element;
  }

  /**
   * Normalizes and validates numeric input.
   */
  public function normalizeAndValidate(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $value = $element['#value'];

    $value = str_replace(',', '.', $value);
    $value = trim($value);

    if (!is_numeric($value)) {
      $form_state->setError($element, $this->t('Value must be a valid number.'));
      return;
    }

    $settings = $this->fieldDefinition->getSettings();

    $precision = (int) $settings['precision'];
    $scale = (int) $settings['scale'];

    $parts = explode('.', $value);
    $integer = $parts[0];
    $decimal = $parts[1] ?? '';

    $integer_digits = ltrim($integer, '-');

    $max_integer_digits = $precision - $scale;

    if (strlen($integer_digits) > $max_integer_digits) {
      $form_state->setError($element, $this->t('Number is too large.'));
      return;
    }

    if (strlen($decimal) > $scale) {
      $form_state->setError($element, $this->t('Too many decimal digits.'));
      return;
    }

    $element['#value'] = $value;
  }

}
