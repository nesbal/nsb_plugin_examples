<?php

namespace Drupal\nsb_plugin_examples\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Minimal Field Widget.
 */
#[FieldWidget(
  id: "custom_field_widget",
  label: new TranslatableMarkup("Custom Field Widget"),
  field_types: ["string"]
)]
class CustomFieldWidget extends WidgetBase {

  /**
   * Builds the form element for the field widget.
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = [
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->value ?? '',
    ];

    return $element;
  }

}
