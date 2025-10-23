# Pagedesigner View Modes Display

## Overview

The Pagedesigner View Modes Display module allows administrators to define display options for Pagedesigner elements based on the view mode of the parent entity. This module extends the core Pagedesigner functionality by providing the ability to hide specific elements in different view modes.

## Features

- **View Mode-Based Element Visibility**: Hide or show Pagedesigner elements based on the current view mode
- **Administrative Configuration**: Configure available view modes through an admin interface
- **Field Integration**: Provides a custom field type for selecting which view modes to hide elements in
- **URL Query Parameter Support**: Optionally determine view mode from URL query parameters
- **Caching Integration**: Proper cache invalidation when settings change

## Installation

1. Ensure the `pagedesigner` module is installed and enabled
2. Place this module in your `/modules` directory
3. Enable the module: `drush en pagedesigner_view_modes_display`
4. Clear cache: `drush cr`

## Configuration

### Admin Settings

Navigate to **Administration > Configuration > Pagedesigner View Modes Display Settings** (`/admin/config/pagedesigner-view-modes-display/settings`) to configure:

1. **View Modes**: Define custom view modes (one per line) that will be available for element visibility control
2. **URL Query Parameter**: Enable and configure URL-based view mode detection
   - Check "Use URL query parameter" to enable
   - Set the query parameter name (default: `viewmode`)

### Field Configuration

The module automatically adds a `field_hidden_view_modes` field to Pagedesigner element types:

- **block**
- **component**
- **row**

This field allows content editors to select which view modes should hide the element.

## Usage

### For Content Editors

1. When editing a Pagedesigner element, you'll see a "View Modes" field
2. Select the view modes where this element should be hidden
3. The element will automatically be hidden when the parent entity is displayed in those view modes

### For Developers

#### View Builder Override

The module overrides the default Pagedesigner element view builder to:

- Add support for custom view modes
- Implement lazy loading for better performance
- Handle view mode-based element hiding

#### Handler Plugin

The `ViewModesDisplayHandler` provides:

- Element visibility control based on view modes
- Serialization/deserialization of hidden view modes data
- Cache tag management for configuration changes

## Technical Details

### Architecture

- **ElementViewBuilder**: Extends the core Pagedesigner view builder
- **ViewModesTrait**: Provides reusable view mode option functionality
- **ViewModesDisplayHandler**: Handles the core logic for element visibility
- **Custom Field Components**: Field formatter for pagedesigner item

## Permissions

- `administer pagedesigner view modes display settings`: Access to module configuration

## Contributing

This module follows the standard Drupal.org development process. 
All contributions should go through the issue queue at [drupal.org/project/pagedesigner_view_modes_display](https://www.drupal.org/project/pagedesigner_view_modes_display).
