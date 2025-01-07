<?php

namespace Drupal\pagedesigner_view_modes_display;

use Drupal\Core\Entity\EntityInterface;
use Drupal\pagedesigner\ElementViewBuilder as PagedesignerElementViewBuilder;
use Drupal\pagedesigner\Entity\ElementInterface;

/**
 * Class ElementViewBuilder.
 *
 * Overrides the view builder for the pagedesigner_element entity type.
 *
 * @package Drupal\pagedesigner_view_modes_display
 */
class ElementViewBuilder extends PagedesignerElementViewBuilder {

  /**
   * {@inheritDoc}
   */
  public function getSpecialViewModes() {
    $available_view_modes = $this->entityDisplayRepository->getViewModes($this->entityTypeId);
    $view_modes = array_merge(parent::getSpecialViewModes(), array_keys($available_view_modes));
    return $view_modes;
  }

}
