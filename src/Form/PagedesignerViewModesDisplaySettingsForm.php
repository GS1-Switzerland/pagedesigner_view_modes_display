<?php

namespace Drupal\pagedesigner_view_modes_display\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The settings form for pagedesigner_view_modes_display.
 */
class PagedesignerViewModesDisplaySettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'pagedesigner_view_modes_display.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pagedesigner_view_modes_display_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    parent::buildForm($form, $form_state);
    $config = $this->config('pagedesigner_view_modes_display.settings');

    $form['view_modes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Pagedesigner View modes'),
      '#description' => $this->t('List of view modes to be used by pagedesigner to manage the display of the elements. One by line.'),
      '#default_value' => $config->get('view_modes'),
    ];

    $form['use_url_query_parameter'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use URL query parameter'),
      '#description' => $this->t('If checked, the view mode will be determined by the URL query parameter set below.'),
      '#default_value' => $config->get('use_url_query_parameter'),
    ];

    $form['url_query_parameter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL query parameter'),
      '#description' => $this->t('The query parameter to use to determine the view mode.'),
      '#default_value' => $config->get('url_query_parameter') ?? 'viewmode',
      '#states' => [
        'visible' => [
          ':input[name="use_url_query_parameter"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('pagedesigner_view_modes_display.settings');
    $config->set('view_modes', $form_state->getValue('view_modes'));
    $config->set('use_url_query_parameter', $form_state->getValue('use_url_query_parameter'));
    $config->set('url_query_parameter', $form_state->getValue('url_query_parameter'));
    $config->save();
  }

}
