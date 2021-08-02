
# jdslv/atoum-report-cobertura

> Cobertura report for atoum

[![GitLab](https://img.shields.io/static/v1?message=GitLab&logo=gitlab&color=grey&label=)](https://gitlab.com/jdslv/atoum-report-cobertura)
[![Latest stable version](https://img.shields.io/packagist/v/jdslv/atoum-report-cobertura)](https://packagist.org/packages/jdslv/atoum-report-cobertura)
[![Build status](https://gitlab.com/jdslv/atoum-report-cobertura/badges/main/pipeline.svg)](https://gitlab.com/jdslv/atoum-report-cobertura/-/pipelines)
[![Coverage status](https://img.shields.io/codecov/c/gitlab/jdslv/atoum-report-cobertura)](https://codecov.io/gl/jdslv/atoum-report-cobertura/)
[![Minimal PHP version](https://img.shields.io/packagist/php-v/jdslv/atoum-report-cobertura)](https://gitlab.com/jdslv/atoum-report-cobertura)
[![License](https://img.shields.io/packagist/l/jdslv/atoum-report-cobertura)](https://gitlab.com/jdslv/atoum-report-cobertura/-/blob/main/LICENSE)


## Install it

Install extension using [composer](https://getcomposer.org):

```
composer require --dev jdslv/atoum-report-cobertura
```

The extension is automatically added to atoum configuration.

## Use it

Add the following code to your configuration file:

```php
<?php // .atoum.php

$cobertura = new atoum\atoum\reports\cobertura();
$writer = new atoum\atoum\writers\file('./cobertura.xml');
$cobertura->addWriter($writer);
$runner->addReport($cobertura);
```

## License

`jdslv/atoum-report-cobertura` is released under the MIT License.
See the bundled [LICENSE](LICENSE) file for details.

![atoum](http://atoum.org/images/logo/atoum.png)
