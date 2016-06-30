[![Build Status](https://travis-ci.org/berarma/cakephp-redsys.svg?branch=master)](https://travis-ci.org/berarma/cakephp-redsys) [![Coverage Status](https://coveralls.io/repos/berarma/cakephp-redsys/badge.png?branch=master)](https://coveralls.io/r/berarma/cakephp-redsys?branch=master)

# CakePHP Redsys Plugin

This plugin enables online payments using the Redsýs TPV service.

## Requirements

* CakePHP 2.x

## Installation

* Copy or clone the files to `app/plugins/Redsys`
* Load the plugin in `app/Config/bootstrap.php`, use `CakePlugin::loadAll();`
  or `CakePlugin::load('Redsys');`

### Using Composer

Ensure `require` is present in `composer.json`, you can add it with the
following command:
```
php composer.phar require berarma/cakephp-redsys
```

## Use

Create your configuration:

```php
$config = array(
    'Redsys' => array(
        // Use 'https://sis.redsys.es/sis/realizarPago' for the real environment
        'url' => 'https://sis-t.redsys.es:25443/sis/realizarPago', // Testing
        'secretKey' => 'QWERTYASDF0123456789',
        'defaults' => [
            'DS_MERCHANT_MERCHANTCODE' => '000000001',
            'DS_MERCHANT_CURRENCY' => '978',
            'DS_MERCHANT_TRANSACTIONTYPE' => '0',
            'DS_MERCHANT_TERMINAL' => '001',
            'DS_MERCHANT_MERCHANTURL' => 'http://example.com/notification',
            'DS_MERCHANT_URLOK' => 'http://example.com/ok',
            'DS_MERCHANT_URLKO' => 'http://example.com/ko',
        ],
    )
);
```

This is a basic configuration example. The defaults section will be merged with
any parameters passed in the requests. Please, read the Redsýs documentation to
learn about all the optional parameters that can be used.

Setting things up in the Controller:

```php
public $components = array('Redsys');
public $helpers = array('Redsys');
```

Initiating a transaction in the Controller:

```php
$this->Redsys->request([
    'DS_MERCHANT_ORDER' => time(),
    'DS_MERCHANT_AMOUNT' => '100',
]);
```

Rendering the form that sends the user to the TPV in the View:

```php
<?php echo $this->Redsys->renderForm(array('id' => 'redsys_form', 'target' => '_blank')); ?>
<?php echo $this->Html->scriptBlock('$( "#redsys_form" ).submit();'); ?>
```

Getting the response in the Controller:

```php
$response = $this->Redsys->response();
```

See the Test files to find more use examples.

## License

This plugin is licensed under the GPL v2 license. Most common uses of this
plugin won't constitute a derived work, thus, you may use it and include it in
your app independently of the license you've choosen for your code as long as
the plugin itself is still distributed with its original license. But using
this plugin from another plugin that provides modified or improved
functionality may constitute a derived work and thus require you to use the
same license for your plugin.

