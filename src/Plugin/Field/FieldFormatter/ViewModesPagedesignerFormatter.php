<?php

namespace Drupal\pagedesigner_view_modes_display\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\pagedesigner\PagedesignerService;
use Drupal\pagedesigner\Service\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * View Modes Plugin implementation of the 'pagedesigner_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "pagedesigner_formatter",
 *   label = @Translation("View modes Pagedesigner Formatter"),
 *   field_types = {
 *     "pagedesigner_item"
 *   }
 * )
 */
class ViewModesPagedesignerFormatter extends FormatterBase {

  /**
   * Constructs a StringFormatter instance.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\pagedesigner\PagedesignerService $pagedesignerService
   *   The pagedesigner service.
   * @param \Drupal\pagedesigner_view_modes_display\Service\Renderer $pagedesignerRenderer
   *   The pagedesigner renderer.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    protected PagedesignerService $pagedesignerService,
    protected RendererInterface $pagedesignerRenderer,
    protected AccountInterface $currentUser,
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('pagedesigner.service'),
      $container->get('pagedesigner_view_modes_display.renderer'),
      $container->get('current_user'),
      $container->get('current_route_match'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays the pagedesigner content.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $node = $items->getEntity();
    $view_mode = $this->viewMode;
    if ($node != NULL && $node instanceof ContentEntityInterface) {
      $node = $node->getTranslation($langcode);
      foreach ($items as $item) {
        $container = $item->entity;
        if ($container != NULL && $container->hasTranslation($langcode)) {
          $container = $container->getTranslation($langcode);
          if ($this->pagedesignerService->isPagedesignerRoute()) {
            $this->pagedesignerRenderer->renderForEdit($container, $node);
          }
          elseif ($this->currentUser->hasPermission('view unpublished pagedesigner element entities')) {
            $this->pagedesignerRenderer->render($container, $node);
          }
          elseif ($view_mode == 'full') {
            $this->pagedesignerRenderer->renderForPublic($container, $node);
          }
          else {
            $this->pagedesignerRenderer->renderForViewMode($container, $node, $view_mode);
          }
          $elements[] = $this->pagedesignerRenderer->getOutput();
        }
      }
    }
    return $elements;
  }

}
