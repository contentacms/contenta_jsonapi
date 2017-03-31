<?php

namespace Drupal\thunder\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Powered by Thunder' block.
 *
 * @Block(
 *   id = "thunder_powered_by_block",
 *   admin_label = @Translation("Powered by Thunder")
 * )
 */
class ThunderPoweredByBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['label_display' => FALSE];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return ['#markup' => '<span>' . $this->t('Powered by <a href=":poweredby">Thunder</a>', [':poweredby' => 'http://www.thunder.org']) . '</span>'];
  }

}
