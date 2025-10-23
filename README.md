# Pagedesigner View Modes Display

## Overview

The Pagedesigner View Modes Display extends the core Pagedesigner functionality
by providing the ability to hide specific elements in different view modes.

## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

## Configuration

Navigate to
**Administration > Configuration > Pagedesigner View Modes Display Settings**
(`/admin/config/pagedesigner-view-modes-display/settings`)
to configure:

1. **View Modes**:
Define custom view modes (one per line) that will be available
for element visibility control
2. **URL Query Parameter**:
Enable and configure URL-based view mode detection
   - Check "Use URL query parameter" to enable
   - Set the query parameter name (default: `viewmode`)

## Usage

### For Content Editors

1. When editing a Pagedesigner element, you'll see a "View Modes" field
2. Select the view modes where this element should be hidden
3. The element will automatically be hidden when the parent entity is displayed
in those view modes

## Permissions

- `administer pagedesigner view modes display settings`:
Access to module configuration

## Contributing

This module follows the standard Drupal.org development process.
All contributions should go through the issue queue at [drupal.org/project/pagedesigner_view_modes_display](https://www.drupal.org/project/pagedesigner_view_modes_display).
