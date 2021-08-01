<?php declare(strict_types=1);

namespace atoum\atoum\reports\cobertura\tests\units;

use atoum\atoum;
use mock;

class extension extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(atoum\reports\extension::class)
        ;
    }

    public function test__construct()
    {
        $this
            ->given($runner = new atoum\scripts\runner(uniqid()))
            ->and($parser = new mock\atoum\atoum\script\arguments\parser())
            ->and($runner->setArgumentsParser($parser))

            ->if($configurator = new mock\atoum\atoum\configurator($runner))
            ->then
                ->object($this->newTestedInstance())

            ->if($this->resetMock($parser))
            ->then
                ->object($this->newTestedInstance($configurator))
                ->mock($parser)
                    ->call('addHandler')
                        ->twice()
        ;
    }
}
