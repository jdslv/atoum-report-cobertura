
# jdslv/atoum-report-cobertura

[![Latest Stable Version](https://poser.pugx.org/jdslv/atoum-report-cobertura/v)](https://packagist.org/packages/jdslv/atoum-report-cobertura)
[![Build Status](https://gitlab.com/jdslv/atoum-report-cobertura/badges/main/pipeline.svg)](https://gitlab.com/jdslv/atoum-report-cobertura/-/pipelines)
[![Coverage Status](https://coveralls.io/repos/gitlab/jdslv/atoum-report-cobertura/badge.svg?branch=main)](https://coveralls.io/gitlab/jdslv/atoum-report-cobertura?branch=main)
[![License](https://poser.pugx.org/jdslv/atoum-report-cobertura/license)](https://gitlab.com/jdslv/atoum-report-cobertura/-/blob/main/LICENSE)

## Install it

Install extension using [composer](https://getcomposer.org):

```
composer require --dev jdslv/atoum-report-cobertura
```

The extension is automatically added to atoum configuration.

## Use it

Add the following code to your configuration file:

```php
<?php

// .atoum.php

$cobertura = new \mageekguy\atoum\reports\cobertura();
$writer = new \mageekguy\atoum\writers\file('./cobertura.xml');
$cobertura->addWriter($writer);
$runner->addReport($cobertura);
```

## License

reports-extension is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.

![atoum](http://atoum.org/images/logo/atoum.png)
