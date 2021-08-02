<?php declare(strict_types=1);

namespace atoum\atoum\reports\cobertura\tests\units\reflection;

use atoum;
use atoum\atoum\reports\cobertura\reflection\klass as testedClass;
use ReflectionClass;

class klass extends atoum\atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ReflectionClass::class)
        ;
    }

    public function testGetFileName()
    {
        $this
            ->if($this->newTestedInstance(testedClass::class))
            ->and($base = dirname(__DIR__, 4))
            ->then
                ->string($this->testedInstance->getFileName())
                    ->isIdenticalTo(implode(DIRECTORY_SEPARATOR, [
                        $base,
                        'src',
                        'cobertura',
                        'reflection',
                        'klass.php',
                    ]))

                ->string($this->testedInstance->getFileName($base))
                    ->isIdenticalTo(implode(DIRECTORY_SEPARATOR, [
                        'src',
                        'cobertura',
                        'reflection',
                        'klass.php',
                    ]))
        ;
    }
}
