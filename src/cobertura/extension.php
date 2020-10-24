<?php declare(strict_types=1);

namespace mageekguy\atoum\reports\cobertura;

use mageekguy\atoum\configurator;
use mageekguy\atoum\reports;

class extension extends reports\extension
{
    public function __construct(configurator $configurator = null)
    {
        if ($configurator) {
            $handler = function ($script, $argument, $values) {
                $script->getRunner()->addTestsFromDirectory(dirname(__DIR__, 2) . '/tests/units');
            };

            $configurator->getScript()->getArgumentsParser()
                ->addHandler($handler, ['--test-ext'])
                ->addHandler($handler, ['--test-it'])
            ;
        }
    }
}
