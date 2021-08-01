<?php declare(strict_types=1);

namespace atoum\atoum\reports\cobertura\tests\units\reflection;

use atoum\atoum;
use atoum\atoum\reports\cobertura;
use atoum\atoum\reports\cobertura\reflection\method as testedClass;
use ReflectionClass;
use ReflectionMethod;

class method extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ReflectionMethod::class)
        ;
    }

    public function testGetCurrentClass()
    {
        $this
            ->if($this->newTestedInstance(testedClass::class, 'getDeclaringClass'))
            ->then
                ->object($this->testedInstance->getCurrentClass())
                    ->isInstanceOf(cobertura\reflection\klass::class)

                ->string($this->testedInstance->getCurrentClass()->getName())
                    ->isIdenticalTo(testedClass::class)

                ->object($this->testedInstance->getDeclaringClass())
                    ->isInstanceOf(ReflectionClass::class)

                ->string($this->testedInstance->getDeclaringClass()->getName())
                    ->isIdenticalTo(ReflectionMethod::class)

            ->if($this->newTestedInstance($this->testedInstance, 'getDeclaringClass'))
            ->then
                ->object($this->testedInstance->getCurrentClass())
                    ->isInstanceOf(cobertura\reflection\klass::class)

                ->string($this->testedInstance->getCurrentClass()->getName())
                    ->isIdenticalTo(testedClass::class)

                ->object($this->testedInstance->getDeclaringClass())
                    ->isInstanceOf(ReflectionClass::class)

                ->string($this->testedInstance->getDeclaringClass()->getName())
                    ->isIdenticalTo(ReflectionMethod::class)

            ->if($this->newTestedInstance(testedClass::class . '::getDeclaringClass'))
            ->then
                ->object($this->testedInstance->getCurrentClass())
                    ->isInstanceOf(cobertura\reflection\klass::class)

                ->string($this->testedInstance->getCurrentClass()->getName())
                    ->isIdenticalTo(testedClass::class)

                ->object($this->testedInstance->getDeclaringClass())
                    ->isInstanceOf(ReflectionClass::class)

                ->string($this->testedInstance->getDeclaringClass()->getName())
                    ->isIdenticalTo(ReflectionMethod::class)
        ;
    }

    public function testGetFullName()
    {
        $this
            ->if($this->newTestedInstance(testedClass::class, 'getFullName'))
            ->then
                ->string($this->testedInstance->getFullName())
                    ->isIdenticalTo(testedClass::class . '::getFullName')
        ;
    }

    public function testGetShortSignature()
    {
        // phpcs:disable
        $class = new class {
            public function method1() {}
            public function method2($a) {}
            public function method3($a, $b) {}
            public function method4($a, ...$b) {}
            public function method5(string $a, int $b) {}
            public function method6(string $a = '', int $b = 0, bool $c = false, bool $d = true) {}
            public function method7(array $a = [], $b = null) {}
            public function method8(&$a, &$b) {}
            public function method9(): string {}
            public function method10(self $a): self {}
            public function method11(\stdObject $a): string {}
            public function method12(cobertura\reflection\klass $a): ?\DateTime {}
            public function method13(cobertura\reflection\klass ...$a): cobertura\reflection\klass {}
            public function method14(&...$a) {}
            public function method15($a = \PHP_INT_MAX) {}
            public function method16(callable $name) {}
        };
        // phpcs:enable

        $this
            ->string($this->newTestedInstance($class, 'method1')->getShortSignature())
                ->isIdenticalTo('method1()')

            ->string($this->newTestedInstance($class, 'method2')->getShortSignature())
                ->isIdenticalTo('method2($a)')

            ->string($this->newTestedInstance($class, 'method3')->getShortSignature())
                ->isIdenticalTo('method3($a, $b)')

            ->string($this->newTestedInstance($class, 'method4')->getShortSignature())
                ->isIdenticalTo('method4($a, ...$b)')

            ->string($this->newTestedInstance($class, 'method5')->getShortSignature())
                ->isIdenticalTo('method5(string $a, int $b)')

            ->string($this->newTestedInstance($class, 'method6')->getShortSignature())
                ->isIdenticalTo('method6(string $a = \'\', int $b = 0, bool $c = false, bool $d = true)')

            ->string($this->newTestedInstance($class, 'method7')->getShortSignature())
                ->isIdenticalTo('method7(array $a = [], $b = null)')

            ->string($this->newTestedInstance($class, 'method8')->getShortSignature())
                ->isIdenticalTo('method8(&$a, &$b)')

            ->string($this->newTestedInstance($class, 'method9')->getShortSignature())
                ->isIdenticalTo('method9(): string')

            ->string($this->newTestedInstance($class, 'method10')->getShortSignature())
                ->isIdenticalTo('method10(self $a): self')

            ->string($this->newTestedInstance($class, 'method11')->getShortSignature())
                ->isIdenticalTo('method11(stdObject $a): string')

            ->string($this->newTestedInstance($class, 'method12')->getShortSignature())
                ->isIdenticalTo('method12(atoum\atoum\reports\cobertura\reflection\klass $a): ?DateTime')

            ->string($this->newTestedInstance($class, 'method13')->getShortSignature())
                ->isIdenticalTo(vsprintf('method13(%s ...$a): %s', [
                    cobertura\reflection\klass::class,
                    cobertura\reflection\klass::class,
                ]))

            ->string($this->newTestedInstance($class, 'method14')->getShortSignature())
                ->isIdenticalTo('method14(&...$a)')

            ->string($this->newTestedInstance($class, 'method15')->getShortSignature())
                ->isIdenticalTo('method15($a = PHP_INT_MAX)')

            ->string($this->newTestedInstance($class, 'method16')->getShortSignature())
                ->isIdenticalTo('method16(callable $name)')
        ;
    }

    public function testGetSignature()
    {
        // phpcs:disable
        $class = new class {
            public function method1() {}
            public function method2($a) {}
            public function method3($a, $b) {}
            public function method4($a, ...$b) {}
            public function method5(string $a, int $b) {}
            public function method6(string $a = '', int $b = 0, bool $c = false, bool $d = true) {}
            public function method7(array $a = [], $b = null) {}
            public function method8(&$a, &$b) {}
            public function method9(): string {}
            public function method10(self $a): self {}
            public function method11(\stdObject $a): string {}
            public function method12(cobertura\reflection\klass $a): ?\DateTime {}
            public function method13(cobertura\reflection\klass ...$a): cobertura\reflection\klass {}
            public function method14(&...$a) {}
            public function method15($a = \PHP_INT_MAX) {}
            public function method16(callable $name) {}
        };
        // phpcs:enable

        $this
            ->string($this->newTestedInstance($class, 'method1')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method1()')

            ->string($this->newTestedInstance($class, 'method2')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method2($a)')

            ->string($this->newTestedInstance($class, 'method3')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method3($a, $b)')

            ->string($this->newTestedInstance($class, 'method4')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method4($a, ...$b)')

            ->string($this->newTestedInstance($class, 'method5')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method5(string $a, int $b)')

            ->string($this->newTestedInstance($class, 'method6')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method6(string $a = \'\', int $b = 0, bool $c = false, bool $d = true)')

            ->string($this->newTestedInstance($class, 'method7')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method7(array $a = [], $b = null)')

            ->string($this->newTestedInstance($class, 'method8')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method8(&$a, &$b)')

            ->string($this->newTestedInstance($class, 'method9')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method9(): string')

            ->string($this->newTestedInstance($class, 'method10')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method10(self $a): self')

            ->string($this->newTestedInstance($class, 'method11')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method11(stdObject $a): string')

            ->string($this->newTestedInstance($class, 'method12')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method12(atoum\atoum\reports\cobertura\reflection\klass $a): ?DateTime')

            ->string($this->newTestedInstance($class, 'method13')->getSignature())
                ->startWith('class@anonymous')
                ->endWith(vsprintf('::method13(%s ...$a): %s', [
                    cobertura\reflection\klass::class,
                    cobertura\reflection\klass::class,
                ]))

            ->string($this->newTestedInstance($class, 'method14')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method14(&...$a)')

            ->string($this->newTestedInstance($class, 'method15')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method15($a = PHP_INT_MAX)')

            ->string($this->newTestedInstance($class, 'method16')->getSignature())
                ->startWith('class@anonymous')
                ->endWith('::method16(callable $name)')
        ;
    }
}
