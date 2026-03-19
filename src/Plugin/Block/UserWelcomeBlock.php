<?php

namespace Drupal\nsb_plugin_examples\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;

#[Block(
  id: 'nsb_simple_block',
  admin_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('NSB Simple Block'),
)]
class UserWelcomeBlock extends BlockBase {

  public function build(): array {
    return [
      '#markup' => 'This is Nesibe\'s world!',
    ];
  }

}
