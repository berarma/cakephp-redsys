# Redsýs plugin for CakePHP

[![Build Status](https://travis-ci.com/berarma/cakephp-redsys.svg?branch=master)](https://travis-ci.com/berarma/cakephp-redsys)
[![codecov](https://codecov.io/gh/berarma/cakephp-redsys/branch/master/graph/badge.svg)](https://codecov.io/gh/berarma/cakephp-redsys)

CakePHP plugin implementing the Redsýs TPV service API.

## Requirements

* CakePHP 3.4+

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org):

```
composer require berarma/cakephp-redsys
```

## Use

Load the component and configure:

```php
$this->loadComponent('Berarma/Redsys.Redsys', [
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
]);
```

This is a basic configuration example. The defaults array will be merged with
any parameters passed in the requests. Please, read the Redsýs documentation to
learn about all the optional parameters that can be used.

Load the helper:

```php
$this->loadHelper('Berarma/Redsys.Redsys');
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
<?php echo $this->Redsys->renderForm(['id' => 'redsys-form']); ?>
<?php echo $this->Html->scriptBlock('document.getElementById("redsys-form").submit();'); ?>
```

Getting the response in the Controller:

```php
$response = $this->Redsys->response();
```

The parameters from the response can then be accessed with:

```php
$response->get('DS_ORDER');
```

## Case sensitivity of parameters

The specification states that parameter names should use upper case or a mixed
style of CamelCase and underline characters. Since the mixed
CamelCase/underline style is confusing to say the least, and having 2 different
naming styles adds to the confusion, I've decided to use the upper case style
everywhere. That means all parameter names feeded to this plugin are converted
to upper case, it doesn't matter how they were.

## TODO

This plugin is a work in progress. You should expect compatibility breaking
changes.

## License

This plugin is licensed under the GPL v2 license. Most common uses of this
plugin won't constitute a derived work, thus, you may use it and include it in
your app independently of the license you've choosen for your code as long as
the plugin itself is still distributed with its original license. But using
this plugin from another plugin that provides modified or improved
functionality may constitute a derived work and thus require you to use the
same license for your plugin.

