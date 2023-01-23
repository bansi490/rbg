# Drupal



This is an LGD module customised by the Royal Borough of Greenwich.

## install

Open the command prompt and install this module using the below

``` composer require rbg/drupalmodule ```

### custom webform element

For testing purposes, I have built one custom webform element called Webform RBG Element. Which is extended from the default autocomplete webform element.

Related files:

``` {YOUR-Project}\modules\custom\rbg\src\Element\WebformRBGElement.php ```
``` {YOUR-Project}\modules\custom\rbg\src\Plugin\WebformElement\WebformRBGElement.php ```

### Populate webform options from an external API.

```Options``` - These we use as options and values within the checkbox, radio buttons, dropdown and autocomplete element.

Alter the hook(.module file) for the webform external options (hook_webform_options_WEBFORM_OPTIONS_ID_alter()).

To populate webform options from an external API first we need to create an empty set of webform options by going to /admin/structure/webform/config/options/manage/add
and then populating these options using hook_webform_options_WEBFORM_OPTIONS_ID_alter().
