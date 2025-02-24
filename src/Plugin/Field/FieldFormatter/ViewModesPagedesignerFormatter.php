<?php

namespace Drupal\pagedesigner_view_modes_display\Plugin\Field\FieldFormatter;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\pagedesigner\PagedesignerService;
use Drupal\pagedesigner\Service\RendererInterface;
use Drupal\pagedesigner_view_modes_display\ViewModesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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

  use ViewModesTrait;

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
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Then entity type manager.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
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
    protected RequestStack $requestStack,
    protected ConfigFactoryInterface $configFactory,
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
      $container->get('entity_type.manager'),
      $container->get('request_stack'),
      $container->get('config.factory'),
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
    $request_view_mode = $this->getRequestViewMode();
    if ($node != NULL && $node instanceof ContentEntityInterface) {
      $node = $node->getTranslation($langcode);
      foreach ($items as $item) {
        $container = $item->entity;
        if ($container != NULL && $container->hasTranslation($langcode)) {
          $container = $container->getTranslation($langcode);
          if ($request_view_mode) {
            $this->pagedesignerRenderer->renderForViewMode($container, $node, $request_view_mode);
          }
          else {
            if ($this->pagedesignerService->isPagedesignerRoute()) {
              $this->pagedesignerRenderer->renderForEdit($container, $node);
            }
            elseif ($this->currentUser->hasPermission('view unpublished pagedesigner element entities')) {
              $this->pagedesignerRenderer->render($container, $node);
            }
            elseif ($this->isPagedesignerViewMode()) {
              $this->pagedesignerRenderer->renderForViewMode($container, $node, $this->viewMode);
            }
            else {
              $this->pagedesignerRenderer->renderForPublic($container, $node);
            }
          }
          $elements[] = $this->pagedesignerRenderer->getOutput();
        }
      }
    }
    return $elements;
  }

  /**
   * Get the view mode from request.
   *
   * @return string
   *   The view mode.
   */
  protected function getRequestViewMode() {
    $config = $this->configFactory->get('pagedesigner_view_modes_display.settings');
    if (!$config->get('use_url_query_parameter')) {
      return NULL;
    }
    // Get request query parameter if it exists.
    $parameter = $config->get('url_query_parameter') ?? 'viewmode';
    $request = $this->requestStack->getCurrentRequest();
    return $request->query->get($parameter);
  }

  /**
   * Check if the view mode is configured to be hidden.
   *
   * @return bool
   *   Whether the view mode is hidden.
   */
  protected function isPagedesignerViewMode() {
    return in_array($this->viewMode, $this->getViewModesOptions());
  }

}
