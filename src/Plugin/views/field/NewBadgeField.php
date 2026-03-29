<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\views\field;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Displays a "New" badge for recently created content.
 */
#[ViewsField("nsb_new_badge")]
class NewBadgeField extends FieldPluginBase {

  /**
   * Time service for current request time.
   */
  protected TimeInterface $time;

  /**
   * Constructs a new NewBadgeField.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TimeInterface $time) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('datetime.time')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();

    $options['days'] = ['default' => 3];
    $options['label'] = ['default' => 'New'];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    $form['days'] = [
      '#type' => 'number',
      '#title' => $this->t('New threshold (days)'),
      '#default_value' => $this->options['days'],
      '#min' => 0,
      '#description' => $this->t('Content newer than this will be marked as "New".'),
    ];

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $this->options['label'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values): array {
    $entity = $this->getEntity($values);

    if (!$entity instanceof NodeInterface) {
      return [];
    }

    $days = (int) $this->options['days'];
    $now = $this->time->getCurrentTime();
    $threshold = $now - ($days * 86400);

    if ($entity->getCreatedTime() >= $threshold) {
      return [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => 'New',
        '#attributes' => ['class' => ['nsb-badge-new']],
      ];
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Intentionally left empty.
  }

}
