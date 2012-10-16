cakephp-nicedate
================

Replaces CakePHP's default date input (three dropdowns) with text inputs using jQueryUI date picker and time picker inputs

##Installation

Add the files to your plugin directory (`app/Plugin`) in a sub-directory called `NiceDate/`

Add the plugin to your `Config/bootstrap.php` file as 

```php
CakePlugin::load('NiceDate');
```

Add the NiceDateForm helper to your helpers as an alias classname for the built-in Cakephp FormHelper:

```php
App::import('View/Helper', 'NiceDate.NiceDateFormHelper');

class AppController extends Controller {
  public $helpers = array(
		'Form' => array(
			'className' => 'NiceDateForm'
		),
  );
}
```

If you have your own FormHelper class, you can extend NiceDateFormHelper.

Optionally, you can also change the datetime validation for the new inputs by making your models (or one model) inherit the NiceDate behavior:

In AppModel or specific models:

```php
App::uses( 'NiceDate.NiceDate', 'Behavior' );

class AppModel extends Model {
  public $actsAs = array( 'NiceDate.NiceDate' );
}
```

##What does it do?

NiceDate overwrites the built-in FormHelper input types datetime, date, and time. It also adds duration, which is two datetimes which are linked together.

##Known bugs

For some reason, you can't overwrite the default notEmpty validation function in a behavior. So instead dates need to use the "nicenotempty" validation due to the change in date inputs.