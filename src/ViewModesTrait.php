<?php

namespace Drupal\pagedesigner_view_modes_display;

/**
 * View modes trait.
 */
trait ViewModesTrait {

  /**
   * Cached view modes options.
   *
   * @var array|null
   */
  protected $viewModesOptionsCache = NULL;

  /**
   * Get view mode options.
   *
   * @return array
   *   The view modes options array.
   */
  public function getViewModesOptions() {
    // Return cached options if available.
    if ($this->viewModesOptionsCache !== NULL) {
      return $this->viewModesOptionsCache;
    }

    $config = \Drupal::config('pagedesigner_view_modes_display.settings');
    $view_modes_string = $config->get('view_modes');
    
    if (empty($view_modes_string)) {
      $this->viewModesOptionsCache = [];
      return $this->viewModesOptionsCache;
    }

    $view_modes = array_filter(array_map('trim', explode(PHP_EOL, $view_modes_string)));
    $options = [];
    foreach ($view_modes as $view_mode) {
      if (!empty($view_mode)) {
        $options[$view_mode] = $view_mode;
      }
    }

    $this->viewModesOptionsCache = $options;
    return $this->viewModesOptionsCache;
  }

  /**
   * Clear the cached view modes options.
   */
  public function clearViewModesOptionsCache() {
    $this->viewModesOptionsCache = NULL;
  }

}
