<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\Condition;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\UserStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a condition to check if the current user logged in within a given number of days.
 */
#[Condition(
  id: "nsb_recent_login",
  label: new TranslatableMarkup("Recent login condition")
)]
class RecentLoginCondition extends ConditionPluginBase implements ContainerFactoryPluginInterface {
  /**
   * Seconds in a day.
   */
  protected const SECONDS_IN_DAY = 86400;

  /**
   * Current user service.
   */
  protected AccountProxyInterface $currentUser;

  /**
   * Time service.
   */
  protected TimeInterface $time;

  /**
   * User storage.
   */
  protected UserStorageInterface $userStorage;

  /**
   * Constructs the condition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxyInterface $current_user,
    TimeInterface $time,
    UserStorageInterface $user_storage,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->time = $time;
    $this->userStorage = $user_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('datetime.time'),
      $container->get('entity_type.manager')->getStorage('user'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'days' => 7,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['days'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of days since last login'),
      '#default_value' => $this->configuration['days'],
      '#min' => 0,
      '#description' => $this->t('Check if user logged in within this number of days.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['days'] = (int) $form_state->getValue('days');
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    if ($this->currentUser->isAnonymous()) {
      return FALSE;
    }

    $user = $this->userStorage->load($this->currentUser->id());

    if (!$user) {
      return FALSE;
    }

    $lastLogin = $user->getLastLoginTime();

    if (!$lastLogin) {
      return FALSE;
    }

    $days = (int) ($this->configuration['days'] ?? 0);
    $threshold = $this->time->getCurrentTime() - ($days * self::SECONDS_IN_DAY);

    $result = $lastLogin >= $threshold;

    if ($this->isNegated()) {
      return !$result;
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    $days = (int) ($this->configuration['days'] ?? 0);

    if ($this->isNegated()) {
      return $this->t('User has NOT logged in within the last @days days', [
        '@days' => $days,
      ]);
    }

    return $this->t('User logged in within the last @days days', [
      '@days' => $days,
    ]);
  }

}
