# CakePHP Sermepa Plugin

This plugin helps setting payments up for the Sermepa TPV service.

## Requirements

* CakePHP 2.x

## Installation

* Copy or clone the files to `app/Plugin/Sermepa`
* Load the plugin in `app/Config/bootstrap.php`, use `CakePlugin::loadAll();` or `CakePlugin::load('Sermepa');`

### Using Composer

Ensure `require` is present in `composer.json`, you can add it with the following command:
```
php composer.phar require berarma/cakephp-sermepa
```

## Use

Create your configuration like this:

```
$config = array(
    'Sermepa' => array(
        'serviceUrl' => 'https://sis-t.redsys.es:25443/sis/realizarPago', // Testing
        // Use 'https://sis.redsys.es/sis/realizarPago' for the real environment
    	'extendedSignature' => false,
    	'merchantName' => 'Merchant Name',
    	'merchantCode' => '000000001',
    	'secretKey' => 'QWERTYASDF0123456789',
    	'terminal' => '001',
    	'currency' => '978',
    	'consumerLanguage' => '1',
    	'merchantUrl' => 'http://example.com/get_notification',
    ));
```

Setting things up in the Controller:

```
public $components = array('Sermepa');
public $helpers = array('Sermepa');
```

Initiating a transaction in the Controller:

```
$this->Sermepa->createTransaction($orderId, $amount, '0');
```

Rendering the form that sends the user to the TPV in the View:
```
<?php echo $this->Sermepa->renderForm(array('id' => 'sermepa_form', 'target' => '_blank')); ?>
<?php echo $this->Html->scriptBlock('$( "#sermepa_form" ).submit();'); ?>
```

Getting a notification in the Controller:

```
$notification = $this->Sermepa->getNotification();
```

See the Test files to find more use examples.

## License

This plugin is licensed under the GPL v2 license. This is a plugin, you're just required to share your modifications/fixes/improvements to it. You aren't required to relicense your own projects because you're using and/or redistributing this plugin with them.
