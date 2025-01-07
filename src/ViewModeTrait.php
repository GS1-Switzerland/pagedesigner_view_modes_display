<?php

namespace Drupal\pagedesigner_view_modes_display;

/**
 * View mode trait.
 */
trait ViewModeTrait {

  /**
   * Get view mode options.
   */
  public function getViewModeOptions() {
    $entityDisplayRepository = \Drupal::service('entity_display.repository');
    $view_modes = $entityDisplayRepository->getViewModes('pagedesigner_element');
    $options = [];
    foreach ($view_modes as $view_mode => $view_mode_info) {
      $options[$view_mode] = $view_mode_info['label'];
    }
    return $options;
  }

}
