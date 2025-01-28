<?php

namespace Drupal\pagedesigner_view_modes_display;

/**
 * View modes trait.
 */
trait ViewModesTrait {

  /**
   * Get view mode options.
   */
  public function getViewModesOptions() {
    $config = \Drupal::config('pagedesigner_view_modes_display.settings');
    if (!$config->get('view_modes')) {
      return [];
    }
    $view_modes = explode(PHP_EOL, $config->get('view_modes'));
    $options = [];
    foreach ($view_modes as $view_mode) {
      $options[$view_mode] = $view_mode;
    }
    return $options;
  }

}
