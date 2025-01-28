class PagedesignerViewModesDisplayHandler {
  /**
   * Construct a pagedesigner View modes display Manager.
   *
   * @param {Object} editor
   * @param {Object} jQuery
   * @param {Object} settings
   */
  constructor(editor, jQuery, settings) {
    this.editor = editor;
    this.jQuery = jQuery;
    this.settings = settings;
    $ = this.jQuery;
    this.component = {};
    this.view_modes = this.settings.view_modes;
  }
  init(component) {
    this.component = component;

    if ($('.gjs-clm-vmd').length == 0) {
      var vmd_container = $('<div class="gjs-clm-vmd gjs-one-bg gjs-two-color" ><div data-vmd-container></div></div>');
      vmd_container.prepend($('<p class="sidebar-subtitle">' + Drupal.t('View Modes Display') + '</p>'));
      vmd_container.insertBefore('.gjs-clm-tags');
    }
    $('[data-vmd-container]').html('');
    self = this;
    self.formEditViewModes();
  }
  formEditViewModes() {
    var component = this.component;
    self = this;
    var hidden_view_mode_form = $('<div class="edit-vmd"></div>');
    var field_holder = $('<label></label>');
    field_holder.append('<p>' + Drupal.t('Hide element for the selected view modes') + '</p>');
    var checkbox_container = $('<div class="vdm-checkbox-wrapper responsive-class"></div>');
    if (Object.keys(self.view_modes).length > 0) {
      Object.keys(self.view_modes).forEach(option => {
        var checkbox_element = $('<label class="inline-label"><input type="checkbox" value="' + option + '"/>' + self.view_modes[option] + '</label>');
        if (component.get('hidden_view_modes') && component.get('hidden_view_modes').indexOf(option) != -1) {
          checkbox_element.find('input').attr('checked', 'checked');
        }
        checkbox_element.find('input').on('change', function () {
          var selected_modes = [];
          checkbox_container.find('input:checked').each(function () {
            selected_modes.push($(this).val());
          });
          component.attributes.hidden_view_modes = selected_modes;
          component.set('changed', true);
        });
        checkbox_container.append(checkbox_element);
      });
    }
    else {
      checkbox_container.append('<p>' + Drupal.t('No view modes available') + '</p>');
      checkbox_container.append('<a href="/admin/config/pagedesigner-view-modes-display/settings" target="_blank">' + Drupal.t('Add view modes') + '</a>');
    }
    field_holder.append(checkbox_container);
    hidden_view_mode_form.append(field_holder);
    $('[data-vmd-container]').append(hidden_view_mode_form);
  }
}
(function ($, Drupal) {
  Drupal.behaviors.pagedesigner_init_component_view_modes_display = {
    attach: function (context, settings) {
      once('pagedesigner_init_component_view_modes_display', 'body', context).forEach(() => {
        $(document).on('pagedesigner-after-init', function (e, editor, options) {
          editor.on('run:edit-component', (component, sender) => {
            if (drupalSettings && typeof drupalSettings.pagedesigner_view_modes_display != 'undefined') {
              var pagedesigner_view_modes_display_handler = new PagedesignerViewModesDisplayHandler(editor, jQuery, drupalSettings.pagedesigner_view_modes_display);
              pagedesigner_view_modes_display_handler.init(editor.getSelected());
            }
          });
          editor.on('component:selected', (component, sender) => {
            if (drupalSettings && typeof drupalSettings.pagedesigner_view_modes_display != 'undefined') {
              var pagedesigner_view_modes_display_handler = new PagedesignerViewModesDisplayHandler(editor, jQuery, drupalSettings.pagedesigner_view_modes_display);
              pagedesigner_view_modes_display_handler.init(editor.getSelected());
            }
          });
        });
        // extend some component functions
        $(document).on('pagedesigner-init-components', function (e, editor, options) {

          ['component', 'row', 'block'].forEach(function (cmp_type) {
            editor.DomComponents.addType(cmp_type, {
              extend: cmp_type,
              model: {
                initToolbar(...args) {
                  editor.DomComponents.getType('pd_base_element').model.prototype.initToolbar.apply(this, args);
                },

                afterSave() {
                  editor.spinner.disable();
                },

                serialize() {
                  var component_data = editor.DomComponents.getType('pd_base_element').model.prototype.serialize.apply(this, []);
                  if (this.attributes.hidden_view_modes) {
                    component_data.hidden_view_modes = [...this.attributes.hidden_view_modes];
                  }
                  return component_data;
                },

                handleLoadResponse(response) {
                  editor.DomComponents.getType('pd_base_element').model.prototype.handleLoadResponse.apply(this, [response]);
                  if (response['hidden_view_modes']) {
                    this.attributes.hidden_view_modes = response['hidden_view_modes'];
                    this.attributes.previousVersion.hidden_view_modes = response['hidden_view_modes'];
                  }
                },

                restore() {
                  editor.DomComponents.getType('pd_base_element').model.prototype.restore.apply(this, []);
                  if (this.get('previousVersion').hidden_view_modes) {
                    this.attributes.hidden_view_modes = this.get('previousVersion').hidden_view_modes;
                  }
                },
              }
            });
          });
        });
      });
    }
  };
})(jQuery, Drupal);