<?php

use atoum\atoum\configurator;
use atoum\atoum\reports\cobertura;
use atoum\atoum\runner;
use atoum\atoum\scripts;

if (defined('atoum\atoum\scripts\runner') === true) {
    scripts\runner::addConfigurationCallable(function (configurator $script, runner $runner) {
        $extension = new cobertura\extension($script);

        $extension->addToRunner($runner);
    });
}
