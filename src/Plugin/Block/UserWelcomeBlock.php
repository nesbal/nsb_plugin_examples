<?php

namespace Drupal\nsb_plugin_examples\Plugin\Block;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a user welcome block displaying user-specific information.
 */
#[Block(
  id: 'nsb_user_welcome_block',
  admin_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('User Welcome Block'),
)]
class UserWelcomeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   */
  protected AccountProxyInterface $currentUser;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The date formatter.
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * The time service.
   */
  protected TimeInterface $time;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxyInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager,
    DateFormatterInterface $date_formatter,
    TimeInterface $time,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('date.formatter'),
      $container->get('datetime.time'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $items = [];

    if ($this->currentUser->isAuthenticated()) {
      $username = $this->currentUser->getDisplayName();
      $date = $this->dateFormatter->format(
        $this->time->getCurrentTime(),
        'custom',
        'l j F'
      );
      $content_count = $this->getAuthoredContentCount($this->currentUser->id());

      $items[] = ['#markup' => $this->t('Welcome @name', ['@name' => $username])];
      $items[] = ['#markup' => $date];
      $items[] = ['#markup' => $this->t('Content by you: @count', ['@count' => $content_count])];
      $items[] = Link::fromTextAndUrl($this->t('My account'), Url::fromRoute('entity.user.canonical', ['user' => $this->currentUser->id()]))->toRenderable();
      $items[] = Link::fromTextAndUrl($this->t('Logout'), Url::fromRoute('user.logout'))->toRenderable();
    }
    else {
      $items[] = ['#markup' => $this->t('Welcome pal!')];
      $items[] = Link::fromTextAndUrl($this->t('Log in'), Url::fromRoute('user.login'))->toRenderable();
      $items[] = Link::fromTextAndUrl($this->t('Register'), Url::fromRoute('user.register'))->toRenderable();
    }
    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];
  }

  /**
   * Returns the number of node content items authored by the given user.
   */
  protected function getAuthoredContentCount(int $uid): int {
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('uid', $uid)
      ->count();

    return (int) $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowed()
      ->addCacheContexts(['user']);
  }

}
