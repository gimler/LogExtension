# LogExtension

Provides a simple listener to grab the server access logs splitted for each feature.

[![Dependency Status](https://www.versioneye.com/user/projects/525fcb18632bac286400007d/badge.png)](https://www.versioneye.com/user/projects/525fcb18632bac286400007d)

## Installation

This extension requires:

* Behat 2.4+
* Mink 1.4+
* Mink extension

### Through Composer

The easiest way to keep your suite updated is to use
`Composer <http://getcomposer.org>`_.

You can add log extension as dependancies for your project.

#### Project dependancy

1. Define dependencies in your ``composer.json``:

```js

{
  "require": {
    ...
    "gimler/log-extension": "*"
  }
}
```

2. Install/update your vendors:

```bash
$ curl http://getcomposer.org/installer | php
$ php composer.phar install
```
3. Activate extension by specifying its class in your ``behat.yml``:

```yaml
# behat.yml
default:
  # ...
  extensions:
    Gimler\Behat\LogExtension\Extension: ~
```

## Configuration

* ``output_path`` - the directory where store `.jmx` files
* ``access_log`` - path to the server access log

## JMeter

There is a script `tests\jmeter_generator.php` it generates jmeter testplan that reads all access logs.

## Copyright

Copyright (c) 2013 Gordon Franke (blog.gimler.de). See LICENSE for details.

## Contributors

* Gordon Franke [gimler](http://github.com/gimler) [lead developer]
* Other [awesome developers](https://github.com/gimler/LogExtension/graphs/contributors)
