<?php

use mageekguy\atoum\configurator;
use mageekguy\atoum\reports\cobertura;
use mageekguy\atoum\runner;
use mageekguy\atoum\scripts;

if (defined('mageekguy\atoum\scripts\runner') === true) {
    scripts\runner::addConfigurationCallable(function (configurator $script, runner $runner) {
        $extension = new cobertura\extension($script);

        $extension->addToRunner($runner);
    });
}
